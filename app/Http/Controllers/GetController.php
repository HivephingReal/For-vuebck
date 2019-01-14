<?php

namespace App\Http\Controllers;

use App\Repair;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;


class GetController extends FirebasehelperController
{
    //
    public function __construct()
    {
    }

    public function user_detail()
    {
        $data = User::where('id', Auth::user()->id)->first();
        return response()->json(['data' => $data]);

    }

    public function get_token($user_id)
    {
        $user = User::where('id', $user_id)->first();
        $token = JWTAuth::fromUser($user);
        return response()->json(['token' => $token]);
    }

    public function close_project(Request $request)
    {
        $data = DB::table('for_repair')->where([['user_id', '=', Auth::user()->id], ['id', '=', $request->id]])->update(['close' => 1]);
        return response()->json(['data' => 'closed']);
    }

    public function open_project(Request $request)
    {
        $data = DB::table('for_repair')->where([['user_id', '=', Auth::user()->id], ['id', '=', $request->id]])->update(['close' => 0]);
        return response()->json(['data' => 'open']);
    }

    public function get_proposals_for_repair($type)
    {
        $user_id = Auth::user()->id;
        switch ($type) {
            case 'fr':
                $to_check_type = ['fr1', 'fr2', 'fr3', 'fr4', 'fr5', 'fr6', 'fr7', 'fr8', 'fr9', 'fr10'];
                break;
            case 'fn':
                $to_check_type = ['fn1', 'fn2', 'fn3', 'fn4'];
                break;
            case 'rb':
                $to_check_type = ['rb1', 'rb2', 'rb3', 'rb4', 'rb5'];
                break;
            case 'b':
                $to_check_type = ['b1', 'b2', 'b3'];
                break;


        }
        $rd = Repair::where('user_id', $user_id)->whereIn('fr_type', $to_check_type);
        if ($rd->count() > 0) {
            foreach ($rd->orderBy('created_at', 'desc')->get() as $r) {
                $get_att1 = DB::table('attachment')->where([['project_id', '=', $r->id], ['position', '=', 1]]);
                if ($get_att1->count() != 0) {
                    $r->att1 = $get_att1->first()->file_name;
                } else {
                    $r->att1 = '';

                }
                $get_att2 = DB::table('attachment')->where([['project_id', '=', $r->id], ['position', '=', 2]]);
                if ($get_att2->count() != 0) {
                    $r->att2 = $get_att2->first()->file_name;
                } else {
                    $r->att2 = '';

                }
                $get_att3 = DB::table('attachment')->where([['project_id', '=', $r->id], ['position', '=', 3]]);
                if ($get_att3->count() != 0) {
                    $r->att3 = $get_att3->first()->file_name;
                } else {
                    $r->att3 = '';

                }

                switch ($r->fr_type) {
                    case 'fr1':
                        $r->dd = 'ေရလုိင္း';
                        $r->eng = 'water and pipeline services';

                        break;
                    case 'fr2':
                        $r->dd = 'မီးလိုင္း';
                        $r->eng = 'M & E sevice';
                        break;

                    case 'fr3':
                        $r->dd = 'လၽွပ္စစ္သြယ္တန္းျခင္း';
                        $r->eng = 'M & E sevice';

                        break;

                    case 'fr4':
                        $r->dd = 'Air-con တပ္ဆင္ျခင္း';
                        $r->eng = 'Air-Conditional service';

                        break;

                    case 'fr5':
                        $r->dd = 'အလူမီနီယံလုပ္ငန္း';
                        $r->eng = 'Aluminum & decoration service';

                        break;

                    case 'fr6':
                        $r->dd = 'ေဆးသုတ္မည္';
                        $r->eng = 'painting service';

                        break;

                    case 'fr7':
                        $r->dd = 'ၾကမ္းခင္း၊ ပါေကးခင္းမည္';
                        $r->eng = 'floor tiles service';

                        break;

                    case 'fr8':
                        $r->dd = 'CCTV ႏွင့္ လံုျခံဳေရးပစၥည္းမ်ား တပ္ဆင္မည္';
                        $r->eng = 'CCTV and security solution service';

                        break;

                    case 'fr9':
                        $r->dd = 'အေဆာက္အဦး ေဆာက္လုပ္မည္';
                        $r->eng = 'Building ';

                }
                switch ($r->fr_type) {

                    case 'fn1':
                        $r->dd = 'အိမ္ခန္းအတြင္း အလွဆင္မည္';
                        $r->eng = 'Interior design and decoration';

                        break;
                    case 'fn2':
                        $r->dd = 'ဆိုင္ခန္းအတြင္း အလွဆင္မည္';
                        $r->eng = 'Interior design and decoration';

                        break;

                    case 'fn3':
                        $r->dd = 'လၽွပ္စစ္သြယ္တန္းျခင္း';
                        $r->eng = 'M & E sevice';

                        break;

                    case 'fn4':
                        $r->dd = 'အေဆာက္အဦးအတြင္း အလွဆင္မည္';
                        $r->eng = 'Interior design and decoration';

                        break;

                    case 'fn5':
                        $r->dd = 'Shopping Mall အတြင္း အလွဆင္မည္';
                        $r->eng = 'Interior design and decoration(Shopping Mall)';

                        break;
                }
                switch ($r->fr_type) {

                    case 'b1':
                        $r->dd = 'Building';
                        $r->eng = 'Building';

                        break;
                    case 'b2':
                        $r->dd = 'Road ';
                        $r->eng = 'Road';

                        break;

                    case 'b3':
                        $r->dd = 'Tat tar';
                        $r->eng = 'Bridge';

                        break;


                }

                switch ($r->fr_type) {
                    case 'rb1':
                        $r->dd = 'အေဆာက္အဦးအား ျပန္လည္တည္ေဆာက္မည္';
                        $r->eng = 'Renovation';

                        break;
                    case 'rb2':
                        $r->dd = 'ေရလုိင္း';
                        $r->eng = 'water and pipeline services';

                        break;
                    case 'rb3':
                        $r->dd = 'လၽွပ္စစ္သြယ္တန္းျခင္း';
                        $r->eng = 'M & E sevice';
                        break;
                    case 'rb4':
                        $r->dd = 'Air-con တပ္ဆင္ျခင္း';
                        $r->eng = 'Air-Conditional service';

                        break;
                    case 'rb5':
                        $r->dd = 'CCTV ႏွင့္ လံုျခံဳေရးပစၥည္းမ်ား တပ္ဆင္မည္';
                        $r->eng = 'CCTV and security solution service';

                        break;
                }
                $data[] = $r;

            }
        } else {
            $data = '';
        }
        return response()->json(['success' => 'true', 'data' => $data]);
    }

