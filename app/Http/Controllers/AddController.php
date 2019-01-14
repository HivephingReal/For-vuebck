<?php

namespace App\Http\Controllers;

use App\Message;
use App\Repair;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddController extends FirebasehelperController
{
    //
    public function __construct()
    {
        $this->middleware('jwt.auth');

    }

    public function add_paint_price(Request $request)
    {
        DB::table('paint_project')->insert(['place' => $request->paint_place, 'ioroorb' => $request->iob, 'Testion' => $request->Testion, 'cleaning' => $request->cleaning, 'color' => $request->Color, 'Putty' => $request->Putty, 'selar' => $request->Selar, 'user_id' => Auth::user()->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        return response()->json($request);
    }
    public function addfr(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|min:1|max:100', 'quotation' => 'required|numeric|min:1|max:5', 'quotation_type' => 'required|numeric|min:0|max:5', 'address' => 'required|min:5|max:1000', 'phone' => 'required|numeric|digits_between:5,40', 'description' => 'required|min:50|max:10000','fr' => 'required|min:1|max:10']);
        if ($validator->fails()) {
            return response()->json(['success' => 'error', 'error' => $validator->errors()]);
        }
        $input = $request->except('token');
        $input['name'] = $request->name;
        $input['quotation'] = $request->quotation;
        $input['quotation_type'] = $request->quotation_type;
        $input['phone'] = $request->phone;
        $input['description'] = $request->description;
        $input['address'] = $request->address;
        $input['fr_type'] = $request->fr;
        $input['user_id'] = Auth::user()->id;
        $input['confirm'] = 'pending';
        $input['city'] = $request->city;
        $input['state'] = $request->state;
        $input['project_define_point'] = 0;
        $input['close'] = 0;
        if ($nowid = Repair::create($input)->id) {
            $rs_count = DB::connection('mysql_admin')->table('relation_user_post_and_op')->count();
            if ($rs_count == 0) {
                DB::connection('mysql_admin')->table('relation_user_post_and_op')->insert(['post_id' => $nowid, 'op_id' => 2, 'process' => 'pending', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            } else {
                $who_is_last = DB::connection('mysql_admin')->table('relation_user_post_and_op')->orderBy('id', 'desc')->first();
                $how_many = DB::connection('mysql_admin')->table('admin')->select('id')->where('role', 'op')->orderBy('id', 'desc');
                foreach ($how_many->get() as $hma) {
                    $arr_hom[] = $hma->id;
                }
                if ($who_is_last->op_id < $how_many->first()->id) {
                    $k = array_search($who_is_last->op_id, $arr_hom);
                    $op_id = $arr_hom[$k - 1];
                } else {
                    $op_id = array_last($arr_hom);
                }
                DB::connection('mysql_admin')->table('relation_user_post_and_op')->insert(['post_id' => $nowid, 'op_id' => $op_id, 'process' => 'pending', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            }
            if (!empty($request->file('file1'))) {
                $file_name1 = Carbon::now()->timestamp . $request->file('file1')->getClientOriginalName();
                if ($request->file('file1')->move(base_path() . '/public/user_attachments/', $file_name1)) {
                    DB::table('attachment')->insert(['user_id' => Auth::user()->id, 'project_id' => $nowid, 'position' => 1, 'file_name' => $file_name1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                }
            }
            if (!empty($request->file('file2'))) {
                $file_name2 = Carbon::now()->timestamp . $request->file('file2')->getClientOriginalName();

                if ($request->file('file2')->move(base_path() . '/public/user_attachments/', $file_name2)) {

                    DB::table('attachment')->insert(['user_id' => Auth::user()->id, 'project_id' => $nowid, 'position' => 2, 'file_name' => $file_name2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                }
            }
            if (!empty($request->file('file3'))) {

                $file_name3 = Carbon::now()->timestamp . $request->file('file3')->getClientOriginalName();

                if ($request->file('file3')->move(base_path() . '/public/user_attachments/', $file_name3)) {

                    DB::table('attachment')->insert(['user_id' => Auth::user()->id, 'project_id' => $nowid, 'position' => 3, 'file_name' => $file_name3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

                }
            }

            return response()->json(['success' => 'true']);
        }
    }

    public function send_msg(Request $request)
    {
        $validator = Validator::make($request->all(), ['message' => 'required|min:100|max:1500']);
        if ($validator->fails()) {
            return response()->json(['success' => 'error', 'error' => $validator->errors()]);
        }
        if ($request->post_id != null and $request->com_id != null) {
            $input = $request->except('token');
            $input['from_user'] = 'user';
            $input['post_id'] = $request->post_id;
            $input['com_id'] = $request->com_id;
            $input['user_id'] = Auth::user()->id;
            $input['message'] = $request->message;
            if (Message::create($input)) {
                return response()->json(['success' => 'success']);

            }
        } else {
            return response()->json(['success' => 'null_error']);
        }
        return response()->json(['success' => 'success']);
    }
    public function confirm_project(Request $request)
    {
        $get_user_free_plan = DB::connection('mysql_admin')->table('user_get_free_plan')->where('user_id', $request->user_id)->first();
        $get_project_data = DB::table('for_repair')->where('id', $request->post_id)->first();
        $com_data = DB::connection('mysql_admin')->table('company')->where('user_id', $request->user_id)->first();

        //get user's free plan
        if ($get_user_free_plan->end_date >= Carbon::now() and $get_user_free_plan->remaining_point > 0) {
            //user's free plan is not expire
            $total_free_and_bonus = $get_user_free_plan->remaining_point + $get_user_free_plan->increase_point;
            //sum remain point and increase point
            if ($get_user_free_plan->remaining_point >= $get_project_data->project_define_point) {
                //if rpoint is enough
                $new_remaining_point =$get_user_free_plan->remaining_point - $get_project_data->project_define_point;
                $new_increase_point = $get_user_free_plan->increase_point;

            } elseif ($total_free_and_bonus >= $get_project_data->project_define_point) {
                //if not rpoint is not enough but total total point is enough
                $new_remaining_point = 0;
                $new_increase_point =  $total_free_and_bonus - $get_project_data->project_define_point;
            } else {
                DB::table('request')->where([['post_id', '=', $request->post_id], ['requester_id', '=', $request->user_id]])->update(['status' => 'rq']);
                return response()->json(['data' => $request->post_id]);
            }
            //you will save to database for free plan
            $new_see_point = $get_user_free_plan->see_point + 1;

            DB::connection('mysql_admin')->table('user_get_free_plan')->where('user_id', $request->user_id)->update(['remaining_point' => $new_remaining_point, 'increase_point' => $new_increase_point, 'see_point' => $new_see_point]);


        } else {
            //if user's free plan is expire

            $get_user_plan = DB::connection('mysql_admin')->table('company_with_plan')->where('com_id', $com_data->id);
            //get user's plan data
            if ($get_user_plan->count() > 0) {
                //check user have plan
                if ($get_user_plan->first()->remaining_point >= $get_project_data->project_define_point) {
                    $new_plan_rem = $get_user_plan->first()->remaining_point - $get_project_data->project_define_point;
                    DB::connection('mysql_admin')->table('company_with_plan')->where('com_id', $com_data->id)->update(['remaining_point' => $new_plan_rem]);


                } else {
                    DB::table('request')->where([['post_id', '=', $request->post_id], ['requester_id', '=', $request->user_id]])->update(['status' => 'rq']);
                    return response()->json(['data' => $request->post_id,'success'=>'false']);
                }
            } else {
                //user didnt buy plan
                DB::table('request')->where([['post_id', '=', $request->post_id], ['requester_id', '=', $request->user_id]])->update(['status' => 'rq']);
                return response()->json(['data' => $request->post_id,'success'=>'false']);
            }


        }
        if(DB::table('request')->where([['post_id', '=', $request->post_id], ['requester_id', '=', $request->user_id]])->update(['status' => 'con'])){

        }
        return response()->json(['data' => $request->post_id,'success'=>'true']);
    }

    public function rate_this(Request $request)
    {
        $user_id = Auth::user()->id;
        $check_rate = DB::connection('mysql_admin')->table('rating')->where([['com_id', '=', $request->com_id], ['from_user', '=', $user_id]])->count();
        if ($check_rate > 0) {
            if (DB::connection('mysql_admin')->table('rating')->where([['com_id', '=', $request->com_id], ['from_user', '=', $user_id]])->delete()) {
                $new_rate = DB::connection('mysql_admin')->table('rating')->where([['com_id', '=', $request->com_id]])->count();
                return response()->json(['rate' => $new_rate, 'rate_sign' => 0]);
            }
        } else {
            DB::connection('mysql_admin')->table('rating')->insert(['com_id' => $request->com_id, 'from_user' => $user_id, 'rate' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            $new_rate = DB::connection('mysql_admin')->table('rating')->where([['com_id', '=', $request->com_id]])->count();
            return response()->json(['rate' => $new_rate, 'rate_sign' => 1]);
        }

    }

    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(), ['title' => 'required|min:5|max:1500', 'subscription' => 'required|min:5|max:1500']);
        if ($validator->fails()) {
            return response()->json(['success' => 'error', 'error' => $validator->errors()]);
        }
        DB::table('contact_message')->insert(['title' => $request->title, 'subscription' => $request->subscription, 'user_id' => Auth::user()->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        return response()->json(['success' => 'success']);

    }
    public function addinvite(Request $request)
    {
        $check=DB::connection('mysql_admin')->table('invite')->where([['post_id','=',$request->postid],['cb_user_id','=',Auth::user()->id],['company_id','=',$request->com_id]])->count();
        if($check == 0) {
            if (DB::connection('mysql_admin')->table('invite')->insert(['post_id' => $request->postid, 'cb_user_id' => Auth::user()->id, 'company_id' => $request->com_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()])) {
               FirebasehelperController::sendnotimsg($body='fff',$title='title',$token='fIArftzyKmk:APA91bFuuNO3YtNLhB7xJ60shvtHMO4Zl-n0BbyXTwlx6CE2SqG56q_GsLCvq_oeFtk5VD5NmXLymH2PBRbfIFk6HkQuPxquCEZAImXKRX5Rizbs7cC2biUGaXgZ6W8Kh9OBYX1nFI_R',$post_id='22');
                return response()->json(['postid' => $request->all()]);
            } else {
                return response()->json(['error' => 'error']);
            }
        }
    }
}
