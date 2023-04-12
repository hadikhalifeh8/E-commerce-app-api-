<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\address;
use App\Models\cart;
use App\Models\couponModel;
use App\Models\ordersModel;
use App\Models\User;
use Illuminate\Http\Request;

class ordersController extends Controller
{

 // CheckOut order
    public function addorder(Request $request)
    {
        $order = new ordersModel();
        $order->user_id = $request->user_id;
        $order->address_id = $request->address_id;
        $order->used_coupon = $request->used_coupon;

        $order->order_type = $request->order_type;
        $order->payment_method = $request->payment_method;

        $order->order_price_delivery = $request->order_price_delivery;
        $order->order_price = $request->order_price;

              // total_price
      
              $now = date("Y-m-d H:i:s");
        $couponcheck = couponModel::where('id',$request->used_coupon)
                                  ->where('expiry_date','>',$now)
                                  ->where('count','>',0)
                                  ->get();
            // if($couponcheck->isNotEmpty()){
            //    $order->total_price = ($request->order_price - $request->order_price * $request->coupon_discount / 100);
            //    couponModel::where('id',$request->used_coupon)->decrement('count'); // delete / minus count
               
            // }else{ //coupon إذا مش مستعمل 
            //     $order->total_price = $request->order_price_delivery + $request->order_price;
            // }
            
            // if($request->order_type !=0 && $request->used_coupon){
            //     $order->total_price =  $request->order_price;

            // }

// if used coupon code && order type = 0 (get the order delivery)
            if($couponcheck->isNotEmpty() && $order->order_type ==0){
                //return "used coupon and order type =0";
                $order->total_price = ($request->order_price - $request->order_price * $request->coupon_discount / 100) + $request->order_price_delivery;
                couponModel::where('id',$request->used_coupon)->decrement('count'); // delete / minus count
            

    // if used coupon code && order type = 1 (get the order by my car)    
            }elseif($couponcheck->isNotEmpty() && $order->order_type ==1){ //coupon إذا مش مستعمل 
               // return "used coupon and order type =1";
     $order->total_price = ($request->order_price - $request->order_price * $request->coupon_discount / 100);
                couponModel::where('id',$request->used_coupon)->decrement('count'); // delete / minus count
            }


// if Not used of coupon code && order type = 0 (get the order by delivery)
             if($couponcheck->isEmpty() && $request->order_type ==0){
               // return " Not used coupon and order type =0";
        $order->total_price =  $request->order_price + $request->order_price_delivery;


// if Not used of coupon code && order type = 1 (get the order by my car )
             }elseif($couponcheck->isEmpty() && $request->order_type ==1){
                // return " Not used coupon and order type =1";
                         $order->total_price =  $request->order_price ;
                  }

        $order->coupon_discount = $request->coupon_discount;

        $order->order_date = now();
        $order->status = $request->status;
     

        $order->save();

        // update order in cart
        if($order){
            $carts_order = cart::where('users_id',$request->user_id)
                                   ->update(['order_id'=>$order->id]);
          
            return response()->json([
                'status' => 'success',
                'data' => $carts_order,
                ]);
        }else{
            return response()->json([
                               'status' => 'failure',
                                'data' => 'No Data Inserted',
                            ]);
        }
    }


    public function viewPendingOrder($usersid)
    {
        $users = User::find($usersid);
       
 
       if(!( $users)) {
                     return response()->json([
                            'status' => 'failure',
                            'data' => 'Not found this userid ' . $usersid  ,
                  ]);
                } 
            $view_orders = ordersModel::where('user_id',$usersid)->get();  
            
      if($view_orders->isNotEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $view_orders,
                    ]);
            }else{
                return response()->json([
                                   'status' => 'failure',
                                    'data' => 'No Data Inserted',
                                ]);
            }

             
               
                

    }



}



