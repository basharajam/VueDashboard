<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::prefix('Cpanel')->group(function () {
    

   Route::get('/',['uses'=>'App\Http\Controllers\CpanelController@MainGet']); 

   Route::prefix('layouts')->group(function () {
    
    route::get('landingPage',['uses'=>'App\Http\Controllers\CpanelController@LandigGet','as'=>'landingLayoutGet']);

   });
});