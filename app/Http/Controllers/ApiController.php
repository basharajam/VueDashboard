<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\PostV;
use App\Models\TermTaxonomy;
use App\Models\TermRelation;
use App\Models\Term;
use App\Models\PostAll;
use App\Models\VueLayouts;
use App\Models\WpUser;
use App\Models\otp;
use App\Models\rating;

class ApiController extends Controller
{


    public function getCategories($cur,$ship)
    {
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

        return response()->json($response, 200);

        // return response()->json($transCat, 200);
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
        
        //Transform Product Object
        $trans=$ProdBy->map(function($item) use ($tax,$cur,$ship,$limit,$title,$link,$compType,$compName,$Display,$mobileDisplay) {
            
            //return $item;

            $curArr=array(
                'AED'=>array(
                    'exhange'=>0.57,
                    'increasedShipCost'=>$ship,
                    'symbol'=>'??.??'
                ),
                'SAR'=>array(
                    'exhange'=>0.58,
                    'increasedShipCost'=>$ship,
                    'symbol'=>'??.??'
                ),
                'OMR'=>array(
                    'exhange'=>0.06,
                    'increasedShipCost'=>$ship,
                    'symbol'=>'??.??'
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
                    'symbol'=>'??.??' //AED
                ),
                'CNY'=>array(
                    'exhange'=>1,
                    'increasedShipCost'=>$ship,
                    'symbol'=>'??'
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

        return ['items'=>$trans,'count'=>$limit,'title'=>$title,'link'=>$link,'type'=>$compType,'value'=>$tax,'name'=>$compName,'Display'=>$Display,'mobileDisplay'=>$mobileDisplay];

    }
   //end Product By 

   public function responseLayout($data,$cur,$ship)
   {
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

       $arr=array();
       foreach ($data as $item) {

           if($item['compType'] === 'ProdList' ){

               $ProdByTax=$this->getProdBy($item['value'],$item['itemNum'],$cur,$finalShipCost,$item['type'],$item['title'],$item['link'],$item['compType'],$item['compName'],$item['Display'],$item['mobileDisplay']);
               array_push($arr,$ProdByTax);

           }
           elseif($item['compType'] === 'banner'){
               $cmpArr=array('items'=>null,'count'=>$item['itemNum'],'title'=>$item['title'],'link'=>$item['link'],'type'=>$item['compType'],'value'=>$item['value'],'name'=>$item['compName'],'Display'=>$item['Display'],'mobileDisplay'=>$item['mobileDisplay']);
               array_push($arr,$cmpArr);
           }
           elseif($item['compType'] === 'ProdInBox'){
               $ProdInBox=$this->getProdBy($item['value'],$item['itemNum'],$cur,$finalShipCost,$item['type'],$item['title'],$item['link'],$item['compType'],$item['compName'],$item['Display'],$item['mobileDisplay']);
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




    public function test()
    {
       // $getProds=Post::newest()->->limit(1)->get();

        $offers=Post::where('on_sale',true)->limit(1)->get();
        //$getProds->categories;
        return response()->json($offers, 200);
    }


    public function validateCreds($type,$value)
    {
        //validate params 
        if(!empty($type) && !empty($value)){

            //Check Type 
            if($type === 'mail'){

                //Check value
                $Check=WpUser::where('user_email',$value)->count();
            }
            elseif($type ==='username'){

                //check value
                $Check=WpUser::where('user_login',$value)->count();
            }
            elseif($type === 'number'){

                //check value 
                $Check=otp::where('mobileno',$value)->count();
            }

            if($Check > 0 ){
                return response()->json(['success'=>false,'message'=>'Exists'], 400);
            }
            else{
                return response()->json(['success'=>true,'message'=>'Done'], 200);
            }

        }
        else{
            return response()->json(['success'=>false,'message'=>'validate'], 400);
        }
    }

    
    public function ProdOne($cat,$id,$cur,$ship)
    {

        
        //validate params
        if(!empty($cat) && !empty($id)){

            //Check Category 
                //get Id Of Category 
                $getCat=Term::where('name',$cat)->first();
                
                //Check Category
                if(!empty($getCat)){
                    $CheckCat=TermTaxonomy::where('term_id',$getCat['term_id'])->where('taxonomy','product_cat')->first();
                }
                else{
                    $CheckCat=null;
                }


            //Check Product

                //Check Product By Product TYpe
                $postTypes=[ 'product','product_variation'];
                $CheckProd=PostAll::where('ID',$id)->whereIn('post_type',$postTypes)->first();
                
            
            if(isset($CheckCat) && isset($CheckProd)){
                return $CheckProd;
            }
            else{
                 return response()->json(null, 403);
            }
        }
        else{
            return response()->json(null, 403);
        }


        //Check Relation Between Category And Product 

        //Set Shipment And Price 

        //Response
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

    public function SaveRate(Request $request)
    {

        //validate inputs 
        $validate = Validator::make(request()->all(), [
            'CompNameI'=>'required',
            'CompDescI'=>'required',
            'RateValI'=>"required",
        ]);

        if ($validate->fails()) {
            return response()->json(['code'=>400,'message'=>'Validation Error','status'=>false,'item'=>null],400);
        }

        //comp_name	value	desc	user_id

        //Save Rate
        $SaveRate=new rating();
        $SaveRate->comp_name=$request->input('CompNameI');
        $SaveRate->value=$request->input('RateValI');
        $SaveRate->desc=$request->input('CompDescI');

        $rate=$SaveRate->save();

        return response()->json($SaveRate, 201);

    }

    public function getRate()
    {
        //get Rates
        $getRates=rating::all();

        return response()->json($getRates, 200);

    }


}
