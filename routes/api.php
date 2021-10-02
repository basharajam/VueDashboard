<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/',function(){
    return response()->json(['code'=>403,'message'=>'Not Authorized','status'=>false,'item'=>null],403);
})->name('ApiN');

Route::get('/Categories/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@getCategories']);

Route::get('/Products/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@getProds']);

Route::post('/getLayout',['uses'=>'App\Http\Controllers\CpanelController@getLayout','as'=>'getLayout']);

Route::get('/ProdByCat/{cat}/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@ProdByCat']);

route::get('/ProdOne/{id}/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@ProdOne']);

Route::get('/test',['uses'=>'App\Http\Controllers\api\ApiController@test']);

Route::get('validate/{type}/{value}',['uses'=>'App\Http\Controllers\api\ApiController@validateCreds']);

Route::post('/RegisterByMail',['uses'=>'App\Http\Controllers\api\UsersController@RegisterByMail']);

Route::get('/redirectF',['uses'=>'App\Http\Controllers\api\UsersController@redirectF']);

Route::get('/redirectG',['uses'=>'App\Http\Controllers\api\UsersController@redirectG']);

Route::get('/RegisterByFaceBook',['uses'=>'App\Http\Controllers\api\UsersController@RegisterByFaceBook']);

Route::get('/RegisterByGoogle',['uses'=>'App\Http\Controllers\api\UsersController@RegisterByGoogle']);

Route::post('/LoginByMail',['uses'=>'App\Http\Controllers\api\UsersController@LoginByMail']);

Route::get('/protected',function(){
    return 'Done';
})->middleware('auth:api');