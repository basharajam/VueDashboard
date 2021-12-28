<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use App\Facades;

use Auth;

use App\Models\WpUser;

class UsersController extends Controller
{
    //
    public function RegisterByMail(Request $request)
    {
        //return $request->all();
        //validate Inputs 
        $validate = Validator::make(request()->all(), [
            'FirstNameI'=>'required',
            'LastNameI'=>'required',
            'UserNameI'=>"required|min:8",
            'PassI'=>'required|min:8',
            'Pass2I'=>'required|min:8',
            'MailI'=>'required|email',
        ]);

        if ($validate->fails()) {
          return response()->json(['code'=>400,'message'=>'Validation Error','status'=>false,'item'=>null],400);
        }

        //Check Mail Is Unique 
        $CheckMail=WpUser::where('user_email',$request->input('MailI'))->get();
        if(count($CheckMail) > 0 ){
            return response()->json(['code'=>400,'message'=>'Mail Already in Use','status'=>false,'item'=>null],400);
        } 

        //Check Username Is Unique 
        $CheckUserName=WpUser::where('user_login',$request->input('UserNameI'))->get();
        if(count($CheckUserName) > 0 ){
            return response()->json(['code'=>400,'message'=>'Username Already in Use','status'=>false,'item'=>null],400);
        } 

        //Check Passwords Are Matches
        $Pass1=$request->input('PassI');
        $Pass2=$request->input('Pass2I');
        if($Pass1 != $Pass2){
            return response()->json(['code'=>400,'message'=>'Passwords Not  matches','status'=>false,'item'=>null],400);
        }

        //Bcrypt password
        $bcryptpass=bcrypt($Pass1);

        //Save User 
        $wp_user = WpUser::create([
            "user_login" =>$request->input('UserNameI'),
            "user_pass"=>$bcryptpass ,
            "user_nicename" =>$request->input('FirstNameI'),
            "user_email"=>$request->input('MailI'),
            "user_url" =>'',
            "user_registered" =>Carbon::now(),
            "display_name" =>$request->input('FirstNameI') . ' '. $request->input('LastNameI'),
        ]);

        //$user=Auth::guard('api')->user();

        //Save Meta 
        Facades::saveMeta([
         ['key'=>'nickname','value'=>$request->input('FirstNameI')],
         ['key'=>'first_name','value'=>$request->input('FirstNameI')],
         ['key'=>'last_name','value'=>$request->input('LastNameI')],
         ['key'=>'description','value'=>'  '],
         ['key'=>'billing_first_name','value'=>$request->input('FirstNameI')],
         ['key'=>'billing_last_name','value'=>$request->input('LastNameI')],
        ],'user',$wp_user['ID']);

        //get JWT Token
        $token=Auth::guard('api')->tokenById($wp_user['id']);


        //response
        $response=array(
            'user'=>$wp_user,
            'token'=>$token
        );

        return response()->json(['code'=>201,'message'=>'User Successfully Created','status'=>true,'item'=>$response],201); 


    }


    public function RegisterByMobile(Request $request)
    {
        //
        //validate Inputs 
        $validate = Validator::make(request()->all(), [
            'FirstNameI'=>'required',
            'LastNameI'=>'required',
            'UserNameI'=>"required|min:8",
            'PassI'=>'required|min:8',
            'Pass2I'=>'required|min:8',
            'PhoneI'=>'required|email',
        ]);

        if ($validate->fails()) {
          return response()->json(['code'=>400,'message'=>'Validation Error','status'=>false,'item'=>null],400);
        }

        //Check Passwords Are Matches
        $Pass1=$request->input('PassI');
        $Pass2=$request->input('Pass2I');
        if($Pass1 != $Pass2){
            return response()->json(['code'=>400,'message'=>'Passwords Not  matches','status'=>false,'item'=>null],400);
        }

        //Bcrypt password
        $bcryptpass=bcrypt($Pass1);

        //Save User 
        $wp_user = WpUser::create([
            "user_login" =>$request->input('UserNameI'),
            "user_pass"=>$bcryptpass ,
            "user_nicename" =>$request->input('FirstNameI'),
            "user_email"=>$request->input('MailI'),
            "user_url" =>'',
            "user_registered" =>Carbon::now(),
            "display_name" =>$request->input('FirstNameI') . ' '. $request->input('LastNameI'),
        ]);


    }


    public function redirectF()
    {
        return Socialite::with('facebook')->stateless()->redirect()->getTargetUrl();
    }

