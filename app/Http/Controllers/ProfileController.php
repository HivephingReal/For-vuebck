<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }
    public function edit_info(Request $request){
        $validator=Validator::make($request->all(),['name'=>'required','phone' => 'required|numeric|digits_between:5,14']);
        if($validator->fails()){
            return response()->json(['success'=>'error','error'=>$validator->errors()]);
        }
        $update=User::where('id',Auth::user()->id)->update(['phone'=>$request->phone,'name'=>$request->name]);
        return response()->json(['success'=>'true','data'=>$request->all()]);
    }

}
