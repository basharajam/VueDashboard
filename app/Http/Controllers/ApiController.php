<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostV;
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
                $Tax0=TermRelation::where('term_taxonomy_id',TermTaxonomy::where('term_id',$tax)->pluck('term_taxonomy_id'))->pluck('object_id');
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

                //return $item;


                $AED=0.57;
                $SAR=0.58;
                $OMR=0.06;
                $USD=0.155;

                $shipPerc=0.2;

                $AEDship=960;
                $SARship=2300;
                $OMRship=985;
                $IrqShip=1550;
                $LbShip=1935;

                $increasedAEDship=$AEDship*$shipPerc+$AEDship;
                $increasedSARship=$SARship*$shipPerc+$SARship;
                $increasedOMRship=$OMRship*$shipPerc+$OMRship;
                $increasedIrqship=$IrqShip*$shipPerc+$IrqShip;
                $increasedLbShip=$LbShip*$shipPerc+$LbShip;

                if($item['type'] ==='simple'){

                    $regular=$item['regular_price'];
                    $sale=$item['sale_price'];
                    $price=$item['price'];
                    $cartonQty=$item['cartqty'];
                    $Cbm=$item['cbm'];

                }
                elseif($item['type'] === 'variable'){

                    //return $item['ID'];
                     $VarProd= PostV::where('post_parent',$item['ID'])->get();
                     $CbmArr=array();
                     $QtyArr=array();
                     $regularPArr=array();
                     $salePArr=array();
                     
                     foreach ($VarProd as $Prod) {
                        array_push($CbmArr,$Prod['cbm']);
                        array_push($QtyArr,$Prod['cartqty']);
                        array_push($regularPArr,$Prod['regular_price']);
                        array_push($salePArr,$Prod['sale_price']);
                     }


                     $minQty=min($QtyArr);
                     $maxQty=max($QtyArr);
                     $minCbm=min($CbmArr);
                     $maxCbm=max($CbmArr);
                     $minRegPrice=min($regularPArr);
                     $maxRegPrice=max($regularPArr);
                     $minSalePrice=min($salePArr);
                     $maxSalePrice=max($salePArr);

                     //return ['minReg'=>$minRegPrice,'maxReg'=>$maxRegPrice,'minSale'=>$minSalePrice,'maxSale'=>$maxSalePrice,'minCbm'=>$minCbm,'maxCbm'=>$maxCbm,'minQty'=>$minQty,'maxQty'=>$maxQty];
                    

                }


                //price html
                if(!empty($cur) && $cur ==='AED' ){

                    if($item['type'] === 'simple'){

                        $CartonShipPrice=$increasedAEDship*$Cbm;
                        $ProdShipPrice=$CartonShipPrice / $cartonQty;
                        $fullPrice=$ProdShipPrice+(float)$regular;
                        $saleFullPrice=$ProdShipPrice+(float)$sale;
                        $regPriceHtml=$fullPrice*$AED ;
                        $salePriceHtml=$saleFullPrice*$AED;

                    }
                    elseif($item['type'] === 'variable'){


                        $minCartonShipPrice=(float)$increasedAEDship*$minCbm;
                        $maxCartonShipPrice=(float)$increasedAEDship*$maxCbm;
                        $minProdShipPrice=(float)$minCartonShipPrice/$minQty;
                        $maxProdShipPrice=(float)$maxCartonShipPrice/$maxQty;
                        //reg Price
                        $minRegFullPrice=$minProdShipPrice+(float)$minRegPrice;
                        $maxRegFullPrice=$maxProdShipPrice+(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=$minProdShipPrice+(float)$minSalePrice;
                            $maxSaleFullPrice=$maxProdShipPrice+(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        //xhange price
                        $minRegFullPriceHtml=$minRegFullPrice*$AED;
                        $maxRegFullPriceHtml=$maxRegFullPrice*$AED;
                        $minSaleFullPriceHtml=$minSaleFullPrice*$AED;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice*$AED;


                    }

                    $curHtml='د.إ';
                }
                elseif(!empty($cur) && $cur ==='SYP' ){

                    if($item['type'] === 'simple'){

                        $regPriceHtml=(float)$regular*$AED ;
                        $salePriceHtml=(float)$sale*$AED;

                    }else{

                        $minCartonShipPrice=$increasedAEDship*$minCbm;
                        $maxCartonShipPrice=$increasedAEDship*$maxCbm;
                        $minProdShipPrice=$minCartonShipPrice/$minQty;
                        $maxProdShipPrice=$maxCartonShipPrice/$maxQty;
                        //reg Price
                        $minRegFullPrice=$minProdShipPrice+(float)$minRegPrice;
                        $maxRegFullPrice=$maxProdShipPrice+(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=$minProdShipPrice+(float)$minSalePrice;
                            $maxSaleFullPrice=$maxProdShipPrice+(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        //xhange price
                        $minRegFullPriceHtml=$minRegFullPrice*$AED;
                        $maxRegFullPriceHtml=$maxRegFullPrice*$AED;
                        $minSaleFullPriceHtml=$minSaleFullPrice*$AED;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice*$AED;


                    }

                    $curHtml='د.إ';
                }
                elseif(!empty($cur) && $cur ==='SAR' ){

                    // $CartonShipPrice=$increasedSARship*$Cbm;
                    // $ProdShipPrice=$CartonShipPrice / $cartonQty;
                    // $fullPrice=$ProdShipPrice+(float)$regular;
                    // $saleFullPrice=$ProdShipPrice+(float)$sale;
                    // $regPriceHtml=$fullPrice* $SAR ;
                    // $salePriceHtml=$saleFullPrice*$SAR;
                    if($item['type'] === 'simple'){

                        $CartonShipPrice=$increasedSARship*$Cbm;
                        $ProdShipPrice=$CartonShipPrice / $cartonQty;
                        $fullPrice=$ProdShipPrice+(float)$regular;
                        $saleFullPrice=$ProdShipPrice+(float)$sale;
                        $regPriceHtml=$fullPrice*$SAR ;
                        $salePriceHtml=$saleFullPrice*$SAR;

                    }
                    elseif($item['type'] === 'variable'){


                        $minCartonShipPrice=$increasedSARship*$minCbm;
                        $maxCartonShipPrice=$increasedSARship*$maxCbm;
                        $minProdShipPrice=$minCartonShipPrice/$minQty;
                        $maxProdShipPrice=$maxCartonShipPrice/$maxQty;
                        //reg Price
                        $minRegFullPrice=$minProdShipPrice+(float)$minRegPrice;
                        $maxRegFullPrice=$maxProdShipPrice+(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=$minProdShipPrice+(float)$minSalePrice;
                            $maxSaleFullPrice=$maxProdShipPrice+(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        //xhange price
                        $minRegFullPriceHtml=$minRegFullPrice*$SAR;
                        $maxRegFullPriceHtml=$maxRegFullPrice*$SAR;
                        $minSaleFullPriceHtml=$minSaleFullPrice*$SAR;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice*$SAR;


                    }

                    $curHtml='ر.س';
                }
                elseif(!empty($cur) && $cur ==='OMR'){

                    // $CartonShipPrice=$increasedOMRship*$Cbm;
                    // $ProdShipPrice=$CartonShipPrice / $cartonQty;
                    // $fullPrice=$ProdShipPrice+(float)$regular;
                    // $saleFullPrice=$ProdShipPrice+(float)$sale;
                    // $regPriceHtml=$fullPrice* $OMR;
                    // $salePriceHtml=$saleFullPrice*$OMR;
                    if($item['type'] === 'simple'){

                        $CartonShipPrice=$increasedOMRship*$Cbm;
                        $ProdShipPrice=$CartonShipPrice / $cartonQty;
                        $fullPrice=$ProdShipPrice+(float)$regular;
                        $saleFullPrice=$ProdShipPrice+(float)$sale;
                        $regPriceHtml=$fullPrice*$SAR ;
                        $salePriceHtml=$saleFullPrice*$SAR;

                    }
                    elseif($item['type'] === 'variable'){


                        $minCartonShipPrice=$increasedOMRship*$minCbm;
                        $maxCartonShipPrice=$increasedOMRship*$maxCbm;
                        $minProdShipPrice=$minCartonShipPrice/$minQty;
                        $maxProdShipPrice=$maxCartonShipPrice/$maxQty;
                        //reg Price
                        $minRegFullPrice=$minProdShipPrice+(float)$minRegPrice;
                        $maxRegFullPrice=$maxProdShipPrice+(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=$minProdShipPrice+(float)$minSalePrice;
                            $maxSaleFullPrice=$maxProdShipPrice+(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        //xhange price
                        $minRegFullPriceHtml=$minRegFullPrice*$OMR;
                        $maxRegFullPriceHtml=$maxRegFullPrice*$OMR;
                        $minSaleFullPriceHtml=$minSaleFullPrice*$OMR;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice*$OMR;

                    }
                    $curHtml='ر.ع';
                }
                elseif(!empty($cur) && $cur ==='IQD'){
                    if($item['type'] === 'simple'){

                        $CartonShipPrice=$increasedIrqship*$Cbm;
                        $ProdShipPrice=$CartonShipPrice / $cartonQty;
                        $fullPrice=$ProdShipPrice+(float)$regular;
                        $saleFullPrice=$ProdShipPrice+(float)$sale;
                        $regPriceHtml=$fullPrice*$USD ;
                        $salePriceHtml=$saleFullPrice*$USD;

                    }
                    elseif($item['type'] === 'variable'){


                        $minCartonShipPrice=$increasedIrqship*$minCbm;
                        $maxCartonShipPrice=$increasedIrqship*$maxCbm;
                        $minProdShipPrice=$increasedIrqship/$minQty;
                        $maxProdShipPrice=$increasedIrqship/$maxQty;
                        //reg Price
                        $minRegFullPrice=$minProdShipPrice+(float)$minRegPrice;
                        $maxRegFullPrice=$maxProdShipPrice+(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=$minProdShipPrice+(float)$minSalePrice;
                            $maxSaleFullPrice=$maxProdShipPrice+(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        //xhange price
                        $minRegFullPriceHtml=$minRegFullPrice*$USD;
                        $maxRegFullPriceHtml=$maxRegFullPrice*$USD;
                        $minSaleFullPriceHtml=$minSaleFullPrice*$USD;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice*$USD;

                    }
                    $curHtml='$';
                }
                elseif(!empty($cur) && $cur ==='LBP' ){
                    if($item['type'] === 'simple'){

                        $CartonShipPrice=$increasedLbShip*$Cbm;
                        $ProdShipPrice=$CartonShipPrice / $cartonQty;
                        $fullPrice=$ProdShipPrice+(float)$regular;
                        $saleFullPrice=$ProdShipPrice+(float)$sale;
                        $regPriceHtml=$fullPrice*$USD ;
                        $salePriceHtml=$saleFullPrice*$USD;

                    }
                    elseif($item['type'] === 'variable'){


                        $minCartonShipPrice=$increasedLbShip*$minCbm;
                        $maxCartonShipPrice=$increasedLbShip*$maxCbm;
                        $minProdShipPrice=$increasedLbShip/$minQty;
                        $maxProdShipPrice=$increasedLbShip/$maxQty;
                        //reg Price
                        $minRegFullPrice=$minProdShipPrice+(float)$minRegPrice;
                        $maxRegFullPrice=$maxProdShipPrice+(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=$minProdShipPrice+(float)$minSalePrice;
                            $maxSaleFullPrice=$maxProdShipPrice+(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        //xhange price
                        $minRegFullPriceHtml=$minRegFullPrice*$USD;
                        $maxRegFullPriceHtml=$maxRegFullPrice*$USD;
                        $minSaleFullPriceHtml=$minSaleFullPrice*$USD;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice*$USD;

                    }
                    $curHtml='$';

                }
                elseif(!empty($cur) && $cur ==='USD'){
                    
                    if($item['type'] === 'simple'){

                        $regPriceHtml=(float)$regular*$USD;
                        $salePriceHtml=(float)$sale*$USD;
                    }
                    elseif($item['type'] === 'variable'){

                        //reg Price
                        $minRegFullPrice=(float)$minRegPrice;
                        $maxRegFullPrice=(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=(float)$minSalePrice;
                            $maxSaleFullPrice=(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        //xhange price
                        $minRegFullPriceHtml=$minRegFullPrice*$USD;
                        $maxRegFullPriceHtml=$maxRegFullPrice*$USD;
                        $minSaleFullPriceHtml=$minSaleFullPrice*$USD;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice*$USD;
                    }

                    $curHtml='$';
                }
                elseif(!empty($cur) && $cur ==='CNY'){
                    
                    if($item['type'] === 'simple'){

                        $regPriceHtml=(float)$regular;
                        $salePriceHtml=(float)$sale;

                    }
                    elseif($item['type'] === 'variable'){


                        //reg Price
                        $minRegFullPrice=(float)$minRegPrice;
                        $maxRegFullPrice=(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPrice=(float)$minSalePrice;
                            $maxSaleFullPrice=(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPrice=null;
                            $maxSaleFullPrice=null;
                        }
                        $minRegFullPriceHtml=$minRegFullPrice;
                        $maxRegFullPriceHtml=$maxRegFullPrice;
                        $minSaleFullPriceHtml=$minSaleFullPrice;
                        $maxSaleFullPriceHtml=$maxSaleFullPrice;
                    }

                    $curHtml='¥';

                }
                else{

                    if($item['type'] === 'simple'){

                        $regPriceHtml=(float)$regular*$USD;
                        $salePriceHtml=(float)$sale*$USD;
                        
                    }
                    elseif($item['type'] === 'variable'){

                        //reg Price
                        $minRegFullPriceHtml=(float)$minRegPrice;
                        $maxRegFullPriceHtml=(float)$maxRegPrice;
                        //sale price
                        if($item['on_sale']){
                            $minSaleFullPriceHtml=(float)$minSalePrice;
                            $maxSaleFullPriceHtml=(float)$maxSalePrice;
                        }
                        else{
                            $minSaleFullPriceHtml=null;
                            $maxSaleFullPriceHtml=null;
                        }
                    }
                    $curHtml='$';

                }

                //Public Html Generate 
                if($item['type'] === 'simple'){

                    if($item['on_sale']){
                        $price_html='<span =""><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>' .number_format((float)$regPriceHtml,2). '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$salePriceHtml,2) . ' </span></ins></span>';
                    }
                    else{
                        $price_html='<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>  ' .number_format((float)$regPriceHtml,2)  . '  </span>';
                    }

                }
                elseif($item['type'] === 'variable'){

                    if($item['on_sale']){
                        $price_html='<span =""><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>' .number_format((float)$regPriceHtml,2). '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$salePriceHtml,2) . ' </span></ins></span>';
                    }
                    else{
                        $price_html='<span><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$minRegFullPriceHtml,2) . ' </span><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$maxRegFullPriceHtml,2) . ' </span></span>';
                    }

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
                
                if($item['type'] === 'simple'){

                    return [
                        'id'=>$item->ID,
                        'type'=>$item['type'],
                        'on_sale'=>$item->on_sale,
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
                }
                elseif($item['type'] === 'variable'){
                    return [
                        'id'=>$item->ID,
                        'type'=>$item['type'],
                        'on_sale'=>$item->on_sale,
                        'name'=>$item->post_title,
                        'permalink'=>'https://www.alyaman.com/product/' . $item->post_name,
                        'average_rating'=>$arr[$avgRate]['meta_value'],
                        'short_description'=>$item->post_excerpt,
                        'min_regular_price'=>$minRegFullPriceHtml,
                        'max_regular_price'=>$maxRegFullPriceHtml,
                        'min_sale_price'=>$minSaleFullPriceHtml,
                        'max_sale_price'=>$maxSaleFullPriceHtml,
                        'price_html'=>$price_html,
                        'images'=>$imgArr,
                        'meta'=>$item->meta
                    ];
                }


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
       
       
        // return $MostPop=getProdBy(755,12,$cur,'tax');

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
            // 'MostPop'=>$MostPop
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
