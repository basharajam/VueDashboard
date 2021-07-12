<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\post;
use App\Models\TermTaxonomy;
use App\Models\TermRelation;
class ApiController extends Controller
{
    //

    public function getProds()
    {

        //get Products By Tag id
        function getProdByTax($tax,$limit)
        {
            set_time_limit(0);
            //get Relations
            $Tax0=TermRelation::where('term_taxonomy_id',TermTaxonomy::where('term_id',$tax)->pluck('term_taxonomy_id'))->take($limit)->pluck('object_id');
            //get Products
            $ProdByTax=Post::whereIn('id',$Tax0)->get();
            return $ProdByTax;
        };
        //end Product By Tag id

        


        //Get Products By Tag
        $ProdByTax=getProdByTax(699,12);
        $ProdByTax0=getProdByTax(718,9);
        $ProdByTax1=getProdByTax(720,8);
        $ProdByTax2=getProdByTax(695,12);
        $ProdByTax3=getProdByTax(731,12);
        $ProdByTax4=getProdByTax(705,12);
        $ProdByTax5=getProdByTax(723,10);
        $ProdByTax6=getProdByTax(717,12);
        $ProdByTax7=getProdByTax(716,12);
        $ProdByTax8=getProdByTax(703,12);
        
        //response
        $response=['ProdByTax'=>$ProdByTax,'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,'ProdByTax8'=>$ProdByTax8];

       return response()->json($response, 200);

    }

}
