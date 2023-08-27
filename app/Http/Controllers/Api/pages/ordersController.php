<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\address;
use App\Models\cart;
use App\Models\couponModel;
use App\Models\ordersModel;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\pages\PushNotificationController;

use App\Models\delivery_users;

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
      
              $now = date("Y-m-d H:i:s A");
            //   $now = date("l jS \of F Y h:i:s A");
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
            $view_orders = ordersModel::where('user_id',$usersid)
                           ->where('status','!=','4') //lam tasel ba3d ela mar7alet l archive
                           ->with('address_rltn')
                           ->get();  
            
      if($view_orders->isNotEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $view_orders,
                    ]);
            }else{
                return response()->json([
                                   'status' => 'failure',
                                    'data' => 'No Pending Orders Data Found  ',
                                ]);
            }
    }

 
    // get the orders by cart
    public function detailsOrder($orderid)
    {
        
       $cart = cart::where('order_id',$orderid)
                     ->with('user_rltn')
                     ->with('item_rltn')
                     ->with('order_rltn')
              ->get();

       if($cart) {
        return response()->json([
               'status' => 'success',
               'data' => $cart ,
               
     ]);
   }
    }


  // delete the order where status =>Await Approve (Only)   
  public function deleteOrder($orderid)
  {
    $orders = ordersModel::find($orderid);
       
 
    if(!( $orders)) {
                  return response()->json([
                         'status' => 'failure',
                         'data' => 'Not found this order Number ' . $orderid  ,
               ]);
             }
             
        $deleteOrder = ordersModel::where('id', $orderid)->where('status',0)->delete('id');

        if($deleteOrder) {
          return response()->json([
                 'status' => 'success',
                 'data' => $deleteOrder ,
       ]);
     }else{
      
      
        return response()->json([
               'status' => 'failure',
               'data' => "Order Not Deleted becouse the Status is not in Awit Approved" ,
     ]);
   
     }

  }

// archive order wher status = 4 , archive تم إستلام الطلبيه
  public function archiveOrder($usersid)
  {
    $users = User::find($usersid);
       
 
    if(!( $users)) {
                  return response()->json([
                         'status' => 'failure',
                         'data' => 'Not found this userid ' . $usersid  ,
               ]);
             } 
         $archive_orders = ordersModel::where('user_id',$usersid)
                        ->where('status',4)
                        ->with('address_rltn')
                        ->get();  
         
   if($archive_orders->isNotEmpty()) {
             return response()->json([
                 'status' => 'success',
                 'data' => $archive_orders,
                 ]);
         }else{
             return response()->json([
                                'status' => 'failure',
                                 'data' => 'No Archive Orders Data Found',
                             ]);
         }
  }



    ////////////////////////////////// ADMIN /////////////////////////////////////

    // in flutter function fbcmConfig.dart;
    // to get notification to user when the admin approved for the order
  public function approvedOrder($orderid, $userid) 
  {
    $orders = ordersModel::find($orderid);
    $users = User::find($userid);

       
 
    if(!( $orders && $users)) {
                  return response()->json([
                         'status' => 'failure',
                         'data' => 'Not found this order-id ' . $orderid. 'this user-id ' .$userid   ,
               ]);
             }

     $approveorder = ordersModel::where('id',$orderid)
                                ->where('user_id',$userid) 
                               // ->where('status',0)
                                  ->update(['status'=> 1]);      

        //   $result = (new TestController)->exampleFunction();  
        // $notification = (new PushNotificationController)->bulksend(
        //      $userid,
        //      "success", //PushNotification(bulksend) لي بال  requset نفس ال 
        //      "The Order has been Approved", //PushNotification(bulksend) لي بال  requset نفس ال 
        //      "users$userid", 
        //      "",
        //      "refreshorderpending"

        // );
            
           
        if($approveorder) {
            return response()->json([
                   'status' => 'success',
                  
                    'data' => $approveorder ,
                  // 'datas' => $userid
         ]);
       } 


        }



 // get all orders for all users where status =0
