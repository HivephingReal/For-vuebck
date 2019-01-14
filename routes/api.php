<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('jwt.auth');
Route::post('/register','RegisterApiController@register');
Route::post('/dashboard',function (){
    return response()->json(['true']);
})->middleware('jwt.auth');
Route::post('/dashboard/addfr','AddController@addfr')->middleware('jwt.auth');

Route::get('/dashboard/{type}/proposals','GetController@get_proposals_for_repair');
Route::get('/dashboard/proposals/detail/{id}','GetController@get_proposal_detail');
Route::get('/dashboard/comdetail/{cid}','GetController@get_com_detail');
Route::get('/dashboard/comdetail_withoutauth/{cid}','GetController@comdetail_withoutauth');
Route::post('/dashboard/send_msg','AddController@send_msg');
Route::post('/dashboard/addrate','AddController@rate_this');
Route::post('/dashboard/add_paint_price','AddController@add_paint_price');
Route::post('/dashboard/getactivity','GetController@getactivity');
Route::get('/user_data','GetController@user_detaail');
Route::post('/user_data/edit','ProfileController@edit_info');
Route::post('/dashboard/contact','AddController@contact');
Route::post('/test',function(){
    return response()->json('ddd');
});
Route::get('/get_cities/{state_id}','GetController@get_cities');


Route::post('/dashboard/confirm_project','AddController@confirm_project');
Route::get('/get_token/company_detail/{cid}','GetController@get_com_detail');


Route::get('/get_work/{first}/{second}','GetController@get_frame');
Route::get('/get_states','GetController@get_states');
Route::get('/get_token/{user_id}','GetController@get_token');

Route::post('/dashboard/proposals/close_project','GetController@close_project');
Route::post('/dashboard/proposals/open_project','GetController@open_project');
Route::get('/rr/{file_name}',function($id){
    $file= public_path(). "/user_attachments/".$id;

    $headers = array(
        'Content-Type: application/pdf',
    );

    return Response::download($file, $id, $headers);
});
//for calculate price
Route::get('/testc',function()
{
    return view('calculatePrices');
});
Route::post('/test2','CalController@simple');
Route::post('/dashboard/addinvite','AddController@addinvite');
//end calculate price

