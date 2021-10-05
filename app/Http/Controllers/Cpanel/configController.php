<?php

namespace App\Http\Controllers\Cpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\VueConfig;

class configController extends Controller
{
    //
    public function ShipmentConfig()
    {
        //get Shipment Configs
        $getConfig=VueConfig::where('type','shipment')->get();
        return view('Cpanel.configs.shipment',['Configs'=>$getConfig]);
    }

    public function CurrencyConfig()
    {
        //get Currency Configs 
        $getConfig=VueConfig::where('type','currency')->get();
        return view('Cpanel.configs.currency',['Configs'=>$getConfig]);
    }

    public function MainConfig()
    {
        //get Main Configs
        $getConfig=VueConfig::where('type','main')->get();
        return view('Cpanel.configs.main',['Configs'=>$getConfig]);
    }

    public function SaveConfig(Request $request)
    {
        //validate Inputs
        $validate = Validator::make(request()->all(), [
            'ConfigNameI'=>'required',
            'ConfigKeyI'=>'required',
            'ConfigValueI'=>'required',
            'ConfigTypeI'=>'required',
        ]);

        

        if ($validate->fails()) {
            return response()->json(['code'=>400,'message'=>'Validation Error','status'=>false,'item'=>null],400);
        }

        

        //Save Comfig
        $saveConfig=new VueConfig([
         'name'=>$request->input('ConfigNameI'),
         'key'=>$request->input('ConfigKeyI'),
         'value'=>$request->input('ConfigValueI'),
         'type'=>$request->input('ConfigTypeI'),
         'subValue'=>$request->input('ConfigSubValueI'),
         'status'=>1
        ]);

        $saveConfig->save();

        //response
        return 'Done';
    }

    public function UpdConfig(Request $request)
    {
        //validate Inputs 
        $validate=$request->validate([
          'ConfigNameUI'=>'required',
          'ConfigKeyUI'=>'required',
          'ConfigValueUI'=>'required',
          'ConfigIdUI'=>'required'
        ]);

        //Update Config
        $update=[
            'name'=>$validate['ConfigNameUI'],
            'key'=>$validate['ConfigKeyUI'],
            'value'=>$validate['ConfigValueUI'],
            'subValue'=>$request->input('ConfigSubValueUI'),
        ];

        $getConfig=VueConfig::where('id',$validate['ConfigIdUI'])->update($update);
        //$getConfig= VueConfig::where('name',$validate['ConfigIdUI'])->update($update);
        return $getConfig;
        if($getConfig >0){
            return response()->json(['success'=>true,'item'=>$validate],200);
        }
        else{
            return response()->json(['success'=>false,'item'=>null],403);
        }
    }

    public function getConfig(Request $request)
    {
        //validat input
        if(!empty($request->input('id'))){

            $getConfig=VueConfig::where('id',$request->input('id'))->first();

            return $getConfig;

        }
    }


}
