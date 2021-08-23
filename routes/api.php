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

Route::get('/Products/{cur}/{ship}',['uses'=>'App\Http\Controllers\ApiController@getProds']);

Route::get('/test',['uses'=>'App\Http\Controllers\ApiController@test']);

route::get('validate/{type}/{value}',['uses'=>'App\Http\Controllers\ApiController@validateCreds'])->middleware(['cors']);