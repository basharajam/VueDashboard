<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Routing\Controller as BaseController;

use App\Models\User;
use Auth;


class Controller extends BaseController
{
    

    public function SaveUser(Request $request)
    {

        // userable_type	
        // ccc	
        // mobile	
        // mobile_verified_at	
        // username


        //save user
        $SaveUser=new User();
        $SaveUser->name='Blaxk';
        $SaveUser->email='blaxk@blaxk.cc';
        $SaveUser->password=bcrypt('a5522122');
        $SaveUser->userable_type='xxx';
        $SaveUser->userable_id=6546;
        $SaveUser->mobile=56456456464;
        $SaveUser->mobile_verified_at=Carbon::now();
        $SaveUser->username='xxxxxxxxxxx0';
        
        return $SaveUser->save();

    }

    public function login(Request $request)
    {
        //check user
        $Check=Auth::guard('test')->attempt(['email'=>'blaxk@blaxk.cc','password'=>'a5522122']);
        
        if($Check){
            return $Check;
        }

    }

    public function protect()
    {
        return 'done';
    }




}
