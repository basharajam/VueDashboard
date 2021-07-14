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

    public function getProds()
    {


  

        getCategories
        $getCat=TermTaxonomy::where('taxonomy','product_cat')->pluck('term_id');
        $getTerm=Term::whereIn('term_id',$getCat)->get();
        $transCat=$getTerm->map(function($item){

            //return $item ;

            return [
                'name'=>$item->name,
                'slug'=>$item->slug,
                'image'=>(object)array('src'=> 'https://www.alyaman.com/wp-content/uploads/'.$item->taxonomy->image->meta['_wp_attached_file'])
            ];

        });


      
        //get Products By Tag id
        function getProdByTax($tax,$limit)
        {

                //get Relations
                $Tax0=TermRelation::where('term_taxonomy_id',TermTaxonomy::where('term_id',$tax)->pluck('term_taxonomy_id'))->take($limit)->pluck('object_id');
                //get Products
                $ProdByTax=Post::whereIn('id',$Tax0)->where('post_type','!=','attachment')->get();
                $trans=$ProdByTax->map(function($item){
    
                    return $item;

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
                         $price_html='<span =""><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">ر.س</span>' .number_format((float)$regular,2). '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">ر.س</span> ' . number_format((int)$regular,2) . ' </span></ins></span>';
                    }
                    else{
                         $sale='';
                         $price_html='<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">ر.س</span>' .number_format((float)$price,2)  . '</span>';
                    }

                    //img 
                    $img = 'https://www.alyaman.com/wp-content/uploads/'.$item->images->meta['_wp_attached_file'];
                    $imgArr=[['src'=>$img]];

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
            $ProdByTax=getProdByTax(699,12);
            $ProdByTax0=getProdByTax(718,9);
            $ProdByTax1=getProdByTax(720,8);
            $ProdByTax2=getProdByTax(695,12);
            $ProdByTax3=getProdByTax(731,12);
            $ProdByTax4=getProdByTax(705,12);
            $ProdByTax5=getProdByTax(723,10);
            $ProdByTax6=getProdByTax(717,12);
            $ProdByTax7=getProdByTax(716,12);
            $ProdByTax8=getProdByTax(703,20);


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
