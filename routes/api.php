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

//Categories
Route::get('/Terms/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@getTerms']);

//


//Products
Route::get('/ProdByCat/{cat}/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@ProdByCat']);

route::get('/ProdOne/{id}/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@ProdOne']);

Route::get('/ProdByTag/{tag}/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@ProdByTag']);

Route::get('/Products/{cur}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@getProds']);

Route::get('/Search/{value}/{sort}/{limit}/{minprice}/{maxprice}/{filter}/{rate}/{curr}/{ship}',['uses'=>'App\Http\Controllers\api\ApiController@Search']);

//




//User
Route::get('validate/{type}/{value}',['uses'=>'App\Http\Controllers\api\ApiController@validateCreds']);

Route::post('/RegisterByMail',['uses'=>'App\Http\Controllers\api\UsersController@RegisterByMail']);

Route::post('/RegisterByMobile',['uses'=>'App\Http\Controllers\api\UsersController@RegisterByMobile']);

Route::post('/LoginByMail',['uses'=>'App\Http\Controllers\api\UsersController@LoginByMail']);

Route::get('/redirectF',['uses'=>'App\Http\Controllers\api\UsersController@redirectF']);

Route::get('/redirectG',['uses'=>'App\Http\Controllers\api\UsersController@redirectG']);

Route::get('/ValidateByFaceBook',['uses'=>'App\Http\Controllers\api\UsersController@ValidateByFaceBook']);

Route::get('/ValidateByGoogle',['uses'=>'App\Http\Controllers\api\UsersController@ValidateByGoogle']);

Route::get('/GetUser',['uses'=>'App\Http\Controllers\api\UsersController@GetUser'])->middleware('auth:api');

Route::post('/UpdateUser',['uses'=>'App\Http\Controllers\api\UsersController@UpdateUser'])->middleware('auth:api');

//


//Rates
Route::get('/getRate',['uses'=>'App\Http\Controllers\api\ApiController@getRate']);

Route::post('/SaveRate',['uses'=>'App\Http\Controllers\api\ApiController@SaveRate']);

//



//Orders
Route::post('/SaveOrderPaypal',['uses'=>'App\Http\Controllers\api\OrderController@SaveOrderPP'])->middleware('auth:api');

Route::post('/SaveOrderBcs',['uses'=>'App\Http\Controllers\api\OrderController@SaveOrderBcs'])->middleware('auth:api');

Route::get('/GetOrder/{status}',['uses'=>'App\Http\Controllers\api\OrderController@GetOrder'])->middleware('auth:api');

//


//Cpanel
Route::post('/getLayout',['uses'=>'App\Http\Controllers\CpanelController@getLayout','as'=>'getLayout']);

Route::get('/getConfig',['uses'=>'App\Http\Controllers\api\ApiController@getConfig']);

Route::post('/getConfig',['uses'=>'App\Http\Controllers\Cpanel\configController@getConfig','as'=>'getConfig']);

//

//Others
Route::get('/',function(){
    return response()->json(['code'=>403,'message'=>'Not Authorized','status'=>false,'item'=>null],403);
})->name('ApiN');

//