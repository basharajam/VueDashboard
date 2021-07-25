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

        set_time_limit(0);
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
        function getProdBy($tax,$limit,$cur,$type)
        {
            if($type ==='tax'){
                //get Relations
                $Tax0=TermRelation::where('term_taxonomy_id',TermTaxonomy::where('term_id',$tax)->pluck('term_taxonomy_id'))->take(60)->pluck('object_id');
                //get Products
                $ProdBy=Post::whereIn('id',$Tax0)->published()->limit($limit)->get();
            }
            elseif($type === 'newest'){
                $ProdBy=Post::newest()->published()->limit($limit)->get();
            }
            elseif($type === 'offers'){
                $getProdSale=Post::published()->limit(60)->get();
                $ProdBy=$getProdSale->filter(function($model){
                          return $model->on_sale == true;
                })->take($limit)->values();
            }

            //Transform Product Object
            $trans=$ProdBy->map(function($item) use ($cur) {

                $AED=0.57;
                $SAR=0.58;
                $USD=0.155;
                $OMR=0.06;

                $regular=$item['regular_price'];
                $sale=$item['sale_price'];
                $price=$item['price'];
                //price html
                if(!empty($cur) && $cur ==='AED' ){

                    $regPriceHtml=(float)$regular*$AED ;
                    $salePriceHtml=(float)$sale*$AED;
                    $curHtml='د.إ';
                }
                elseif(!empty($cur) && $cur ==='SYP' ){

                    $regPriceHtml=(float)$regular*$AED ;
                    $salePriceHtml=(float)$sale*$AED;
                    $curHtml='د.إ';
                }
                elseif(!empty($cur) && $cur ==='SAR' ){

                    $regPriceHtml=(float)$regular* $SAR ;
                    $salePriceHtml=(float)$sale*$SAR;
                    $curHtml='ر.س';
                }
                elseif(!empty($cur) && $cur ==='USD'){
                    
                    $regPriceHtml=(float)$regular* $USD;
                    $salePriceHtml=(float)$sale*$USD;
                    $curHtml='$';
                }
                elseif(!empty($cur) && $cur ==='OMR'){
                    $regPriceHtml=(float)$regular* $OMR;
                    $salePriceHtml=(float)$sale*$OMR;
                    $curHtml='ر.ع';
                }
                elseif(!empty($cur) && $cur ==='CNY'){
                    $regPriceHtml=(float)$regular;
                    $salePriceHtml=(float)$sale;
                    $curHtml='¥';
                }
                else{
                    $regPriceHtml=(float)$regular*$USD;
                    $salePriceHtml=(float)$sale*$USD;
                    $curHtml='$';
                }


                if($item['on_sale']){

                    $price_html='<span =""><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>' .number_format((float)$regPriceHtml,2). '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$salePriceHtml,2) . ' </span></ins></span>';
                }
                else{
                    $price_html='<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>  ' .number_format((float)$regPriceHtml,2)  . '  </span>';
                }
                
                //get Avarage Rate 
                $arr=$item->meta;
                $avgRate =array_search('_wc_average_rating', array_column(json_decode($arr,true), 'meta_key'));

                //img 
                if(!empty($item->gallery[0]['guid'])){

                    $imgMetaArr = $item->gallery[0]['meta'];
                    $imgMetaIndex=array_search('_wp_attached_file', array_column(json_decode($imgMetaArr,true), 'meta_key'));
                    $imgMeta=$imgMetaArr[$imgMetaIndex];
                    $imgurl='http://alyaman.com/wp-content/uploads/'.$imgMeta['meta_value'];
                    $imgArr=[['src'=>$imgurl]];
                }
                else{
                    $imgArr=null;
                }
                
                return [
                    'id'=>$item->ID,
                    'name'=>$item->post_title,
                    'permalink'=>'https://www.alyaman.com/product/' . $item->post_name,
                    'average_rating'=>$arr[$avgRate]['meta_value'],
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
       //end Product By 

        //Get Products By Tag
        $ProdByTax=getProdBy(699,12,$cur,'tax');
        $ProdByTax0=getProdBy(718,9,$cur,'tax');
        $ProdByTax1=getProdBy(720,8,$cur,'tax');
        $ProdByTax2=getProdBy(695,12,$cur,'tax');
        $ProdByTax3=getProdBy(731,12,$cur,'tax');
        $ProdByTax4=getProdBy(705,12,$cur,'tax');
        $ProdByTax5=getProdBy(723,10,$cur,'tax');
        $ProdByTax6=getProdBy(717,12,$cur,'tax');
        $ProdByTax7=getProdBy(716,12,$cur,'tax');
        $ProdByTax8=getProdBy(703,12,$cur,'tax');
       
       
        return $MostPop=getProdBy(755,12,$cur,'tax');

        //Prod By Box
        $ProdByBox=getProdBy(696,4,$cur,'tax'); 

        //get ProdInBox
        $ProdInBox=getProdBy(704,4,$cur,'tax'); //Prod Sticker
        $ProdInBox0=getProdBy(705,4,$cur,'tax'); //Prod DecIns
        $ProdInBox1=getProdBy(707,4,$cur,'tax'); //Prod tag
        $ProdInBox2=getProdBy(703,4,$cur,'tax'); //Prod DecRope
        
        //get Recent Products 
        $getRecentProds=getProdBy(0,8,$cur,'newest');

        //get Offers Prods
        $offers=getProdBy(0,12,$cur,'offers');

        //response
        //$response=['Categories'=>$transCat,'ProdByTax'=>$ProdByTax,'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,'ProdByTax8'=>$ProdByTax8];
        //$response=['Categories'=>$transCat,'ProdByTax'=>$ProdByTax,'ProdByBox'=>$ProdByBox,'ProdInBox'=>$ProdInBox,'ProdInBox0'=>$ProdInBox0,'ProdInBox1'=>$ProdInBox1,'ProdInBox2'=>$ProdInBox2,'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,'ProdByTax8'=>$ProdByTax8];
        $response=[
            'Categories'=>$transCat,'ProdByTax'=>$ProdByTax,
            'ProdInBox'=>$ProdInBox,'ProdInBox0'=>$ProdInBox0,
            'ProdInBox1'=>$ProdInBox1,'ProdInBox2'=>$ProdInBox2,
            'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,
            'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,
            'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,
            'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,
            'ProdByTax8'=>$ProdByTax8,'ProdByBox'=>$ProdByBox,
            'RecentProds'=>$getRecentProds,'Offers'=>$offers,
            'MostPop'=>$MostPop
        ];

        return response()->json($response, 200);

    }

    public function test()
    {
       // $getProds=Post::newest()->->limit(1)->get();

        $offers=Post::where('on_sale',true)->limit(1)->get();
        //$getProds->categories;
        return response()->json($offers, 200);
    }

}