    public function redirectG()
    {
        return Socialite::with('google')->stateless()->redirect()->getTargetUrl();
    }
    public function ValidateByFaceBook()
    {
        //get User Data 
        $fb_user = Socialite::with('facebook')->stateless()->user();

        //Check Mail Is Unique 
        $CheckMail=WpUser::where('user_email',$fb_user->getEmail())->get();

        //Check Username Is Unique 
        $CheckUserName=WpUser::where('user_login',strstr($fb_user->getEmail(), '@', true))->get();

        //If User Exists Auth With It
        if(count($CheckMail) > 0 && count($CheckUserName) > 0){
            $token=Auth::guard('api')->tokenById($CheckMail[0]['ID']);
            $response=array(
                'user'=>$CheckMail[0],
                'token'=>$token
            );
            return view('others.socialiteCallback',['user'=>$CheckMail[0],'token'=>$token]);
        }

        // password
        $bcryptpass=bcrypt(md5(uniqid(rand(), true)));

        //Save User 
        $wp_user = WpUser::create([
            "user_login" =>strstr($fb_user->getEmail(), '@', true),
            "user_pass"=>$bcryptpass ,
            "user_nicename" =>strstr($fb_user->getEmail(), '@', true),
            "user_email"=>$fb_user->getEmail(),
            "user_url" =>'',
            "user_registered" =>Carbon::now(),
            "display_name" =>$fb_user->getName(),
        ]);

        //Save User Meta

        //get Jwt Response
        $token=Auth::guard('api')->tokenById($wp_user['id']);

        //response
        $response=array(
            'user'=>$wp_user,
            'token'=>$token
        );
        return view('others.socialiteCallback',['user'=>$wp_user,'token'=>$token]);
        //return view('socialiteCallback',$response);

        //return response()->json(['code'=>201,'message'=>'User Successfully Created','status'=>true,'item'=>$response],201); 
    }

    public function ValidateByGoogle()
    {
        //get User Data 
        $fb_user = Socialite::with('google')->stateless()->user();

        //Check Mail Is Unique 
        $CheckMail=WpUser::where('user_email',$fb_user->getEmail())->get();

        //Check Username Is Unique 
        $CheckUserName=WpUser::where('user_login',strstr($fb_user->getEmail(), '@', true))->get();

        //If User Exists Auth With It
        if(count($CheckMail) > 0 && count($CheckUserName) > 0){
            $token=Auth::guard('api')->tokenById($CheckMail[0]['ID']);
            $response=array(
                'user'=>$CheckMail[0],
                'token'=>$token
            );
            return view('others.socialiteCallback',['user'=>$CheckMail[0],'token'=>$token]);
        }

        // password
        $bcryptpass=bcrypt(md5(uniqid(rand(), true)));

        //Save User 
        $wp_user = WpUser::create([
            "user_login" =>strstr($fb_user->getEmail(), '@', true),
            "user_pass"=>$bcryptpass ,
            "user_nicename" =>strstr($fb_user->getEmail(), '@', true),
            "user_email"=>$fb_user->getEmail(),
            "user_url" =>'',
            "user_registered" =>Carbon::now(),
            "display_name" =>$fb_user->getName(),
        ]);


        //Save User Meta

        //get Jwt Response
        $token=Auth::guard('api')->tokenById($wp_user['id']);

        //response
        $response=array(
            'user'=>$wp_user,
            'token'=>$token
        );

        return view('others.socialiteCallback',['user'=>$wp_user,'token'=>$token]);

        return response()->json(['code'=>201,'message'=>'User Successfully Created','status'=>true,'item'=>$response],201); 
    }


    public function LoginByMail(Request $request)
    {
        //validate Inputs 
        $validate = Validator::make(request()->all(), [
            'userMail'=>'required|email',
            'password'=>"required|min:8",
        ]);

        if ($validate->fails()) {
            return response()->json(['code'=>400,'message'=>'Validation Error','status'=>false,'items'=>null],400);
        }


        //Check User
        if(!$token = Auth::guard('api')->attempt(
            array(
              'user_email'=>$request->input('userMail'),
              'password'=>$request->input('password')
        ))){
             return response()->json(['code'=>400,'message'=>'Email Or Password Wrong','status'=>false,'items'=>null],400); 
            }
        else{

             //get user
             $user=Auth::guard('api')->user();

             $user['description']=Facades::getMeta('user','description',$user->ID);
             $user['billing_first_name']=Facades::getMeta('user','billing_first_name',$user->ID);
             $user['billing_last_name']=Facades::getMeta('user','billing_last_name',$user->ID);
             $user['billing_address_1']=Facades::getMeta('user','billing_address_1',$user->ID);
             $user['billing_address_2']=Facades::getMeta('user','billing_address_2',$user->ID);
             $user['shipping_first_name']=Facades::getMeta('user','shipping_first_name',$user->ID);
             $user['shipping_last_name']=Facades::getMeta('user','shipping_last_name',$user->ID);
             $user['shipping_address_1']=Facades::getMeta('user','shipping_address_1',$user->ID);
             $user['shipping_address_2']=Facades::getMeta('user','shipping_address_2',$user->ID);
             $user['shipping_city']=Facades::getMeta('user','shipping_city',$user->ID);
             $user['shipping']=Facades::getMeta('user','shipping',$user->ID);
                
              $response=array(
                'user'=>$user,
                'token'=>$token
              );


             return response()->json(['code'=>200,'message'=>'User Successfully Logged-In','status'=>true,'items'=>$response],200); 
       
        }
        //Done

    }



