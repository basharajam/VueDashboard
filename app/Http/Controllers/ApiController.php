<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\TermTaxonomy;
use App\Models\TermRelation;
use App\Models\Term;
class ApiController extends Controller
{
    //

    public function getProds($cur)
    {

        // return $cur;

  

        //getCategories
        $getCat=TermTaxonomy::where('taxonomy','product_cat')->pluck('term_id');
        $getTerm=Term::whereIn('term_id',$getCat)->get();
        $transCat=$getTerm->map(function($item){
       
            if(!empty($item->taxonomy->image->guid)){
                $img=$item->taxonomy->image->guid;
            }
            else{
                $img='';
            }
            return [
                'name'=>$item->name,
                'slug'=>$item->slug,
                'image'=>(object)array('src'=>$img)
            ];

        });

      
        //get Products By Tag id
        function getProdByTax($tax,$limit,$cur)
        {

                //get Relations
                $Tax0=TermRelation::where('term_taxonomy_id',TermTaxonomy::where('term_id',$tax)->pluck('term_taxonomy_id'))->take($limit)->pluck('object_id');
                //get Products
                $ProdByTax=Post::whereIn('id',$Tax0)->where('post_type','!=','attachment')->get();

                $trans=$ProdByTax->map(function($item) use ($cur) {
    
                    // return $item;
                     

                    $arr=$item->meta;
                    //regular
                    if(array_key_exists('_regular_price',$arr)){
                        $regular =  $arr['_regular_price'];
                    }
                    else{
                        $regular = '';
                    }
                    //price
                    if(array_key_exists('_price',$arr)){
                        $price =  $arr['_price'];
                    }
                    else{
                        $price= '';
                    }
                    if(empty($regular)){
                        $regular=$price;
                    }

                    //sale
                    if(array_key_exists('_sale_price',$arr)){
                         $sale=  $arr['_sale_price'];
            
                         //$price_html='<span =""><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">ر.س</span>' .number_format((float)$regular,2). '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">ر.س</span> ' . number_format((int)$regular,2) . ' </span></ins></span>';
                    }
                    else{
                         $sale='';
                         //$price_html='<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">ر.س</span>' .number_format((float)$price,2)  . '</span>';
                    }

                    $AED=0.57;
                    $SAR=0.58;
                    $USD=0.155;
                    $OMR=0.06;
                    //price html
                    if(!empty($cur) && $cur ==='AED' ){

                        $regPriceHtml=(float)$regular*$AED ;
                        $salePriceHtml=(float)$sale*$AED;
                    }
                    elseif(!empty($cur) && $cur ==='SYP' ){

                        $regPriceHtml=(float)$regular*$AED ;
                        $salePriceHtml=(float)$sale*$AED;
                    }
                    elseif(!empty($cur) && $cur ==='SAR' ){

                        $regPriceHtml=(float)$regular* $SAR ;
                        $salePriceHtml=(float)$sale*$SAR;
                    }
                    elseif(!empty($cur) && $cur ==='USD'){
                        
                        $regPriceHtml=(float)$regular* $USD;
                        $salePriceHtml=(float)$sale*$USD;
                    }
                    elseif(!empty($cur) && $cur ==='OMR'){
                        $regPriceHtml=(float)$regular* $OMR;
                        $salePriceHtml=(float)$sale*$OMR;
                    }
                    elseif(!empty($cur) && $cur ==='CNY'){
                        $regPriceHtml=(float)$regular;
                        $salePriceHtml=(float)$sale;
                    }
                    else{
                        $regPriceHtml=(float)$regular*$USD;
                        $salePriceHtml=(float)$sale*$USD;
                    }

                    //Cur
                    if($cur ==='USD'){
                        $curHtml='$';
                    }
                    elseif($cur ==='SYP'){
                        $curHtml='د.إ';
                    }
                    elseif($cur ==='SAR'){
                        $curHtml='ر.س';
                    }
                    elseif($cur ==='AED'){
                        $curHtml='د.إ';
                    }
                    elseif($cur ==='OMR'){
                        $curHtml='ر.ع';
                    }
                    elseif($cur ==='CNY'){
                        $curHtml='¥';
                    }
                    else{
                        $curHtml='$'; 
                    }

                    if(array_key_exists('_sale_price',$arr) && $arr['_sale_price'] != ''){

                        $price_html='<span =""><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>' .number_format((float)$regPriceHtml,2). '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$salePriceHtml,2) . ' </span></ins></span>';
                    }
                    else{
                        $price_html='<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>  ' .number_format((float)$regPriceHtml,2)  . '  </span>';
                    }

                    //return ['price'=>$regular,'sale'=>$sale,'priceht'=>$regPriceHtml,'salehtml'=>$salePriceHtml];

                    //img 
                    if(!empty($item->images->guid)){

                        $img = $item->images->guid;
                        $imgArr=[['src'=>$img]];
                    }
                    else{
                        $imgArr=null;
                    }

                    // return $price_html;

                    return [
                        'id'=>$item->ID,
                        'name'=>$item->post_title,
                        'permalink'=>'https://www.alyaman.com/product/' . $item->post_name,
                        'average_rating'=>$item->meta['_wc_average_rating'],
                        'short_description'=>$item->post_excerpt,
                        'regular_price'=>$regular,
                        'sale_price'=>$sale,
                        'price'=>$price,
                        'price_html'=>$price_html,
                        'images'=>$imgArr,
                        'meta'=>$item->meta
                    ];

                });
    
                return $trans;

        };

      
    //     //end Product By Tag id

    //     //Get Products By Tag
            $ProdByTax=getProdByTax(699,12,$cur);
            $ProdByTax0=getProdByTax(718,9,$cur);
            $ProdByTax1=getProdByTax(720,8,$cur);
            $ProdByTax2=getProdByTax(695,12,$cur);
            $ProdByTax3=getProdByTax(731,12,$cur);
            $ProdByTax4=getProdByTax(705,12,$cur);
            $ProdByTax5=getProdByTax(723,10,$cur);
            $ProdByTax6=getProdByTax(717,12,$cur);
            $ProdByTax7=getProdByTax(716,12,$cur);
            $ProdByTax8=getProdByTax(703,20,$cur);


        //response
        $response=['Categories'=>$transCat,'ProdByTax'=>$ProdByTax,'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,'ProdByTax8'=>$ProdByTax8];

       return response()->json($response, 200);

    }

    public function test()
    {
        $getProds=Post::limit(100)->get();

        return response()->json($getProds, 200);
    }

}
