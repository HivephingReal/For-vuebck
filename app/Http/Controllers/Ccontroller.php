<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class calculatePrices extends Controller
{
    public function test(Request $request)
    {

        $standard = $request->standard; //high middle normal
        $city_id = $request->city_id;
        $type_of_calculation = $request->type_of_calculation; #one_sqft #ten_sqft
        if($type_of_calculation == 'Ten square feet')
        {
            $calculationtype = 'ten_sq';
            $num_sqft = $request->txtShowBox1;
        }
        elseif ($type_of_calculation='One square feet')
        {
            $calculationtype = 'one_sq';
            $num_sqft = $request->txtShowBox2;
        }
        else #for_custom
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
         $testionInput = "yes";*/

        $companies = DB::table('company')
            ->where('city_id', '=', $city_id)
            ->where('business_hub', '=', 8)
            ->orderBy('created_at', 'desc')->get();

        echo "<h1> Company lists matching with your facts for painting</h1>";

        $i=0;
        foreach($companies as $company) {
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

                if($clearingInput == $clearingDB and $selarInput == $selarDB and $puttyInput == $puttyDB and $colorInput == $colorDB and $testionInput == $testionDB)
                {
                    $totalcost = 0; #clearing cost
                    if($sq_type == $calculationtype) #match with user's choice and company existing facts of sqft type
                    {
                        echo "ok with ".$sq_type."<br>";

                        $totalcost = $final->cost_for_paint_pk;
                    }
                    else
                    {
                        echo "need to change ".$sq_type."<br>";
                        if($calculationtype == 'one_sq') //$calculationtype is user's input for type of sqft(one sqft or kyin?)
                        {
                            $totalcost = $final->cost_for_paint_pk / 10;
                        }
                        else
                        {
                            $totalcost = $final->cost_for_paint_pk * 10;
                        }
                    }

                }
                else
                {
                    if($sq_type == $calculationtype)
                    {
                        $totalcost += $selarInput * $final->cost_ex_Selar;
                        $totalcost += $puttyInput * $final->cost_ex_Putty;
                        $totalcost += $colorInput * $final->cost_ex_Color;

                        if($clearingInput == 'yes')
                        {
                            $totalcost += $final->cost_ex_clear;
                        }
                        if($testionInput == 'yes')
                        {
                            $totalcost += $final->cost_ex_Testion;
                        }
                    }
                    else
                    {
                        if($calculationtype == 'one_sq') //$calculationtype is user's input for type of sqft(one sqft or kyin?)
                        {
                            $totalcost += $selarInput * $final->cost_ex_Selar / 10;
                            $totalcost += $puttyInput * $final->cost_ex_Putty / 10;
                            $totalcost += $colorInput * $final->cost_ex_Color / 10;

                            if ($clearingInput == 'yes') {
                                $totalcost += $final->cost_ex_clear / 10;
                            }
                            if ($testionInput == 'yes') {
                                $totalcost += $final->cost_ex_Testion / 10;
                            }
                        }
                        else
                        {
                            $totalcost += $selarInput * $final->cost_ex_Selar * 10;
                            $totalcost += $puttyInput * $final->cost_ex_Putty * 10;
                            $totalcost += $colorInput * $final->cost_ex_Color * 10;

                            if ($clearingInput == 'yes') {
                                $totalcost += $final->cost_ex_clear * 10;
                            }
                            if ($testionInput == 'yes') {
                                $totalcost += $final->cost_ex_Testion * 10;
                            }
                        }
                    }

                }

                echo $i."<br> Company ID: ".$com_id .
                    "<br>"."Company Name: ".$company->name.
                    "<br>"."Standard: ".$standard.
                    "<br>"."cost of ten square feet :".$totalcost.
                    "<br>"."Calculation type :".$calculationtype.
                    "<br>"."number of ten sqft :".$num_sqft.
                    "<br>"."total cost: ".$num_sqft * $totalcost." ks".
                    "<br>"."Final id:".$final->id.
                    "<br>"."Additional info Input:".$clearingInput."/ ".$selarInput.
                    "/ ".$puttyInput."/ ".$colorInput."/ ".$testionInput.
                    "<br>"."Additional info DB :".$clearingDB."/ ".$selarDB.
                    "/ ".$puttyDB."/ ".$colorDB."/ ".$testionDB;


                echo "<hr>";
            }

        }




    }
    public function index(Request $request)
    {

    return $request->all();


    }

}

