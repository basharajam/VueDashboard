<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CpanelController extends Controller
{
    //


    public function MainGet()
    {
        
        //
        return view('Cpanel.main');
        
    }

    public function LandigGet()
    {
        //
        return view('Cpanel.layouts.landing');
    }
}