    public function GetUser()
    {
        //get User
        $user=Auth::guard('api')->user();

        //
        if(!empty($user)){

            $user['description']=Facades::getMeta('user','description',$user->ID);
            $user['billing_first_name']=Facades::getMeta('user','billing_first_name',$user->ID);
            $user['billing_last_name']=Facades::getMeta('user','billing_last_name',$user->ID);
            $user['billing_address_1']=Facades::getMeta('user','billing_address_1',$user->ID);
            $user['billing_address_2']=Facades::getMeta('user','billing_address_2',$user->ID);
            $user['shipping_first_name']=Facades::getMeta('user','shipping_first_name',$user->ID);
            $user['shipping_last_name']=Facades::getMeta('user','shipping_last_name',$user->ID);
            $user['shipping_address_1']=Facades::getMeta('user','shipping_address_1',$user->ID);
            $user['shipping_address_2']=Facades::getMeta('user','shipping_address_2',$user->ID);
            $user['shipping_city']=Facades::getMeta('user','shipping_city',$user->ID);
            $user['shipping']=Facades::getMeta('user','shipping',$user->ID);
            return response()->json(['code'=>200,'message'=>'User Informations','status'=>true,'items'=>['user'=>$user]], 200);
        
        }
    }

    public function UpdateUser(Request $request)
    {

        //get user
        $user=Auth::guard('api')->user();

        //first_name //
        //last_name //
        //description //
        ///billing_first_name //
        //billing_last_name //
        //billing_company
        //billing_address_1
        //billing_address_2
        //billing_city
        //billing_country
        //billing_email
        //billing_phone

        //shipping_first_name
        //shipping_last_name
        //shipping_company
        //shipping_address_1
        //shipping_address_2
        //shipping_city
        
        //Update User
        if($request->input('From') === 'Billing'){

            $arr=[
                ['key'=>'billing_first_name','value'=>$request->input('FirstNameI')],
                ['key'=>'billing_last_name','value'=>$request->input('LastNameI')],
                ['key'=>'billing_address_1','value'=>$request->input('BillingAddressI')],
                ['key'=>'billing_country','value'=>$request->input('BillingCountryI')],
                ['key'=>'billing_address_2','value'=>$request->input('BillingAddress2I')],
            ];

        }
        elseif($request->input('From') === 'Shipment'){

            $arr=[
                ['key'=>'shipping_first_name','value'=>$request->input('FirstNameI')],
                ['key'=>'shipping_last_name','value'=>$request->input('LastNameI')],
                ['key'=>'shipping_country','value'=>$request->input('ShipmentCountryI')],
                ['key'=>'shipping_address_1','value'=>$request->input('ShipmentAddressI')],
                ['key'=>'shipping_address_2','value'=>$request->input('ShipmentAddress2I')],
            ];

        }
        elseif($request->input('From') === 'User'){

            $arr=[
                ['key'=>'first_name','value'=>$request->input('FirstNameI')],
                ['key'=>'last_name','value'=>$request->input('LastNameI')],       
            ];

        }
    
        
        Facades::saveMeta($arr,'user',$user->ID);
        //get user
        $u=WpUser::where('ID',$user->ID)->first();
        $u['description']=Facades::getMeta('user','description',$user->ID);
        $u['billing_first_name']=Facades::getMeta('user','billing_first_name',$user->ID);
        $u['billing_last_name']=Facades::getMeta('user','billing_last_name',$user->ID);
        $u['billing_address_1']=Facades::getMeta('user','billing_address_1',$user->ID);
        $u['billing_address_2']=Facades::getMeta('user','billing_address_2',$user->ID);
        $u['shipping_first_name']=Facades::getMeta('user','shipping_first_name',$user->ID);
        $u['shipping_last_name']=Facades::getMeta('user','shipping_last_name',$user->ID);
        $u['shipping_address_1']=Facades::getMeta('user','shipping_address_1',$user->ID);
        $u['shipping_address_2']=Facades::getMeta('user','shipping_address_2',$user->ID);
        $u['shipping_city']=Facades::getMeta('user','shipping_city',$user->ID);
        $u['shipping']=Facades::getMeta('user','shipping',$user->ID);
        

        return response()->json(['code'=>200,'message'=>'User Informations updated','status'=>true,'items'=>['user'=>$u]], 200);
        # code...

    }


}
