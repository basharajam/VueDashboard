<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\PostV;
use App\Models\TermTaxonomy;
use App\Models\TermRelation;
use App\Models\Term;
use App\Models\PostAll;
use App\Models\VueLayouts;
use App\Models\VueConfig;


class ProductController extends Controller
{
    //

    public function SetShipCurr($cur,$ship,$cartQty,$Cbm,$type,$price,$dprice,$onSale)
    {

        $cartonQty=$cartQty;
        
        if($type === 'simple'){
            $regular=$price;
            $sale=$dprice;
            $price=$price;
            $Cbm=$Cbm;
        }
        elseif($type === 'variable'){

            $CbmArr=$Cbm;
            $QtyArr=$cartQty;
            $regularPArr=$price;
            $salePArr=$dprice;
            
            $minQty=min($QtyArr);
            $maxQty=max($QtyArr);
            $minCbm=min($CbmArr);
            $maxCbm=max($CbmArr);
            $minRegPrice=min($regularPArr);
            $maxRegPrice=max($regularPArr);
            $minSalePrice=min($salePArr);
            $maxSalePrice=max($salePArr);
        }

        //get ShipPerc
        $ShipPrecConfig=VueConfig::where('key','ShipPerc')->where('type','main')->first();
        
        //get Currency
        $getCurr=VueConfig::where('key',$cur)->where('type','Currency')->first();
        
        //get Shipment
        $getShipConfig=VueConfig::where('key',$ship)->where('type','shipment')->first();
        

        if(empty($getCurr)){
            $getCurr=VueConfig::where('key','default')->where('type','Currency')->first();
        }

        if(empty($getShipConfig)){
            $getShipConfig=VueConfig::where('key','default')->where('type','shipment')->first();
        }

        

        //$finalShipCost=$shipCost*$shipPerc+$shipCost;
        if($getShipConfig['key'] === 'default'){
            $incresedShipCost=$ShipPrecConfig['value'];
        }else{
            $incresedShipCost=$getShipConfig['value']*$ShipPrecConfig['value']+$getShipConfig['value'];
        }

        //$incresedShipCost=$getShipConfig['value']*$ShipPrecConfig['value']+$getShipConfig['value'];
        


        if($type ==='simple'){

            $CartonShipPrice=(float)$incresedShipCost*(float)$Cbm;
            
            (float)$ProdShipPrice=(float)$CartonShipPrice / (float)$cartonQty;
            $fullPrice=$ProdShipPrice+(float)$regular;
            $saleFullPrice=$ProdShipPrice+(float)$sale;
            $regPriceHtml=$fullPrice*$getCurr['value'];
            $salePriceHtml=$saleFullPrice*$getCurr['value'];
        }
        elseif($type === 'variable'){
            $minCartonShipPrice=(float)$incresedShipCost*$minCbm;
            $maxCartonShipPrice=(float)$incresedShipCost*$maxCbm;
            $minProdShipPrice=(float)$minCartonShipPrice/$minQty;
            $maxProdShipPrice=(float)$maxCartonShipPrice/$maxQty;
            //reg Price
            $minRegFullPrice=$minProdShipPrice+(float)$minRegPrice;
            $maxRegFullPrice=$maxProdShipPrice+(float)$maxRegPrice;
            //sale price
            if($onSale){
                $minSaleFullPrice=$minProdShipPrice+(float)$minSalePrice;
                $maxSaleFullPrice=$maxProdShipPrice+(float)$maxSalePrice;
            }
            else{
                $minSaleFullPrice=null;
                $maxSaleFullPrice=null;
            }
            //xhange price
            $minRegFullPriceHtml=$minRegFullPrice*$getCurr['value'];
            $maxRegFullPriceHtml=$maxRegFullPrice*$getCurr['value'];
            $minSaleFullPriceHtml=$minSaleFullPrice*$getCurr['value'];
            $maxSaleFullPriceHtml=$maxSaleFullPrice*$getCurr['value'];
        }

        //Cur Symbol
        $curHtml=$getCurr['subValue'];

        //Public Html Generate 
        if($type === 'simple'){

            if($onSale){
                $price_html='<span =""><del aria-hidden="true"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>' .number_format((float)$regPriceHtml,2). '</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span> ' . number_format((float)$salePriceHtml,2) . ' </span></ins></span>';
            }
            else{
                $price_html='<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">  '.$curHtml.'  </span>  ' .number_format((float)$regPriceHtml,2)  . '  </span>';
            }
        }
        elseif($type === 'variable'){

            if($onSale){
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

        if($type === 'simple'){

            return [
                'regPriceHtml'=>$regPriceHtml,
                'salePriceHtml'=>$salePriceHtml,
                'price_html'=>$price_html
            ];
        }
        elseif($type === 'variable'){

            return [
                'minRegFullPriceHtml'=>$minRegFullPriceHtml,
                'maxRegFullPriceHtml'=>$maxRegFullPriceHtml,
                'minSaleFullPriceHtml'=>$minSaleFullPriceHtml,
                'maxSaleFullPriceHtml'=>$maxSaleFullPriceHtml,
                'price_html'=>$price_html
            ];
        }
    }

    //
    public function getProdBy($tax,$limit,$cur,$ship,$type,$title,$link,$compType,$compName,$Display,$mobileDisplay)
    {

        if($type ==='tag'){
            //get Relations
            $Tax0=TermRelation::where('term_taxonomy_id',TermTaxonomy::where('term_id',$tax)->pluck('term_taxonomy_id'))->pluck('object_id');
            //get Products
            $ProdBy=Post::whereIn('id',$Tax0)->published()->limit($limit)->get();
        }
        elseif($type ==='newest'){
            $ProdBy=Post::newest()->published()->limit($limit)->get();
        }
        elseif($type ==='offers'){
            $getProdSale=Post::published()->limit(60)->get();
            $ProdBy=$getProdSale->filter(function($model){
                      return $model->on_sale == true;
            })->take($limit)->values();
        }
        elseif($type ==='category'){
            $getCat=Term::where('name',$tax)->first();
            if(!empty($getCat)){
                $CheckCat=TermTaxonomy::where('term_id',$getCat['term_id'])->where('taxonomy','product_cat')->first();
                //get Posts
                $rel=TermRelation::where('term_taxonomy_id',$CheckCat['term_id'])->pluck('object_id');
                $ProdBy=Post::published()->whereIn('post_type',[ 'product','product_variation'])->whereIn('ID',$rel)->limit($limit)->get();
            
            }
            else{
                $ProdBy=null;
            }
        }
        elseif($type === 'search'){

            $ProdBy=Post::published()->where('post_title','like', '%'.$tax.'%' )
            ->limit($limit)->get();
        }
 
        
        //Transform Product Object
        $trans=$ProdBy->map(function($item) use ($tax,$cur,$ship,$limit,$title,$link,$compType,$compName,$Display,$mobileDisplay) {

            if($item['type'] === 'variable'){

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

                $priceX=$this->SetShipCurr($cur,$ship,$QtyArr,$CbmArr,$item['type'],$regularPArr,$salePArr,$item['on_sale']);
            }
            elseif($item['type'] === 'simple'){
                $priceX=$this->SetShipCurr($cur,$ship,$item['cartqty'],$item['cbm'],$item['type'],$item['regular_price'],$item['sale_price'],$item['on_sale']);
            }

            //get Avarage Rate 
            $arr=$item->meta;
            $avgRate =array_search('_wc_average_rating', array_column(json_decode($arr,true), 'meta_key'));

            //get Category

            
            //img 
            if(!empty($item->gall[0]['guid'])){

                $imgMetaArr = $item->gall[0]['meta'];
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
                    'min_qty'=>$item->cartqty,
                    'permalink'=>'https://www.alyaman.com/product/' . $item->post_name,
                    'average_rating'=>$arr[$avgRate]['meta_value'],
                    'short_description'=>$item->post_excerpt,
                    'regular_price'=>$priceX['regPriceHtml'],
                    'sale_price'=>$priceX['salePriceHtml'],
                    'price'=>$priceX['regPriceHtml'],
                    'price_html'=>$priceX['price_html'],
                    'images'=>$imgArr,
                    'Category'=>$item->Categories[0]->term,
                    'created_at'=>$item->post_date
                    // 'meta'=>$item->meta
                ];
            }
            elseif($item['type'] === 'variable'){

                return [
                    'id'=>$item->ID,
                    'type'=>$item['type'],
                    'on_sale'=>$item->on_sale,
                    'name'=>$item->post_title,
                    'min_qty'=>$item->cartqty,
                    'permalink'=>'https://www.alyaman.com/product/' . $item->post_name,
                    'average_rating'=>$arr[$avgRate]['meta_value'],
                    'short_description'=>$item->post_excerpt,
                    'min_regular_price'=>$priceX['minRegFullPriceHtml'],
                    'max_regular_price'=>$priceX['maxRegFullPriceHtml'],
                    'min_sale_price'=>$priceX['minSaleFullPriceHtml'],
                    'max_sale_price'=>$priceX['maxSaleFullPriceHtml'],
                    'price_html'=>$priceX['price_html'],
                    'images'=>$imgArr,
                    'Category'=>$item->Categories[0]->term,
                    'created_at'=>$item->post_date
                    // 'meta'=>$item->meta
                ];
            }


        });

        return ['items'=>$trans,'count'=>$limit,'title'=>$title,'link'=>$link,'type'=>$compType,'value'=>$tax,'name'=>$compName,'Display'=>$Display,'mobileDisplay'=>$mobileDisplay];

    }
   //end Product By 

   public function responseLayout($data,$cur,$ship)
   {



       $arr=array();
       foreach ($data as $item) {

           if($item['compType'] === 'ProdList' ){

               $ProdByTax=$this->getProdBy($item['value'],$item['itemNum'],$cur,$ship,$item['type'],$item['title'],$item['link'],$item['compType'],$item['compName'],$item['Display'],$item['mobileDisplay']);
               array_push($arr,$ProdByTax);

           }
           elseif($item['compType'] === 'banner'){
               $cmpArr=array('items'=>null,'count'=>$item['itemNum'],'title'=>$item['title'],'link'=>$item['link'],'type'=>$item['compType'],'value'=>$item['value'],'name'=>$item['compName'],'Display'=>$item['Display'],'mobileDisplay'=>$item['mobileDisplay']);
               array_push($arr,$cmpArr);
           }
           elseif($item['compType'] === 'ProdInBox'){
               $ProdInBox=$this->getProdBy($item['value'],$item['itemNum'],$cur,$ship,$item['type'],$item['title'],$item['link'],$item['compType'],$item['compName'],$item['Display'],$item['mobileDisplay']);
               array_push($arr,$ProdInBox);
           }
       }

       return $arr;
   }


   public function getProds($cur,$ship)
   {

       set_time_limit(0);

       //get Products By Tag id
    
       $response=array();

       //Get Components Desktop
       $getLayoutsLists=VueLayouts::where('wherePage','landing')->where('Display','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sort','asc')->get();
       $DesktopResponse=$this->responseLayout($getLayoutsLists,$cur,$ship);
       $getLayoutsListsMobile=VueLayouts::where('wherePage','landing')->where('mobileDisplay','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sortMobile','asc')->get();
       $mobileResponse=$this->responseLayout($getLayoutsListsMobile,$cur,$ship);
       $ProdInBox=VueLayouts::where('wherePage','landing')->where('Display','!=','hide')->where('compType','ProdInBox')->orderBy('sort','desc')->get();           
       $ProdInBoxResponse=$this->responseLayout($ProdInBox,$cur,$ship);

       $response['desktop'] = $DesktopResponse;
       $response['mobile'] = $mobileResponse;
       $response['ProdInBox'] = $ProdInBoxResponse;

       return response()->json($response, 200);
   }

   public function ProdOne($Name,$cur,$ship){

       if(!empty($Name)){

           //Check Product
               //Check Product By Product TYpe
               $postTypes=[ 'product','product_variation'];
               $CheckProd=PostAll::where('post_title',$Name)->whereIn('post_type',$postTypes)->first();

           if(isset($CheckProd)){
               
               $item= $CheckProd;
               //Set Shipment And Curr 
               if($item['type'] === 'variable'){

                   $VarProd= PostV::where('post_parent',$item['ID'])->where('post_type','product_variation')->get();
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
   
                   $priceX=$this->SetShipCurr($cur,$ship,$QtyArr,$CbmArr,$item['type'],$regularPArr,$salePArr,$item['on_sale']);
               }
               elseif($item['type'] === 'simple'){
                   $priceX=$this->SetShipCurr($cur,$ship,$item['cartqty'],$item['cbm'],$item['type'],$item['regular_price'],$item['sale_price'],$item['on_sale']);
               }

               if($item['type'] === 'simple'){

                   $transProdOne= [
                       'id'=>$item->ID,
                       'type'=>$item['type'],
                       'on_sale'=>$item->on_sale,
                       'avg_rate'=>$item->avg_rate,
                       'min_qty'=>$item->min_qty,
                       'name'=>$item->post_title,
                       'permalink'=>'https://www.alyaman.com/product/' . $item->post_name,
                       //'average_rating'=>$arr[$avgRate]['meta_value'],
                       'short_description'=>$item->post_excerpt,
                       'regular_price'=>$priceX['regPriceHtml'],
                       'sale_price'=>$priceX['salePriceHtml'],
                       'price'=>$priceX['regPriceHtml'],
                       'price_html'=>$priceX['price_html'],
                       'gallery'=>$item['gallery'],
                       'meta'=>$item->meta
                   ];
               }
               elseif($item['type'] === 'variable'){
                   
                   $transProdOne= [
                       'id'=>$item->ID,
                       'type'=>$item['type'],
                       'on_sale'=>$item->on_sale,
                       'name'=>$item->post_title,
                       'permalink'=>'https://www.alyaman.com/product/' . $item->post_name,
                       'avg_rate'=>$item->avg_rate,
                       'short_description'=>$item->post_excerpt,
                       'min_regular_price'=>$priceX['minRegFullPriceHtml'],
                       'max_regular_price'=>$priceX['maxRegFullPriceHtml'],
                       'min_sale_price'=>$priceX['minSaleFullPriceHtml'],
                       'max_sale_price'=>$priceX['maxSaleFullPriceHtml'],
                       'price_html'=>$priceX['price_html'],
                       'gallery'=>$item['gallery'],
                       'variations'=>$VarProd,
                       'meta'=>$item->meta
                   ];
               }

               

               return $transProdOne;
           }
           else{
               $ProdBy=null;
           }
       }
       else{
               $ProdBy=null;
       } 
   }

   public function ProdByCat($cat,$cur,$ship)
   {

       //get Products By Category 
       if(!empty($cat)){

           //Check & get Category 
               //Check Category
                   // $CheckCat=TermTaxonomy::where('term_id',$getCat['term_id'])->where('taxonomy','product_cat')->first();
           $getPosts=$this->getProdBy($cat,12,$cur,$ship,'category','','','ProdList','ProdByCatList','list','list');

       }
       else{
           $getPosts=null;
       }
       
       //get Prod By Cat layout 
       $getLayoutsLists=VueLayouts::where('wherePage','ProdByCat')->where('Display','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sort','asc')->get();
       $res=$this->responseLayout($getLayoutsLists,$cur,$ship);
       $getLayoutsListsMobile=VueLayouts::where('wherePage','ProdByCat')->where('Display','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sortMobile','asc')->get();
       $res2=$this->responseLayout($getLayoutsListsMobile,$cur,$ship);

       //response
       $response=array();
       $response['category']=$getPosts;
       $response['desktop']=$res;
       $response['mobile']=$res2;

       return response()->json($response, 200);

   }



   public function ProdByTag($tag,$cur,$ship)
   {

       if(!empty($tag)){
           
           $getTag=VueLayouts::where('type','tag')->where('title',$tag)->first();
           $getPosts=$this->getProdBy($getTag['value'],12,$cur,$ship,'tag','','','ProdList','ProdByCatList','list','list');
       }
       else{
           $getPosts=null;
       }

       $getLayoutsLists=VueLayouts::where('wherePage','ProdByTag')->where('Display','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sort','asc')->get();
       $res=$this->responseLayout($getLayoutsLists,$cur,$ship);
       $getLayoutsListsMobile=VueLayouts::where('wherePage','ProdByTag')->where('Display','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sortMobile','asc')->get();
       $res2=$this->responseLayout($getLayoutsListsMobile,$cur,$ship);


       $response['tag']=$getPosts;
       $response['desktop']=$res;
       $response['mobile']=$res2;


       return response()->json($response, 200);
   }


   public function Search($value,$sort,$limit,$minprice,$maxprice,$filter,$rate,$cur,$ship)
   {

       $init=$this->getProdBy($value,$limit,$cur,$ship,'search','','','','','','');
       $posts=$init['items'];
       $posts=$posts->toArray();
       //Check filtter
       switch ($filter) {
           case 'PriceHighToLow':
               
               // Desc sort
               usort($posts,function($first,$second){
                   return $first['price'] < $second['price'] ? 1 : -1;
               });
               

               break;
           case 'PriceLowToHigh':

               //Asc Sort
               usort($posts,function($first,$second){
                   return $first['price'] > $second['price'] ? 1 : -1;
               });

               break;
           case 'Recommendations':
               
               //get recommendations
               


               break;
           case 'RatingHighToLow':

               usort($posts,function($first,$second){
                   return $first['avg_rate'] < $second['avg_rate'] ? 1 : -1;
               });

               break;
           case 'RatingLowToHigh':
               usort($posts,function($first,$second){
                   return $first['avg_rate'] > $second['avg_rate'] ? 1 : -1;
               });
               break;
           case 'HasSale':

               $hasSale=array_filter($posts,function($items){
                   return $items['on_sale'] == true;
               });

               $posts=$hasSale;

               break;  
           case 'New':
               
               usort($posts,function($first,$second){
                   return $first['created_at'] < $second['created_at'] ? 1 : -1;
               });

               break;
       }

       //min & max price range & rate filter
       $filterdPosts= array_filter($posts,function($item) use($minprice,$maxprice,$rate)  {

           if($item['type'] === 'variable'){
               return $item['min_regular_price'] > $minprice && $item['min_regular_price'] <= $maxprice;
           }
           elseif($item['type'] === 'simple'){
               return $item['price'] > $minprice && $item['price'] <= $maxprice && $item['average_rating'] <= $rate  ;
           }

       });

       $postsArr=array();

       foreach ($filterdPosts as $value) {
         array_push($postsArr,$value);
       }

       return response()->json([
           'status'=>true,
           'items'=>$postsArr
       ], 200);
   }


   
    //
    public function getTerms($cur,$ship)
    {
        //getCategories
        $getCat=TermTaxonomy::where('taxonomy','product_cat')->where('parent',0)->pluck('term_id');
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
                'image'=>(object)array('src'=>$img),
                'sub'=>$item->taxonomy->sub
            ];

        });


        //getTags
        $getTerm=TermTaxonomy::where('taxonomy','product_tag')->where('parent',0)->pluck('term_id');
        $getTermTag=Term::whereIn('term_id',$getTerm)->get();
        $transTag=$getTermTag->map(function($item){

            if(!empty($item->taxonomy->image->guid)){
                $img=$item->taxonomy->image->guid;
            }
            else{
                $img='';
            }
            return [
                'name'=>$item->name,
                'slug'=>$item->slug,
                'image'=>(object)array('src'=>$img),
                'sub'=>$item->taxonomy->sub
            ];
        });

        $response=array();

        //Get Components Desktop
        $getLayoutsLists=VueLayouts::where('wherePage','AllCat')->where('Display','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sort','asc')->get();
        $DesktopResponse=$this->responseLayout($getLayoutsLists,$cur,$ship);
       //Get Components Mobile
        $getLayoutsListsMobile=VueLayouts::where('wherePage','AllCat')->where('mobileDisplay','!=','hide')->where('compType','!=','ProdInBox')->orderBy('sortMobile','asc')->get();
        $mobileResponse=$this->responseLayout($getLayoutsListsMobile,$cur,$ship);
        
        $response['desktop'] = $DesktopResponse;
        $response['mobile'] = $mobileResponse;
        $response['Categories'] = $transCat;
        $response['Tags'] = $transTag;

        return response()->json(['status'=>true,'items'=>$response], 200);

        // return response()->json($transCat, 200);
    } 


}
