<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

use Auth;

use App\Models\WpUser;

class UsersController extends Controller
{
    //


    public function RegisterByMail(Request $request)
    {


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

        //get JWT Token
        $token=Auth::guard('api')->tokenById($wp_user['id']);


        //response
        $response=array(
            'user'=>$wp_user,
            'token'=>$token
        );

        return response()->json(['code'=>201,'message'=>'User Successfully Created','status'=>true,'item'=>$response],201); 


    }

    public function redirectF()
    {
        return Socialite::with('facebook')->stateless()->redirect()->getTargetUrl();
    }

    public function redirectG()
    {
        return Socialite::with('google')->stateless()->redirect()->getTargetUrl();
    }

    public function RegisterByFaceBook()
    {
        //get User Data 
        $fb_user = Socialite::with('facebook')->stateless()->user();

        //validate Inputs 
        

        //Check Mail Is Unique 
        $CheckMail=WpUser::where('user_email',$fb_user->getEmail())->get();
        if(count($CheckMail) > 0 ){
            return response()->json(['code'=>400,'message'=>'Mail Already in Use','status'=>false,'item'=>null],400);
        } 

        //Check Username Is Unique 
        $CheckUserName=WpUser::where('user_login',strstr($fb_user->getEmail(), '@', true))->get();
        if(count($CheckUserName) > 0 ){
            return response()->json(['code'=>400,'message'=>'Username Already in Use','status'=>false,'item'=>null],400);
        } 

        // password
        $bcryptpass=0;

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

        //get Jwt Response
        $token=Auth::guard('api')->tokenById($wp_user['id']);

        //response
        $response=array(
            'user'=>$wp_user,
            'token'=>$token
        );

        return response()->json(['code'=>201,'message'=>'User Successfully Created','status'=>true,'item'=>$response],201); 
    }

    public function RegisterByGoogle()
    {
        //get User Data 
        $fb_user = Socialite::with('google')->stateless()->user();
        // $fb_user->getNickname();
        // $fb_user->getName();
        // $fb_user->getEmail();


        //validate Inputs 
        

        //Check Mail Is Unique 
        $CheckMail=WpUser::where('user_email',$fb_user->getEmail())->get();
        if(count($CheckMail) > 0 ){
            return response()->json(['code'=>400,'message'=>'Mail Already in Use','status'=>false,'item'=>null],400);
        } 

        //Check Username Is Unique 
        $CheckUserName=WpUser::where('user_login',strstr($fb_user->getEmail(), '@', true))->get();
        if(count($CheckUserName) > 0 ){
            return response()->json(['code'=>400,'message'=>'Username Already in Use','status'=>false,'item'=>null],400);
        } 

        // password
        $bcryptpass=0;

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

        //get Jwt Response
        $token=Auth::guard('api')->tokenById($wp_user['id']);

        //response
        $response=array(
            'user'=>$wp_user,
            'token'=>$token
        );

        return response()->json(['code'=>201,'message'=>'User Successfully Created','status'=>true,'item'=>$response],201); 
    }


    public function LoginByMail(Request $request)
    {
        //validate Inputs 
        
        //Check User
        if(!$token = Auth::guard('api')->attempt(
            array(
            'user_email'=>$request->input('mail'),
            'password'=>$request->input('pass')
            ))){
                return 'Baddd';
            }
            else{
                
              $response=array(
                'user'=>Auth::guard('api')->user(),
                'token'=>$token
              );

             return response()->json(['code'=>200,'message'=>'User Successfully Logged-In','status'=>true,'item'=>$response],200); 
            }
        //Done

    }


}
