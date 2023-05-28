<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\cart;
use App\Models\categories;
use App\Models\favorite;
use App\Models\items;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function getalldata()
    {
        // $categories = categories::all();
        // $items = items::where('discount','!=','0')->get();
        // $favorite = favorite::all();

        // return response()->json([
        //     'status' => 'success',
        //     'categories' => $categories,
        //     'items' => $items,
        //     'favorites' => $favorite,

        // ]);

        $categories = categories::all();
        
        // top selling
        // get only row if have a same items_id
                $countitems = cart::where('order_id','!=',Null)
                            ->with('item_rltn')->orderBy('id','desc')->distinct()->get(['items_id']); 

        if($countitems)
        {
            return response()->json([
                'status' => 'success',
                'categories' => $categories,
                'items' => $countitems,
             ]);
            
          }else{
             return response()->json([
                'status' => 'failure',
                'data' => 'No Favorite Date Found',
             ]);
    
          }
    }

    // public function topselling()
    // {   

    //     $countitems = cart::where('order_id','!=',Null)
    //                         ->with('item_rltn')->distinct()->orderBy('id', 'desc')->get()
    //                         ;

    //     if($countitems)
    //     {
    //         return response()->json([
    //             'status' => 'success',
    //             'data' => $countitems,
    //          ]);
            
    //       }else{
    //          return response()->json([
    //             'status' => 'failure',
    //             'data' => 'No Favorite Date Found',
    //          ]);
    
    //       }
       
    // }
}