public function viewPendingOrderToAdmin()
{
            
                $view_orders = ordersModel::
                                            // where('status','!=','4') 
                                           where('status',0)        
                                          ->with('address_rltn')
                                          ->get();  
                
          if($view_orders->isNotEmpty()) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $view_orders,
                        ]);
                }else{
                    return response()->json([
                                       'status' => 'failure',
                                        'data' => 'No Pending Orders Data Found  ',
                                    ]);
                }
}


// get all orders where status !=0 (not pending), and status !=4 (not done)
public function viewAcceptedOrdersToAdmin()
{

           $accepted_orders = ordersModel::
                                       where('status','!=','0') 
                                       ->where('status','!=','4')        
                                       ->with('address_rltn')
                                       ->get();  

               if($accepted_orders->isNotEmpty()) {
                           return response()->json([
                                      'status' => 'success',
                                      'data' => $accepted_orders,
                           ]);
                  }else{
                         return response()->json([
                         'status' => 'failure',
                         'data' => 'No Accepted Orders Data Found  ',
                       ]);
              }
}




public function preparedOrdersShow($orderid, $userid) 
{
  $orders = ordersModel::find($orderid);
  $users = User::find($userid); // to send notification
  // $delivery = delivery_users::find($deliveryid); // to send notification

     

  if(!( $orders && $users)) {
                return response()->json([
                       'status' => 'failure',
                       'data' => 'Not found this order-id ' . $orderid. 'this user-id ' .$userid   ,
             ]);
           }

           if(delivery_users::where('order_type',0)) // delivery
           {
   $approveorder = ordersModel::where('id',$orderid)
                              ->where('user_id',$userid) 
                             // ->where('status',0)
                                ->update(['status'=> 2]);  
           }else {
            $approveorder = ordersModel::where('id',$orderid) // personal 
                              ->where('user_id',$userid) 
                             // ->where('status',0)
                                ->update(['status'=> 4]);  

           }    

      // notification for users

      //   $result = (new TestController)->exampleFunction();  
      // $notification = (new PushNotificationController)->bulksend(
      //     //  $deliveryid,
      //      "warning", //PushNotification(bulksend) لي بال  requset نفس ال 
      //      "There is an  Order waiting to approved", //PushNotification(bulksend) لي بال  requset نفس ال 
      //     //  "delivery$deliveryid", 
      //      "",
      //      "refreshorderpending"

      // );
          
         
      if(!( $approveorder)) {
          return response()->json([
                 'status' => 'success',
                 'data' => $approveorder ,
                 'datas' => $userid
       ]);
     }
         
      }


// archive order wher status = 4 , archive تم إستلام الطلبيه
public function archiveOrderToAdmin()
{

       $archive_orders = ordersModel::where('status',4)                 
                                      ->with('address_rltn')
                                      ->get();  
       
 if($archive_orders->isNotEmpty()) {
           return response()->json([
               'status' => 'success',
               'data' => $archive_orders,
               ]);
       }else{
           return response()->json([
                              'status' => 'failure',
                               'data' => 'No Archive Orders Data Found',
                           ]);
       }
}



/************************************ADMIN*********************************************** */


public function rating_for_archive_Order($orderid, Request $request)
{
  $orders = ordersModel::find($orderid);
  //$users = User::find($userid);

     

  if(!( $orders)) {
                return response()->json([
                       'status' => 'failure',
                       'data' => 'Not found this order-id ' . $orderid   ,
             ]);
           }


    // update order in cart
    if($orders){
      $ratingOrders = ordersModel::where('id',$orderid)
                          //  ->where('user_id',$userid)
                             ->update(['order_rating'=>$request->order_rating, 
                             'order_note_rating'=>$request->order_note_rating, 
                            ]);
    
      return response()->json([
          'status' => 'success',
          'data' => $ratingOrders,
          ]);
  }else{
      return response()->json([
                         'status' => 'failure',
                          'data' => 'No Data Inserted',
                      ]);
  }

  
}




