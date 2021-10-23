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
    

    //Layout
    route::get('landingPage',['uses'=>'App\Http\Controllers\CpanelController@LandingGet','as'=>'landingLayoutGet']);

    route::get('ProdOnePage',['uses'=>'App\Http\Controllers\CpanelController@ProdOneGet','as'=>'ProdOneLayoutGet']);

    route::get('ProdByCat',['uses'=>'App\Http\Controllers\CpanelController@ProdByCatGet','as'=>'ProdByCat']);

    route::get('AllCatPage',['uses'=>'App\Http\Controllers\CpanelController@AllCatGet','as'=>'AllCatLayoutGet']);

    route::get('ProdByTag',['uses'=>'App\Http\Controllers\CpanelController@ProdByTagGet','as'=>'ProdByTag']);

    route::post('updateSectionLanding',['uses'=>'App\Http\Controllers\CpanelController@updateSectionLanding','as'=>'updateSectionLanding']);

    route::post('updateSort',['uses'=>'App\Http\Controllers\CpanelController@updateSort','as'=>'updateSort']);

    route::post('SaveComp',['uses'=>'App\Http\Controllers\CpanelController@SaveComp','as'=>'SaveComp']);

    route::post('DelComp',['uses'=>'App\Http\Controllers\CpanelController@DelComp','as'=>'DelComp']);

    
    //Configs
    route::get('Shipment',['uses'=>'App\Http\Controllers\Cpanel\configController@ShipmentConfig','as'=>'ShipmentConfig']);

    route::get('Currency',['uses'=>'App\Http\Controllers\Cpanel\configController@CurrencyConfig','as'=>'CurrencyConfig']);
    
    route::get('main',['uses'=>'App\Http\Controllers\Cpanel\configController@MainConfig','as'=>'MainConfig']);

    route::post('SaveConfig',['uses'=>'App\Http\Controllers\Cpanel\configController@SaveConfig','as'=>'SaveConfig']);

    route::post('UpdConfig',['uses'=>'App\Http\Controllers\Cpanel\configController@UpdConfig','as'=>'UpdConfig']);


   });
});