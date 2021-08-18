<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostV;
use App\Models\TermTaxonomy;
use App\Models\TermRelation;
use App\Models\Term;
use App\Models\VueLayouts;
class ApiController extends Controller
{
    //

    public function getProds($cur,$ship)
    {

        set_time_limit(0);

        //get Products By Tag id
        function getProdBy($tax,$limit,$cur,$ship,$type,$title)
        {


            if($type ==='tag'){
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
            $trans=$ProdBy->map(function($item) use ($cur,$ship,$limit,$title) {
                
                //return $item;

                $curArr=array(
                    'AED'=>array(
                        'exhange'=>0.57,
                        'increasedShipCost'=>$ship,
                        'symbol'=>'د.إ'
                    ),
                    'SAR'=>array(
                        'exhange'=>0.58,
                        'increasedShipCost'=>$ship,
                        'symbol'=>'ر.س'
                    ),
                    'OMR'=>array(
                        'exhange'=>0.06,
                        'increasedShipCost'=>$ship,
                        'symbol'=>'ر.ع'
                    ),
                    'IQD'=>array(
                        'exhange'=>0.155, //USD
                        'increasedShipCost'=>$ship,
                        'symbol'=>'$'
                    ),
                    'LBP'=>array(
                        'exhange'=>0.155, //USD
                        'increasedShipCost'=>$ship,
                        'symbol'=>'$'
                    ),
                    'SYP'=>array(
                        'exhange'=>0.57, //AED
                        'increasedShipCost'=>$ship, //AED
                        'symbol'=>'د.إ' //AED
                    ),
                    'CNY'=>array(
                        'exhange'=>1,
                        'increasedShipCost'=>$ship,
                        'symbol'=>'¥'
                    ),
                    'random'=>array(
                        'exhange'=>0.155, //USD
                        'increasedShipCost'=>$ship,
                        'symbol'=>'$'
                    )
                );
                $CurValues=$curArr;

                if($item['type'] === 'simple'){
                    $regular=$item['regular_price'];
                    $sale=$item['sale_price'];
                    $price=$item['price'];
                    $cartonQty=$item['cartqty'];
                    $Cbm=$item['cbm'];
                }
                elseif($item['type'] === 'variable'){
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



                }

                if($item['type'] ==='simple' && array_key_exists($cur,$curArr)){


                    $CartonShipPrice=$CurValues[$cur]['increasedShipCost']*$Cbm;
                    $ProdShipPrice=$CartonShipPrice / $cartonQty;
                    $fullPrice=$ProdShipPrice+(float)$regular;
                    $saleFullPrice=$ProdShipPrice+(float)$sale;
                    $regPriceHtml=$fullPrice*$CurValues[$cur]['exhange'] ;
                    $salePriceHtml=$saleFullPrice*$CurValues[$cur]['exhange'];

                }
                elseif($item['type'] ==='simple' && !array_key_exists($cur,$curArr)){


                    $CartonShipPrice=$CurValues['random']['increasedShipCost']*$Cbm;
                    $ProdShipPrice=$CartonShipPrice / $cartonQty;
                    $fullPrice=$ProdShipPrice+(float)$regular;
                    $saleFullPrice=$ProdShipPrice+(float)$sale;
                    $regPriceHtml=$fullPrice*$CurValues['random']['exhange'] ;
                    $salePriceHtml=$saleFullPrice*$CurValues['random']['exhange'];


                }
                elseif($item['type'] === 'variable' && array_key_exists($cur,$curArr)){
                    $minCartonShipPrice=(float)$CurValues[$cur]['increasedShipCost']*$minCbm;
                    $maxCartonShipPrice=(float)$CurValues[$cur]['increasedShipCost']*$maxCbm;
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
                    $minRegFullPriceHtml=$minRegFullPrice*$CurValues[$cur]['exhange'];
                    $maxRegFullPriceHtml=$maxRegFullPrice*$CurValues[$cur]['exhange'];
                    $minSaleFullPriceHtml=$minSaleFullPrice*$CurValues[$cur]['exhange'];
                    $maxSaleFullPriceHtml=$maxSaleFullPrice*$CurValues[$cur]['exhange'];
                }
                elseif($item['type'] === 'variable' && !array_key_exists($cur,$curArr)){
                    $minCartonShipPrice=(float)$CurValues['random']['increasedShipCost']*$minCbm;
                    $maxCartonShipPrice=(float)$CurValues['random']['increasedShipCost']*$maxCbm;
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
                    $minRegFullPriceHtml=$minRegFullPrice*$CurValues['random']['exhange'];
                    $maxRegFullPriceHtml=$maxRegFullPrice*$CurValues['random']['exhange'];
                    $minSaleFullPriceHtml=$minSaleFullPrice*$CurValues['random']['exhange'];
                    $maxSaleFullPriceHtml=$maxSaleFullPrice*$CurValues['random']['exhange'];
                }

                //Cur Symbol
                if(array_key_exists($cur,$curArr)){
                    $curHtml=$curArr[$cur]['symbol'];
                }
                else{
                    $curHtml=$curArr['random']['symbol'];
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

                        if($minRegFullPriceHtml === $maxRegFullPriceHtml){
                            $price_html='<span><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'</span>' . number_format((float)$minRegFullPriceHtml,2) . ' </span>  <span style="font-weight: bold;color:black;font-size: 16px;">-</span> <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'.$curHtml.'</span>' . number_format((float)$maxRegFullPriceHtml,2) . '</span></span>';
                        }
                        else{
                            $price_html='<span><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$minRegFullPriceHtml,2) . ' </span> <span style="font-weight: bold;color:black;font-size: 16px;">-</span> <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'.$curHtml.'</span>'.number_format((float)$maxRegFullPriceHtml,2).'</span></span>';
                        }

                        
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

            return ['items'=>$trans,'count'=>$limit,'title'=>$title];

        };
       //end Product By 

       //Set Shipment Price
       switch ($ship) {
        case "SA":
            $shipCost=2200;
            break;
        case "SY":
            $shipCost=960; //AED
            break;
        case "OM":
            $shipCost=985;
            break;
        case "YE":
            $shipCost=985;
            break;
        case "LB":
            $shipCost=1935; //OM
            break;
        case "IQ":
            $shipCost=1550;
            break;
        case "AE":
            $shipCost=960;
            break;
        case "PS":
            $shipCost=1935; //Lbn
            break;
        default:
            $shipCost=0;
    }

    // $AEDship=960;
    // $SARship=2300;
    // $OMRship=985;
    // $IrqShip=1550;
    // $LbShip=1935;
    $shipPerc=0.2;
    $finalShipCost=$shipCost*$shipPerc+$shipCost;


        
     
        $response=array();

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
        $response['Categories'] = $transCat;
        

        //Get Products By Tag
        $getLayoutsLists=VueLayouts::where(['wherePage'=>'landing','compType'=>'ProdList'])->get();
        foreach ($getLayoutsLists as $item) {
            $ProdByTax=getProdBy($item['value'],$item['itemNum'],$cur,$finalShipCost,$item['type'],$item['title']);
            $response[$item['compName']] = $ProdByTax;
        }

        //get ProdIn Box
        $getLayoutsLists0=VueLayouts::where(['wherePage'=>'landing','compType'=>'ProdInBox'])->get();
        foreach ($getLayoutsLists0 as $item) {
            $ProdInBox=getProdBy($item['value'],$item['itemNum'],$cur,$finalShipCost,$item['type'],$item['title']);
            $response[$item['compName']] = $ProdInBox;
        }

        return response()->json($response, 200);


        // $ProdByTax=getProdBy(699,12,$cur,$finalShipCost,'tag');
        // $ProdByTax0=getProdBy(718,9,$cur,$finalShipCost,'tag');
        // $ProdByTax1=getProdBy(720,8,$cur,$finalShipCost,'tag');
        // $ProdByTax2=getProdBy(695,12,$cur,$finalShipCost,'tag');
        // $ProdByTax3=getProdBy(731,12,$cur,$finalShipCost,'tag');
        // $ProdByTax4=getProdBy(705,12,$cur,$finalShipCost,'tag');
        // $ProdByTax5=getProdBy(723,10,$cur,$finalShipCost,'tag');
        // $ProdByTax6=getProdBy(717,12,$cur,$finalShipCost,'tag');
        // $ProdByTax7=getProdBy(716,12,$cur,$finalShipCost,'tag');
        // $ProdByTax8=getProdBy(703,12,$cur,$finalShipCost,'tag');
        // $ProdByTax9=getProdBy(757,12,$cur,$finalShipCost,'tag'); 
       
        // $MostPop=getProdBy(755,12,$cur,$finalShipCost,'tag');

        //get Recent Products 
        //$getRecentProds=getProdBy(0,8,$cur,$finalShipCost,'newest');
        
        // //get Offers Prods
        // $offers=getProdBy(0,12,$cur,$finalShipCost,'offers');

        //Prod By Box
        // $ProdByBox=getProdBy(696,6,$cur,$finalShipCost,'tag'); 
        //get ProdInBox
        // $ProdInBox=getProdBy(704,4,$cur,$finalShipCost,'tag'); //Prod Sticker
        // $ProdInBox0=getProdBy(705,4,$cur,$finalShipCost,'tag'); //Prod DecIns
        // $ProdInBox1=getProdBy(757,4,$cur,$finalShipCost,'tag'); //Prod tag
        // $ProdInBox2=getProdBy(703,4,$cur,$finalShipCost,'tag'); //Prod DecRope
        
        //Best Sell Prods
        // $BestSell=getProdBy(719,12,$cur,$finalShipCost,'tag');

        //$response=['Categories'=>$transCat,'ProdByTax'=>$ProdByTax,'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,'ProdByTax8'=>$ProdByTax8];
        //$response=['Categories'=>$transCat,'ProdByTax'=>$ProdByTax,'ProdByBox'=>$ProdByBox,'ProdInBox'=>$ProdInBox,'ProdInBox0'=>$ProdInBox0,'ProdInBox1'=>$ProdInBox1,'ProdInBox2'=>$ProdInBox2,'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,'ProdByTax8'=>$ProdByTax8];
        //response
        // $response=[
        //     'Categories'=>$transCat,'ProdByTax'=>$ProdByTax,
        //     'ProdInBox'=>$ProdInBox,'ProdInBox0'=>$ProdInBox0,
        //     'ProdInBox1'=>$ProdInBox1,'ProdInBox2'=>$ProdInBox2,
        //     'ProdByTax0'=>$ProdByTax0,'ProdByTax1'=>$ProdByTax1,
        //     'ProdByTax2'=>$ProdByTax2,'ProdByTax3'=>$ProdByTax3,
        //     'ProdByTax4'=>$ProdByTax4,'ProdByTax5'=>$ProdByTax5,
        //     'ProdByTax6'=>$ProdByTax6,'ProdByTax7'=>$ProdByTax7,
        //     'ProdByTax8'=>$ProdByTax8,'ProdByTax9'=>$ProdByTax9,
        //     'ProdByBox'=>$ProdByBox,'RecentProds'=>$getRecentProds,
        //     'Offers'=>$offers,'BestSell'=>$BestSell,
        //     'MostPop'=>$MostPop
        // ];


    }

    public function test()
    {
       // $getProds=Post::newest()->->limit(1)->get();

        $offers=Post::where('on_sale',true)->limit(1)->get();
        //$getProds->categories;
        return response()->json($offers, 200);
    }

}