/***************************************Delivery App************************************************ */

    // 'status'=> 2
    // send notification to delivery man that there are an order


    // in flutter function fbcmConfig.dart;
    // to get notification to user when the admin approved for the order
    public function preparedtoDeliveryMan($orderid, $userid, $deliveryid) 
    {
      $orders = ordersModel::find($orderid);
      $users = User::find($userid); // to send notification
      $delivery = delivery_users::find($deliveryid); // to send notification
  
         
   
      if(!( $orders && $users)) {
                    return response()->json([
                           'status' => 'failure',
                           'data' => 'Not found this order-id ' . $orderid. 'this user-id ' .$userid   ,
                 ]);
               }
  
               if(delivery_users::where('order_type',0)) // delivery
               {
       $approveorder = ordersModel::where('id',$orderid)
                                  ->where('user_id',$userid) 
                                 // ->where('status',0)
                                    ->update(['status'=> 2]);  
               }else {
                $approveorder = ordersModel::where('id',$orderid) // personal 
                                  ->where('user_id',$userid) 
                                 // ->where('status',0)
                                    ->update(['status'=> 4]);  

               }    
  
          // notification for users

          //   $result = (new TestController)->exampleFunction();  
          $notification = (new PushNotificationController)->bulksend(
               $deliveryid,
               "warning", //PushNotification(bulksend) لي بال  requset نفس ال 
               "There is an  Order waiting to approved", //PushNotification(bulksend) لي بال  requset نفس ال 
               "delivery$deliveryid", 
               "",
               "refreshorderpending"
  
          );
              
             
          if(!( $approveorder)) {
              return response()->json([
                     'status' => 'success',
                     'data' => $approveorder ,
                     'datas' => $userid
           ]);
         }
             
          }




     // 'status'=> 3

    // in flutter function fbcmConfig.dart;
    // to get notification to user when the admin approved for the order
    public function DeliveryManApproved($orderid,$userid, $deliveryid) 
    {
      $orders = ordersModel::find($orderid);
      $users = User::find($userid); // to send notification
    //  $delivery = delivery_users::find($deliveryid); // to send notification
         
   
      if(!( $orders && $users )) {
                    return response()->json([
                           'status' => 'failure',
                           'data' => 'Not found this order-id ' . $orderid. 'this user-id ' .$userid
                 ]);
               }
  
       $approveorder = ordersModel::where('id',$orderid)
                                  ->where('user_id',$userid) 
                                  ->where('status',2)
                                    ->update(['status'=> 3, 'delivery_id' => $deliveryid]);
                                    //->update(['delivery_id'=> $deliveryid]);     
  
          // notification for users

          //   $result = (new TestController)->exampleFunction();  
          // $notification = (new PushNotificationController)->bulksend(
          //      $userid,
          //      "success", //PushNotification(bulksend) لي بال  requset نفس ال 
          //      "The Order is in the way", //PushNotification(bulksend) لي بال  requset نفس ال 
          //      "users$userid", 
          //      "",
          //      "refreshorderpending",


             
          // );

          // notification for delivery Man
            //$userid;
            // "Hi all";
            // "success"; //PushNotification(bulksend) لي بال  requset نفس ال 
            // "The Order /has been approved by delivery"; //PushNotification(bulksend) لي بال  requset نفس ال 
            // "services";
            // "";
            // "";


            // // send notification to all delivery man that has a specificy delivery man approve to send the order
            // $userid;
            // "warning"; //PushNotification(bulksend) لي بال  requset نفس ال 
            // "The Order /has been approved by delivery.$deliveryid"; //PushNotification(bulksend) لي بال  requset نفس ال 
            // "services";
            // "";
            // "";
      

               
          if(( $approveorder > 0)) {
              return response()->json([
                     'status' => 'success',
                     'data' => $approveorder ,
                     // 'datas' => $userid
           ]);
        }
        
          
               
          }



         // 'status'=> 4

    // in flutter function fbcmConfig.dart;
    // to get notification to user when the admin approved for the order
    public function done($orderid, $userid) 
    {
      $orders = ordersModel::find($orderid);
      $users = User::find($userid); // to send notification
      
  
         
   
      if(!( $orders && $users)) {
                    return response()->json([
                           'status' => 'failure',
                           'data' => 'Not found this order-id ' . $orderid. 'this user-id ' .$userid   ,
                 ]);
               }
  
       $approveorder = ordersModel::where('id',$orderid)
                                  ->where('user_id',$userid) 
                                  ->where('status',3)
                                    ->update(['status'=> 4]);      
  
          // notification for users
 
          //   $result = (new TestController)->exampleFunction();  
          $notification = (new PushNotificationController)->bulksend(
               $userid,
               "success", //PushNotification(bulksend) لي بال  requset نفس ال 
               "Your Order Has Been Delivered", //PushNotification(bulksend) لي بال  requset نفس ال 
               "users$userid", 
               "",
               "refreshorderpending"

          );

          // notification for delivery Man
            
            "Warning"; //PushNotification(bulksend) لي بال  requset نفس ال 
            "The Order /has been delivered to the customer"; //PushNotification(bulksend) لي بال  requset نفس ال 
            "services";
            "";
            "";

      

               
          if(( $approveorder)) {
              return response()->json([
                     'status' => 'success',
                     'data' => $approveorder ,
                    // 'datas' => $userid
           ]);
         }else{
          return response()->json([
                             'status' => 'failure',
                              'data' => 'No Done Orders Data Found  ',
                          ]);
      }
           
}