    public function get_cities($state_id)
    {
        $cities = DB::connection('mysql_admin')->table('cities')->where('state_id', $state_id)->get();
        return response()->json(['cities' => $cities]);

    }

    public function get_com_detail($cid)
    {
        $com_data = DB::connection('mysql_admin')->table('company')->where('id', $cid)->first();
        $name = DB::connection('mysql_admin')->table('cities')->where('id', $com_data->city_id)->first();
        $rate = DB::connection('mysql_admin')->table('rating')->where('com_id', $cid)->count();
        $rate_sign = DB::connection('mysql_admin')->table('rating')->where([['com_id', '=', $cid], ['from_user', '=', Auth::user()->id]])->count();

        $com_data->city_name = $name->name;
        $com_data->rate = $rate;
        if ($rate_sign == 0) {
            $rate_sign = 1;
        } else {
            $rate_sign = 0;
        }
        $com_data->rate_sign = $rate_sign;
        $port = DB::connection('mysql_admin')->table('portfolio')->where('com_id', $cid)->get();
        if (DB::connection('mysql_admin')->table('invite')->where([['company_id', '=', $cid], ['post_id', '=', $_GET['postid']]])->count() > 0) {
            $com_data->check = true;
        } else {
            $com_data->check = false;
        };
        if (DB::table('request')->where([['requester_id', '=', $com_data->user_id], ['post_id', '=', $_GET['postid']],['status','=','rq']]  )->count() > 0) {
            $com_data->confirm = false;
        } else {
            $com_data->confirm = true;
        };

        $ports = [];
        foreach ($port as $p) {
            $ports[] = $p;
        }
        return response()->json(['data' => $com_data, 'port' => $ports]);

    }

