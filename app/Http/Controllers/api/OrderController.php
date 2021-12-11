<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\Order;
use App\Models\OrderItem;


use App\Facades;
use Auth;


class OrderController extends Controller
{
    //
    public function GetOrder($status)
    {

        //validate status 
        if(!empty($status) ){

            //get User 
            $user=Auth::guard('api')->user();
            
            //get Orders By User & Status
            if($status === 'completed'){
                $getOrders=Order::where('post_author',$user->ID)->completed()->get();
            }
            elseif($status === 'cancelld'){
                $getOrders=Order::where('post_author',$user->ID)->cancelled()->get();
            }
            elseif($status === 'failed'){
                $getOrders=Order::where('post_author',$user->ID)->failed()->get();
            }
            elseif($status === 'onHold'){
                $getOrders=Order::where('post_author',$user->ID)->onHold()->get();
            }
            elseif($status === 'pending'){
                $getOrders=Order::where('post_author',$user->ID)->pending()->get();
            }
            elseif($status === 'refunded'){
                $getOrders=Order::where('post_author',$user->ID)->refunded()->get();
            }
            elseif($status === 'processing'){
                $getOrders=Order::where('post_author',$user->ID)->pending()->get();
            }
            elseif($status === 'all'){
                $getOrders=Order::where('post_author',$user->ID)->get();
            }
            else{
                return 'Worng Status';
            }

            $getOrders->load('items');
            $getOrders->load('customer');

            return response()->json(['status'=>true,'items'=>$getOrders], 200,);

            //return $getOrders;
            
        }

    }

    public function SaveOrderPP(Request $request)
    {

        //Validate Inputs 


        //tran_id
        $transId=$request->input('trans_id');

        //check items 
        $ItemsArr=$request->input('Items');

        //Full Price
        $FullPrice=$request->input('FullPrice');

        //get user
        $user=Auth::guard('api')->user();

        //Save Order 
        $SaveOrder=new Order;
        $SaveOrder->post_author=$user->ID;
        $SaveOrder->post_date=Carbon::now();
        $SaveOrder->post_date_gmt=Carbon::now('UTC');
        $SaveOrder->post_content= ' ';
        $SaveOrder->post_title = 'Order &ndash;  '. Carbon::now();
        $SaveOrder->post_excerpt= 'Saved From Api';
        /////////////
        //OrderType
        //
        //   'cancelled',
        //   'completed',
        //   'failed',
        //   'on-hold',
        //   'pending',
        //   'processing',
        //   'refunded',
        //
        //////////////
        $SaveOrder->post_status='wc-on-hold';
        $SaveOrder->comment_status='open';
        $SaveOrder->ping_status='closed';
        $SaveOrder->post_password='wc_order_hasd1231';
        $SaveOrder->post_name='Order &ndash;  '. Carbon::now();
        $SaveOrder->to_ping = '  ';
        $SaveOrder->pinged= ' ';
        $SaveOrder->post_modified=Carbon::now();
        $SaveOrder->post_modified_gmt= Carbon::now('UTC');
        $SaveOrder->post_content_filtered = ' ';
        $SaveOrder->post_parent=0;
        $SaveOrder->guid = 'http://www.test.com';
        $SaveOrder->menu_order=0;
        $SaveOrder->post_type='shop_order';
        $SaveOrder->post_mime_type=' ';
        $SaveOrder->comment_count=0;

        //Save Order Meta`s
        $SaveOrder->save();

        //set order address
        $SaveOrder->saveField('_billing_first_name','Blaxk');
        $SaveOrder->saveField('_billing_last_name','Blaxk Last');
        $SaveOrder->saveField('_billing_address_1','Blaxk Address');
        $SaveOrder->saveField('_billing_city','Blaxk Order City');
        $SaveOrder->saveField('_billing_country','Blaxk Order Country');
        $SaveOrder->saveField('_billing_address_index','Blaxk');

        //set Order user Inf
        $SaveOrder->saveField('_billing_email','Blaxk Order Mail');
        $SaveOrder->saveField('_billing_phone','Blaxk Order Phone');

        //set Shipment Address 
        $SaveOrder->saveField('_shipping_first_name','Blaxk Ship First Name');
        $SaveOrder->saveField('_shipping_last_name','Blaxk Ship last name');
        $SaveOrder->saveField('_shipping_address_1','Blaxk Ship Address ');
        $SaveOrder->saveField('_shipping_city','Blaxk Ship City');
        $SaveOrder->saveField('_shipping_country','Blaxk Ship Country');
        $SaveOrder->saveField('_shipping_address_index','Blaxk');
        
        //set payment
        //   _payment_method bacs paypal
        //   _payment_method_title PayPal, حوالة بنكية مباشرة 
        $SaveOrder->saveField('_payment_method','paypal');
        $SaveOrder->saveField('_payment_method_title','PayPal');
        $SaveOrder->saveField('_transaction_id',$transId);
        
        

        //set discount
        $SaveOrder->saveField('_cart_discount',0);
        $SaveOrder->saveField('_cart_discount_tax',0);

        //set order shipping
        $SaveOrder->saveField('_order_shipping',0);
        $SaveOrder->saveField('_order_shipping_tax',0);
        $SaveOrder->saveField('_order_tax',0);
        $SaveOrder->saveField('_order_currency','USD');

        //set main order mata`s
        $SaveOrder->saveField('_customer_user',318);
        $SaveOrder->saveField('_order_currency','USD');
        $SaveOrder->saveField('is_vat_exempt','USD');
        $SaveOrder->saveField('_order_total',454);

        //set order items
        $OrderItems = $SaveOrder->items;

        foreach ($ItemsArr as $itemF) {
            
            # code...
            $item=$itemF['item'];
            $Saveitem = new OrderItem();
            $Saveitem->order_id = $SaveOrder->ID;
            $Saveitem->order_item_name =$item['name'];;
            $Saveitem->order_item_type ="line_item";
            $Saveitem->save();

            if($item['type'] === 'variable'){
                $price=$item['min_regular_price'];
            }
            elseif($item['type'] === 'simple'){
                $price=$item['price'];
            }

            $Saveitem->createMeta (['_qty'=>$itemF['qty'],'_product_id'=>$item['id'],'_line_subtotal'=>$price]);
            $OrderItems->add($Saveitem);
        }

        return $SaveOrder;

    
        //get & Save Items
        

        // _order_key
        // _customer_user
        // _payment_method
        // _payment_method_title
        // _customer_ip_address
        // _customer_user_agent

        // _created_via
        // _cart_hash
        // _billing_first_name
        // _billing_last_name
        // _billing_address_1
        // _billing_city
        // _billing_country
        // _billing_email
        // _billing_phone


        // _shipping_first_name
        // _shipping_last_name
        // _shipping_address_1
        // _shipping_city
        // _shipping_country


        // _order_currency
        // _cart_discount
        // _cart_discount_tax
        // _order_shipping
        // _order_shipping_tax
        // _order_tax
        // _order_total
        // _order_version


        // _billing_address_index
        // _shipping_address_index

        // is_vat_exempt

        //return $request->all();
    }

