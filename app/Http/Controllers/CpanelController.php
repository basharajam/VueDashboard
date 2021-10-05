<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VueLayouts;
use Illuminate\Support\Facades\Validator;

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
        $getProdInBox=VueLayouts::where('wherePage','landing')->where('compType','ProdInBox')->orderBy('sort','asc')->get();
        $getLandingLayoutMobile=VueLayouts::where('wherePage','landing')->orderBy('sortMobile', 'asc')->where('compType','!=','ProdInBox')->get();
        return view('Cpanel.layouts.landing',['Layout'=>$getLandingLayout,'mobileLayout'=>$getLandingLayoutMobile,'ProdInBox'=>$getProdInBox]);
    }

    public function updateSectionLanding(Request $request)
    {

        # code...
        $validate=$request->validate([
            'title'=>'required',
            'value'=>'required',
            'compName'=>'required',
            'ItemNum'=>'required',
            'link'=>'max:225',
            'displayDesktop'=>'required',
            'displayMobile'=>'required',
            'compId'=>'required'
        ]);

        

        $update=[
            'title'=>$validate['title'],
            'value'=>$validate['value'],
            'itemNum'=>$validate['ItemNum'],
            'link'=>$validate['link'],
            'Display'=>$validate['displayDesktop'],
            'mobileDisplay'=>$validate['displayMobile'],
        ];

        $getSection=Vuelayouts::where('compName',$validate['compName'])->where('id',$validate['compId'])->update($update);

        if($getSection >0){
            return response()->json(['success'=>true,'item'=>$validate], 200);
           
        }
        else{
            return response()->json(['success'=>false,'item'=>null], 403);
        }

    }

    public function getLayout(Request $request)
    {
        //validate Input 
        $id=$request->input('id');
        if(!empty($id)){

        //get Layout 
        $getLayout=VueLayouts::find($id);

        

        if(!empty($getLayout)){

            return response()->json($getLayout, 200);
        }
        else{
            return response()->json(null, 400);
        }
        //response
        }
    }
    public function updateSort(Request $request)
    {
        //validate
        //return $request->all();
        $NewSort=$request->input('sortArr');
        $ids=$request->input('idArr');
        $type=$request->input('type');
        
        if(!empty($type) && count($ids) > 0 && count($NewSort)){

            foreach ($NewSort as $key=>$item ) {
                $getSec=VueLayouts::find($ids[$key]);
                if($type=='desktop'){
                    $update=$getSec->update(['sort'=>$item]);
                }
                elseif($type==='mobile'){
                    $update=$getSec->update(['sortMobile'=>$item]);
                }
            }
    
            return response()->json(['success'=>true], 200);

        }
        else{
            return response()->json(['success'=>false], 400);

        }
   

    }


    public function SaveComp(Request $request)
    {
        //valiadte Inputs
        $validate=$request->validate([
            'CompTypeNI'=>'required',
            'CompNameNI'=>'required',
            'SectionTitleNI'=>'required|min:3',
            'SectionTypeNI'=>'required',
            'SectionValNI'=>'required',
            'ItemNumNI'=>'required|integer',
            'linkNI'=>'required',
            'displayMobileNI'=>'required',
            'displayDesktopNI'=>'required',
            'compwhereNI'=>'required'
        ]);

        //get Last Sort Mobile And Desktop
        $getSortDesk=VueLayouts::orderBy('sort','desc')->first();
        $getSortMob=VueLayouts::orderBy('sortMobile','desc')->first();
        

        //Save 
        $SaveComp=new VueLayouts([
            'title'=>$validate['SectionTitleNI'],
            'value'=>$validate['SectionValNI'],
            'type'=>$validate['SectionTypeNI'],
            'wherePage'=>$validate['compwhereNI'],
            'compName'=>$validate['CompNameNI'],
            'compType'=>$validate['CompTypeNI'],
            'itemNum'=>$validate['ItemNumNI'],
            'link'=>$validate['linkNI'],
            'sort'=>$getSortDesk['sort']+1,
            'sortMobile'=>$getSortMob['sortMobile']+1,
            'Display'=>$validate['displayDesktopNI'],
            'mobileDisplay'=>$validate['displayMobileNI']
        ]);

        $SaveComp->save();
        return $SaveComp;
    }

    public function DelComp(Request $request)
    {

        //return $request->all();
      
        //validate input 
        $validate=$request->validate([
            'CompDelI'=>'required',
            'CompIdI'=>'required'
            
        ]);

        //Check Component 
        $getComp=VueLayouts::find($validate['CompIdI']);
        if( $validate['CompDelI'] ==='delete' &&  !empty($getComp)){

            //Delete Component
            $getComp->delete();
            return 'Deleted ';
        }

        return $validate;
    }


    public function ProdOneGet()
    {
        //get Layout Items Where compWhere = ProdOne

        $getComp=VueLayouts::where('wherePage','ProdOne')->get();

        return view('Cpanel.layouts.ProdOne',['Layout'=>$getComp]);

    }

    public function ProdByCatGet()
    {
        //get Layout Items Where compWhere = ProdByCat

        $getComp=VueLayouts::where('wherePage','ProdByCat')->orderBy('sort','asc')->get();
        $getCompMobile=VueLayouts::where('wherePage','ProdByCat')->orderBy('sortMobile','asc')->get();

        return view('Cpanel.layouts.ProdByCat',['Layout'=>$getComp,'LayoutMobile'=>$getCompMobile]);
    }

    public function AllCatGet()
    {
        $getComp=VueLayouts::where('wherePage','AllCat')->orderBy('sort','asc')->get();
        $getCompMobile=VueLayouts::where('wherePage','AllCat')->orderBy('sortMobile','asc')->get();

        return view('Cpanel.layouts.AllCat',['Layout'=>$getComp,'LayoutMobile'=>$getCompMobile]);
    }
    
}
