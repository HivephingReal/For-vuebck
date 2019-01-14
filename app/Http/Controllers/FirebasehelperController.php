<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FirebaseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Configuration;
use Kreait\Firebase\Firebase;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class FirebasehelperController extends Controller
{
    //
    public $body;
    public $title;
    public $token;
    public $status;
    public $date;

    public function __construct()
    {

    }

    public function sendnotimsg($body,$title,$token,$post_id)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['post_id' => $post_id,'user_token'=>$token]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();



        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
        if($downstreamResponse){

        }

    }

    public function store_firebase_data($ptitle,$user_id,$date,$project_id,$project_description,$status){
        $config = new Configuration();
        $config->setAuthConfigFile(asset('firebase_admin.json'));

        $firebase = new Firebase('https://hivephing-6589f.firebaseio.com', $config);

        $firebase->set(['title'=>$ptitle,'project_description'=>$project_description, 'status' =>$status,'date'=>$date], "'".$user_id.'/'.$project_id."'");

//        return dd($firebase->get("'".$user_id.'/'.$project_id."'"));
    }

}
