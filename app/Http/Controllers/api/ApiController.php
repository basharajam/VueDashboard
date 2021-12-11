<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use App\Models\VueLayouts;
use App\Models\WpUser;
use App\Models\otp;
use App\Models\VueConfig;


use App\Facades;

use Auth;

class ApiController extends Controller
{

    public function getConfig()
    {
        $response=array();

        //get Social Links
        $facebook=Socialite::with('facebook')->stateless()->redirect()->getTargetUrl();
        $google=Socialite::with('google')->stateless()->redirect()->getTargetUrl();
        
        //get Currency Configs
        $CurrConfig=VueConfig::where('type','currency')->get();

        //get Shipment Configs
        $ShipConfig=VueConfig::where('type','shipment')->get();

        $response['facebook']=$facebook;
        $response['google']=$google;
        $response['Shipment']=$ShipConfig;
        $response['Currency']=$CurrConfig;

        return response()->json(['code'=>200,'message'=>'Login Links Successfully Generated','status'=>true,'item'=>$response],200); 
    }


    public function validateCreds($type,$value)
    {
        //validate params 
        if(!empty($type) && !empty($value)){

            //Check Type 
            if($type === 'mail'){

                //Check value
                $Check=WpUser::where('user_email',$value)->count();
            }
            elseif($type ==='username'){

                //check value
                $Check=WpUser::where('user_login',$value)->count();
            }
            elseif($type === 'number'){

                //check value 
                $Check=otp::where('mobileno',$value)->count();
            }

            if($Check > 0 ){
                return response()->json(['success'=>false,'message'=>'Exists'], 400);
            }
            else{
                return response()->json(['success'=>true,'message'=>'Done'], 200);
            }

        }
        else{
            return response()->json(['success'=>false,'message'=>'validate'], 400);
        }
    }


}
