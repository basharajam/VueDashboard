<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VueLayouts;

class CpanelController extends Controller
{
    //


    public function MainGet()
    {
        
        //
        return view('Cpanel.main');
        
    }

    public function LandingGet()
    {
        //
        //vue Layouts
        $getLandingLayout=VueLayouts::where('wherePage','landing')->orderBy('sort', 'asc')->where('compType','!=','ProdInBox')->get();
        $getProdInBox=VueLayouts::where('wherePage','landing')->where('compType','ProdInBox')->get();

        return view('Cpanel.layouts.landing',['Layout'=>$getLandingLayout,'ProdInBox'=>$getProdInBox]);
    }

    public function updateSectionLanding(Request $request)
    {

        # code...
        $validate=$request->validate([
            'SectionTitleI'=>'required',
            'SectionValI'=>'required',
            'compNameI'=>'required',
            'ItemNumI'=>'required|not_in:0',
            'linkI'=>'max:225',
        ]);

        $update=[
            'title'=>$validate['SectionTitleI'],
            'value'=>$validate['SectionValI'],
            'itemNum'=>$validate['ItemNumI'],
            'link'=>$validate['linkI']
        ];

        $getSection=Vuelayouts::where('compName',$validate['compNameI'])->update($update);

        if($getSection >0){
            return response()->json(['success'=>true,'item'=>$validate], 200);
           
        }
        else{
            return response()->json(['success'=>false,'item'=>null], 403);
        }

    }
}
