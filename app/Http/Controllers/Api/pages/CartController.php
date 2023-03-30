<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\cart;
use App\Models\items;
use App\Models\User;
use Illuminate\Http\Request;

use function PHPUnit\Framework\countOf;

class CartController extends Controller
{


    public function addtocart($usersid, $itemsid, Request $request)
    {

 if(items::find($itemsid) AND User::find($usersid)){
      $addtoCart = new cart();

      $addtoCart->users_id = $usersid;
      $addtoCart->items_id = $itemsid;

      $addtoCart->save();

      //items::where('id',$itemsid)->update(['cart_qty'=>+1]);

      items::where('id',$itemsid)->increment('cart_qty');



    return response()->json([
        'status' => 'success',
        'data' => $addtoCart,
     ]);
    }else{
        return response()->json([
            'status' => 'failure',
            'data' => 'Errrrrrror Not Saved YET',
         ]);
    }
}


public function deletecart($usersid, $itemsid)
{
   // 1- bet2akad eza mawjood l id aw la  
   $items = items::find($itemsid);
   $users = User::find($usersid);
    
   if(!($items AND $users))
   {
    return response()->json([
                            'status' => 'failure',
                            'data' => 'Not found this userid ' . $usersid .' OR this itemid '. $itemsid ,
            ]);
   } 


   items::where('id','=',$itemsid);
   User::where('id','=',$usersid);

   // بس row إذا زدت أكتر المنتج أكتر من مره بس إحذف بيحذف أخر 
       cart::where('items_id',$itemsid)->where('users_id',$usersid)->orderBy('id', 'desc')->limit(1)->delete();
       items::where('id',$itemsid)->decrement('cart_qty'); // delete / minus qty of items

   if($items AND $users)
     {
        return response()->json([
                                 'status' => 'success',
                                 'data' => $items,
                ]);
            }

}


// count the number of rows in cart table  that have the same itemid and same userid (and get the number) + 1 -
public function getItemsCount($usersid, $itemsid)   // كم حبه من المنتج
{
    // 1- bet2akad eza mawjood l id aw la  
    $items = items::find($itemsid);
    $users = User::find($usersid);
     
    if(!($items AND $users))
    {
     return response()->json([
                             'status' => 'failure',
                             'data' => 'Not found this userid ' . $usersid .' OR this itemid '. $itemsid ,
             ]);
    } 

    $cart = cart::where('users_id',$usersid)->where('items_id',$itemsid)->count('id');
    
    if($items AND $users)
     {
        return response()->json([
                                 'status' => 'success',
                                 'data' => $cart,
                ]);
            }
}


 // get the count of row in cart table and the price of each item in the item table and get the sum of items price 
public function cartView($usersid) 
 {
      // 1- bet2akad eza mawjood l id aw la  
    //$items = items::find($itemsid);
    $users = User::find($usersid);
     
    if(!($users))
    {
     return response()->json([
                             'status' => 'failure',
                             'data' => 'Not found this userid ' . $usersid ,
             ]);
    } 


    // bjeeb bs row 1 men lmkarareen bl table ({id=1/usersid=1/itemsid=2} / {id=2/usersid=1/itemsid=2}) bjeble bs row 1
      $cart = cart::where('users_id',$usersid)
                 ->with('user_rltn')->with('item_rltn')->distinct()
                 ->get('items_id');
                
                
 //$items = items::with('userscart_rltn')->get();

                 

    // $item = items::with('userscart_rltn')->where('userscart_rltn.users_id',$usersid)->get();

                 

                 // $sumofallItemsbyuser = cart::where('users_id',$usersid)->with('user_rltn')->with('item_rltn')->get()->sum('item_rltn.price');
                 $sumofallItemsbyuser = cart::where('users_id',$usersid)->with('user_rltn')->with('item_rltn')->get()->sum('item_rltn.itemspricediscount');

                 //$totalcountOfallitemsItemsQNTY = cart::where('users_id',$usersid)->with('user_rltn')->with('item_rltn')->get()->sum('item_rltn.cart_qty');
                
                
                
                
                 $totalcountOfallitemsItems = cart::where('users_id',$usersid)->count('id');
                 //$totalcountOfSpecificItems = cart::where('users_id',$usersid)->where('items_id',$itemsid)->count('id');

                
    
    if($users)
    {
       return response()->json([
                                'status' => 'success',
                                'data' => $cart,
                                // flutter لي بألب ال  Model لل  data باخد بس ال 
                               
                                 'dataSpecificSumandCountOfItems' => 
                                 [                    'sumofallItemsbyuser' => $sumofallItemsbyuser,
                                                      'totalcountOfallitemsItemsQNTY' => $totalcountOfallitemsItems,
                                                     //'totalcountOfSpecificItems' => $totalcountOfSpecificItems
                              //  'getqtyitems' => $carts,
                                                    ],


                                // 'sumofallItemsbyuser' => $sumofallItemsbyuser,
                                // 'totalcountOfItems' => $totalcountOfallitemsItems,
                                // 'totalcountOfSpecificItems' => $totalcountOfSpecificItems,


                                
               ]);
           }
}

    
/******************************************************************************* */



}