    public function SaveOrderBcs(Request $request)
    {
        //validate inputs 

        //return $request->all();

        //get user id
        $user=Auth::guard('api')->user();

        //check items 
        $ItemsArr=$request->input('Items');

        //Full Price
        $FullPrice=$request->input('FullPrice');


        //Save Order 
        $SaveOrder=new Order;
        $SaveOrder->post_author=$user->ID;
        $SaveOrder->post_date=Carbon::now();
        $SaveOrder->post_date_gmt=Carbon::now('UTC');
        $SaveOrder->post_content= ' ';
        $SaveOrder->post_title = 'Order &ndash;  '. Carbon::now();
        $SaveOrder->post_excerpt= 'Saved From Api';
        /////////////
        //OrderStatus
        //
        //   'cancelled',
        //   'completed',
        //   'failed',
        //   'on-hold',
        //   'pending',
        //   'processing',
        //   'refunded',
        //
        //////////////
        $SaveOrder->post_status='wc-on-hold';
        $SaveOrder->comment_status='open';
        $SaveOrder->ping_status='closed';
        $SaveOrder->post_password='wc_order_hasd1231';
        $SaveOrder->post_name='Order &ndash;  '. Carbon::now();
        $SaveOrder->to_ping = '  ';
        $SaveOrder->pinged= ' ';
        $SaveOrder->post_modified=Carbon::now();
        $SaveOrder->post_modified_gmt= Carbon::now('UTC');
        $SaveOrder->post_content_filtered = ' ';
        $SaveOrder->post_parent=0;
        $SaveOrder->guid = 'http://www.test.com';
        $SaveOrder->menu_order=0;
        $SaveOrder->post_type='shop_order';
        $SaveOrder->post_mime_type=' ';
        $SaveOrder->comment_count=0;

        //Save Order Meta`s
        $SaveOrder->save();

        // ID: 324
        // avatar: "//secure.gravatar.com/avatar/6d0732df28ee8d6d52c1bed75ab30377?d=mm"
        // billing_address_1: "Billing Address Updated From Billing Form"
        // billing_address_2: "Billing Addres 2 Updated From Billing Form"
        // billing_first_name: "Updated Shipment First name"
        // billing_last_name: "Updated Shipment Last name"
        // created_at: "2021-11-07T11:35:46.000000Z"
        // deleted: 0
        // description: "Updated Desription"
        // display_name: "blaxk blaxk"
        // email: "deadman1002014@blaxk.cc"
        // first_name: "Updated Shipment First name"
        // last_name: "Updated Shipment Last name"
        // login: "deadman100204"
        // meta: [{umeta_id: 6198, user_id: 324, meta_key: "nickname", meta_value: "Blaxk", value: "Blaxk"},…]
        // nickname: "Blaxk"
        // shipping: "ttt"
        // shipping_address_1: "xxxxxxxxxxxxxxxxxxxxxxxxx"
        // shipping_address_2: "9663"
        // shipping_city: null
        // shipping_first_name: "Updated Shipment First name"
        // shipping_last_name: "Updated Shipment Last name"
        // slug: "blaxk"
        // spam: 0
        // url: null
        // user_activation_key: null
        // user_email: "deadman1002014@blaxk.cc"
        // user_login: "deadman100204"
        // user_nicename: "blaxk"
        // user_registered: "2021-11-07T11:35:46.000000Z"
        // user_status: 0
        // user_url: null
        // form: {FirstNameI: "Updated Shipment First name", LastNameI: "Updated Shipment Last name",…}
        // BillingAddressI: "Billing Address Updated From Billing Form"
        // FirstNameI: "Updated Shipment First name"
        // LastNameI: "Updated Shipment Last name"
        // MailI: "deadman1002014@blaxk.cc"
        // OrderCountryI: "LB"
        // OrderZipI: "0036"
        // ShipmentAddressI: "xxxxxxxxxxxxxxxxxxxxxxxxx"

        $form=$request->input('form');
        $OrderUser=$request->input('User');


        //set order address
        $SaveOrder->saveField('_billing_first_name',$form['FirstNameI']);
        $SaveOrder->saveField('_billing_last_name',$form['LastNameI']);
        $SaveOrder->saveField('_billing_address_1',$form['BillingAddressI']);
        $SaveOrder->saveField('_billing_address_2',$OrderUser['billing_address_2']);
        $SaveOrder->saveField('_billing_city','');
        $SaveOrder->saveField('_billing_country',$form['OrderCountryI']);
        $SaveOrder->saveField('_billing_address_index',$form['BillingAddressI']);

        //set Order user Inf
        $SaveOrder->saveField('_billing_email',$form['MailI']);
        $SaveOrder->saveField('_billing_phone','');

        //set Shipment Address 
        $SaveOrder->saveField('_shipping_first_name','Blaxk Ship First Name');
        $SaveOrder->saveField('_shipping_last_name','Blaxk Ship last name');
        $SaveOrder->saveField('_shipping_address_1',$form['ShipmentAddressI']);
        $SaveOrder->saveField('_shipping_city','');
        $SaveOrder->saveField('_shipping_country',$form['OrderCountryI']);
        $SaveOrder->saveField('_shipping_address_index',$form['ShipmentAddressI']);
        
        //set payment
        //   _payment_method bacs paypal
        //   _payment_method_title PayPal, حوالة بنكية مباشرة 
        $SaveOrder->saveField('_payment_method','bacs');
        $SaveOrder->saveField('_payment_method_title','حوالة بنكية مباشرة ');        

        //set discount
        $SaveOrder->saveField('_cart_discount',0);
        $SaveOrder->saveField('_cart_discount_tax',0);

        //set order shipping
        $SaveOrder->saveField('_order_shipping',0);
        $SaveOrder->saveField('_order_shipping_tax',0);
        $SaveOrder->saveField('_order_tax',0);
        $SaveOrder->saveField('_order_currency','USD');

        //set main order mata`s
        $SaveOrder->saveField('_customer_user',$OrderUser['ID']);
        $SaveOrder->saveField('_order_currency',$request->input('Curr'));
        $SaveOrder->saveField('is_vat_exempt',$request->input('Curr'));
        $SaveOrder->saveField('_order_total',$request->input('FullPrice'));

        //set order items
        $OrderItems = $SaveOrder->items;

        foreach ($ItemsArr as $itemF) {
            
            # code...
            $item=$itemF['item'];
            $Saveitem = new OrderItem();
            $Saveitem->order_id = $SaveOrder->ID;
            $Saveitem->order_item_name =$item['name'];;
            $Saveitem->order_item_type ="line_item";
            $Saveitem->save();

            if($item['type'] === 'variable'){
                $price=$item['min_regular_price'];
            }
            elseif($item['type'] === 'simple'){
                $price=$item['price'];
            }

            $Saveitem->createMeta (['_qty'=>$itemF['qty'],'_product_id'=>$item['id'],'_line_subtotal'=>$price]);
            $OrderItems->add($Saveitem);
        }

        return response()->json(['success'=>true,'message'=>'order Successfully created','items'=>$SaveOrder], 201);
        //return $SaveOrder;      

    }
}