public function viewPendingOrderToDeliveryMan()
{
   // $delivery = delivery_users::find($delivery_id);
   

  //  if(!( $delivery)) {
  //                return response()->json([
  //                       'status' => 'failure',
  //                       'data' => 'Not found this userid ' . $delivery_id  ,
  //             ]);
  //           } 
        $view_orders = ordersModel::where('status','2')
                       ->Where('order_type','0')
                       //->where('delivery_id',$delivery_id)
                       ->with('address_rltn')
                       ->with('delivery_rltn')

                       ->get();  
        
  if($view_orders->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => $view_orders,
                ]);
        }else{
            return response()->json([
                               'status' => 'failure',
                                'data' => 'No Pending Orders Data Found  ',
                            ]);
        }
}


////////////////////////////////// Accepted Orders /////////////////////////////////

public function viewAcceptedOrderToDeliveryMan($delivery_id)
{
  $delivery = delivery_users::find($delivery_id);

  if(!( $delivery)) {
    return response()->json([
           'status' => 'failure',
           'data' => 'Not found this delivery ' . $delivery ,
 ]);
}


       $view_Accepted_orders = ordersModel::where('status','3')
                                            ->Where('order_type','0')
                                            ->where('delivery_id',$delivery_id)
                                            ->with('address_rltn')
                                            ->with('delivery_rltn')

                                            ->get();

      if($view_Accepted_orders->isNotEmpty()) {
        return response()->json([
            'status' => 'success',
            'data' => $view_Accepted_orders,
            ]);
    }else{
        return response()->json([
                           'status' => 'failure',
                            'data' => 'No Accepted Orders Data Found  ',
                        ]);
    }                                  

}














// archive order wher status = 4 , archive تم إستلام الطلبيه
public function archiveOrderToDeliveryMan($delivery_id)
{

       $archive_orders = ordersModel::where('status',4)
                                      ->with('delivery_id',$delivery_id)                 
                                      ->with('address_rltn')
                                      ->get();  
       
 if($archive_orders->isNotEmpty()) {
           return response()->json([
               'status' => 'success',
               'data' => $archive_orders,
               ]);
       }else{
           return response()->json([
                              'status' => 'failure',
                               'data' => 'No Archive Orders Data Found',
                           ]);
       }
}

}




