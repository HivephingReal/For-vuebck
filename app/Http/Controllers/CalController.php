<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CalController extends Controller
{
    public function test(Request $request)
    {

        $standard = $request->standard; //high middle normal
        $city_id = $request->city_id;
        $type_of_calculation = $request->type_of_calculation; #one_sqft #ten_sqft
        if ($type_of_calculation == 'Ten square feet') {
            $calculationtype = 'ten_sq';
            $num_sqft = $request->txtShowBox1;
        } elseif ($type_of_calculation = 'One square feet') {
            $calculationtype = 'one_sq';
            $num_sqft = $request->txtShowBox2;
        } else #for_custom
        {
            $calculationtype = 'custom';
            $num_sqft = $request->txtShowBox3;
        }

        #clear #selar #putty #color #teston #user's choices
        $clearingInput = $request->clear;
        $selarInput = $request->selar;
        $puttyInput = $request->putty;
        $colorInput = $request->color;
        $testionInput = $request->testion;
        #####default values######change with requests
        /* $clearingInput = "yes";
         $selarInput = 1;
         $puttyInput = 1;
         $colorInput = 1;
         $testionInput = "yes";
        echo $colorInput;*/

        $companies = DB::table('company')
            ->where('city_id', '=', $city_id)
            ->where('business_hub', '=', 8)
            ->orderBy('created_at', 'desc')->get();
        echo "<h1> Company lists matching with your facts for painting</h1>";
        $i = 0;
        foreach ($companies as $company) {
            $i++;
            $com_id = $company->id;

            $final = DB::connection('mysql_service')
                ->table('company_with_paint')
                ->where('com_id', $com_id)
                ->where('pctype', $standard)
                ->first(); #filter_by_standard( Normal Middle High )
            if (is_array($final) || is_object($final)) {
                //echo $final->sq."<br><br><br> ";
                $sq_type = $final->sq;
                $clearingDB = $final->clearing;
                $selarDB = $final->Selar;
                $puttyDB = $final->Putty;
                $colorDB = $final->Color;
                $testionDB = $final->Testion;
                $totalcost = $final->cost_for_paint_pk;

                if ($clearingInput == $clearingDB and $selarInput == $selarDB and $puttyInput == $puttyDB and $colorInput == $colorDB and $testionInput == $testionDB) {
                    if ($sq_type != $calculationtype) {
                        echo "need to change from " . $sq_type . "<br>";
                        if ($calculationtype == 'one_sq') //$calculationtype is user's input for type of sqft(one sqft or kyin?)
                        {
                            $totalcost = $totalcost / 10;
                        } else {
                            $totalcost = $totalcost * 10;
                        }
                    }

                } else {
                    #Subtract "worker fees" from total cost
                    $totalcost = $final->cost_for_paint_pk - ($final->cost_ex_clear + $final->cost_ex_Selar + $final->cost_ex_Putty + $final->cost_ex_Color + $final->cost_ex_Testion);

                    #clearing multiple times of cost
                    $selar_cost = $final->cost_ex_Selar / $selarDB;
                    $putty_cost = $final->cost_ex_Putty / $puttyDB;
                    $color_cost = $final->cost_ex_Color / $colorDB;

                    if ($sq_type == $calculationtype) {
                        if ($clearingInput == 'yes') {
                            $totalcost += $final->cost_ex_clear;
                        }
                        if ($testionInput == 'yes') {
                            $totalcost += $final->cost_ex_Testion;
                        }
                        $totalcost += $selarInput * $final->cost_ex_Selar;
                        $totalcost += $puttyInput * $final->cost_ex_Putty;
                        $totalcost += $colorInput * $final->cost_ex_Color;
                    } else {
                        if ($calculationtype == 'one_sq') //$calculationtype is user's input for type of sqft(one sqft or kyin?)
                        {

                            $totalcost = $totalcost / 10;
                            if ($clearingInput == 'yes') {
                                $totalcost += $final->cost_ex_clear / 10;
                            }
                            if ($testionInput == 'yes') {
                                $totalcost += $final->cost_ex_Testion / 10;
                            }
                            $totalcost += $selarInput * $final->cost_ex_Selar / 10;
                            $totalcost += $puttyInput * $final->cost_ex_Putty / 10;
                            $totalcost += $colorInput * $final->cost_ex_Color / 10;
                        } else {
                            $totalcost = $totalcost * 10;
                            if ($clearingInput == 'yes') {
                                $totalcost += $final->cost_ex_clear * 10;
                            }
                            if ($testionInput == 'yes') {
                                $totalcost += $final->cost_ex_Testion * 10;
                            }
                            $totalcost += $selarInput * $final->cost_ex_Selar * 10;
                            $totalcost += $puttyInput * $final->cost_ex_Putty * 10;
                            $totalcost += $colorInput * $final->cost_ex_Color * 10;
                        }
                    }

                }

                echo $i . "<br> Company ID: " . $com_id .
                    "<br>" . "Company Name: " . $company->name .
                    "<br>" . "Standard: " . $standard .
                    "<br>" . "cost of ten square feet :" . $totalcost .
                    "<br>" . "Calculation type :" . $calculationtype .
                    "<br>" . "number of ten sqft :" . $num_sqft .
                    "<br>" . "total cost: " . $num_sqft * $totalcost . " ks" .
                    "<br>" . "Final id:" . $final->id .
                    "<br>" . "Additional info Input:" . $clearingInput . "/ " . $selarInput .
                    "/ " . $puttyInput . "/ " . $colorInput . "/ " . $testionInput .
                    "<br>" . "Additional info DB :" . $clearingDB . "/ " . $selarDB .
                    "/ " . $puttyDB . "/ " . $colorDB . "/ " . $testionDB;


                echo "<hr>";
            }

        }


    }

    public function index(Request $request)
    {

        $companies = DB::connection('mysql_admin')->table('company')

            ->orderBy('created_at', 'desc')->get();
        //get all com by city_id and business hub


//        $standard = $request->standard; //high middle normal
//        $city_id = $request->city_id;
//        $type_of_calculation = $request->type_of_calculation; #one_sqft #ten_sqft
//        //get all companies by city and bh
//            $companies = DB::table('company')
//            ->where('city_id', '=', $city_id)
//            ->where('business_hub', '=', 8)
//            ->orderBy('created_at', 'desc')->first();
//
//        foreach($companies as $com){
//            $get_com_paint_price = DB::connection('mysql_service')
//                ->table('company_with_paint')
//                ->where('com_id', $com->id)
//                ->where('pctype', $standard)
//                ->where('clearing',$request->cleaning)
//                ->where('cost_ex_Selar',$request->Selar)
//                ->where('cost_ex_Putty',$request->Putty)
//                ->where('cost_ex_Color',$request->Color)
//                ->where('cost_ex_Testion',$request->Testion)
//                ->first();
//
//        }
        $list_price=[];
        foreach ($companies as $com) {
            $final = DB::table('company_with_paint')
                ->where('com_id', $com->id)
                ->where('pctype',$request->paint_class)
                ->first();
            //get com's paint price

            if (is_array($final) || is_object($final)) {
                //if not empty price
                $original_price = $final->cost_for_paint_pk;

                if ($final->Selar >= $request->Selar) {
                    //if company selar is larger than customer request selar
                    $get_selar_price = ($final->Selar - $request->Selar) * $final->cost_ex_Selar;
                    $original_price -= $get_selar_price;
                }
                else {
                    //else request selar is more than com
                    $get_selar_price = ($request->Selar - $final->Selar) * $final->cost_ex_Selar;
                    $original_price += $get_selar_price;
                }
                if ($final->Putty >= $request->Putty) {
                    //if company selar is larger than customer request selar
                    $get_selar_price = ($final->Putty - $request->Putty) * $final->cost_ex_Putty;
                    $original_price -= $get_selar_price;
                }
                else {
                    //else request selar is more than com
                    $get_selar_price = ($request->Putty - $final->Putty) * $final->cost_ex_Putty;
                    $original_price += $get_selar_price;
                }
                if ($final->Color >= $request->Color) {
                    //if company selar is larger than customer request selar
                    $get_selar_price = ($final->Color - $request->Color) * $final->cost_ex_Color;
                    $original_price -= $get_selar_price;
                }
                else {
                    //else request selar is more than com
                    $get_selar_price = ($request->Color - $final->Color) * $final->cost_ex_Color;
                    $original_price += $get_selar_price;
                }
                if ($final->Color >= $request->Color) {
                    //if company selar is larger than customer request selar
                    $get_selar_price = ($final->Color - $request->Color) * $final->cost_ex_Color;
                    $original_price -= $get_selar_price;
                }
                else {
                    //else request selar is more than com
                    $get_selar_price = ($request->Color - $final->Color) * $final->cost_ex_Color;
                    $original_price += $get_selar_price;
                }
                $list_price [$com->id]=$original_price;
            }
            else {
                continue;
            }
        }
        return $list_price;
    }
    public function simple(Request $request){
        $companies = DB::connection('mysql_admin')->table('company')
            ->where('city_id', '=', $request->city_id)
            ->orderBy('created_at', 'desc')->get();
        foreach($companies as $com) {
            $final = DB::table('company_with_paint')
                ->where('com_id', $com->id)
                ->where('pctype', $request->paint_class)
                ->where('main_type',$request->paint_place)
                ->where('second_type',$request->iob)
                ->first();
            if (is_array($final) || is_object($final)) {
                $original_price = $final->cost_for_paint_pk;
                if($request->one_t_sf==0) {
                    //if request for 1 sq ft
                    if ($final->sq == 'one_sq') {
                        //if com has only one sq
                        $result_price = $final->cost_for_paint_pk*$request->one_sf;
                    } else {
                        //if com has 10 sq ft
                        $result_price = ceil(($final->cost_for_paint_pk/10)*$request->one_sf);

                    }
                }
                else{
                    if ($final->sq == 'one_sq') {
                        $result_price =$final->cost_for_paint_pk*10*$request->one_t_sf;
                    } else {
                        $result_price =  $final->cost_for_paint_pk*$request->one_t_sf;
                    }

                }
                $com->result_price=$result_price;
                $com->Selar=$final->Selar;
                $com->Putty=$final->Putty;
                $com->Color=$final->Color;

                $re[]=$com;

            }
            }

        return response()->json(['success' => 'true', 'data' => $re]);

    }


}