    public function comdetail_withoutauth($cid)
    {
        $com_data = DB::connection('mysql_admin')->table('company')->where('id', $cid)->first();
        $name = DB::connection('mysql_admin')->table('cities')->where('id', $com_data->city_id)->first();
        $rate = DB::connection('mysql_admin')->table('rating')->where('com_id', $cid)->count();



        $port = DB::connection('mysql_admin')->table('portfolio')->where('com_id', $cid)->get();
//        if (DB::connection('mysql_admin')->table('invite')->where([['company_id', '=', $cid], ['post_id', '=', $_GET['postid']]])->count() > 0) {
//            $com_data->check = true;
//
//        } else {
//            $com_data->check = false;
//
//        };
        $ports = [];
        foreach ($port as $p) {
            $ports[] = $p;
        }
        return response()->json(['data' => $com_data, 'port' => $ports]);

    }
    public function get_states()
    {
        $states = DB::connection('mysql_admin')->table('states')->where('country_id', 150)->get();
        return response()->json(['states' => $states]);

    }

    public function get_frame($first, $second)
    {
        $data = DB::connection('mysql')->table('third_work_type')->where([['first_id', '=', $first], ['second_id', '=', $second]])->get();
        return response()->json(['data' => $data]);

    }

    public function get_proposal_detail($id)
    {
        $user_id = Auth::user()->id;
        $data = DB::table('for_repair')->where([['id', '=', $id]])->first();
        $cname = DB::connection('mysql_admin')->table('cities')->where('id', $data->city)->first();
        $sname = DB::connection('mysql_admin')->table('states')->where('id', $data->state)->first();
        $data->cname = $cname->name;
        $data->sname = $sname->name;
        if ($data->quotation_type == 0) {
            $data->quotation_type = 1;
        } else {
            $data->quotation_type = 0;
        }
        $fmessage = DB::table('message')->where([['post_id', '=', $data->id], ['from_user', '=', 'user']]);
        if ($fmessage->count() == 0) {
            $fmsg[] = '';
        } else {
            foreach ($fmessage->get() as $fms) {
                $name = DB::connection('mysql_admin')->table('company')->where('id', $fms->com_id)->first();
                $fms->com_name = $name->name;
                $fmsg[] = $fms;
            }
        }

        $tmessage = DB::table('message')->where([['post_id', '=', $data->id], ['from_user', '=', 'com']]);
        if ($tmessage->count() == 0) {
            $tmsg[] = '';
        } else {
            foreach ($tmessage->get() as $tms) {
                $name = DB::connection('mysql_admin')->table('company')->where('id', $tms->com_id)->first();
                $tms->com_name = $name->name;
                $tmsg[] = $tms;
            }
        }
        $get_request_count = DB::table('request')->where([['post_id', '=', $id]]);
        $all_grc = [];
        foreach ($get_request_count->get() as $grc) {
            $grc->request_data = DB::connection('mysql_admin')->table('company')->where('user_id', $grc->requester_id)->first();
            $grc->request_data->post_id = $grc->post_id;
            $grc->request_data->user_id = $grc->requester_id;
            $grc->request_data->com_status = $grc->status;
            $all_grc[] = $grc->request_data;
        }
        $all_rec = [];
        $get_rec = DB::connection('mysql_admin')->table('company')->where([['business_hub', '=', '9'], ['status', '>', '2']])->get();
        foreach ($get_rec as $rec) {

            $check_ub = DB::connection('mysql_admin')->table('user_block')->where([['user_id', '=', $rec->user_id], ['circum', '=', 'unblock']])->count();
            if ($check_ub > 0) {
                $all_rec[] = $rec;
            }
        }
        return response()->json(['data' => $data, 'fmessage' => $fmsg, 'tmessage' => $tmsg, 'rq_count' => $get_request_count->count(), 'requested_com' => $all_grc, 'all_rec' => $all_rec]);
    }

    public function getactivity(Request $request)
    {
        $user_id = DB::connection('mysql_admin')->table('company')->where('id', $request->com_id)->first();
        $act = DB::connection('mysql_admin')->table('activities')->where('user_id', $user_id->user_id)->get();
        return response()->json(['data' => $act]);
    }
    public function get_portfolio(Request $request)
    {

    }

}