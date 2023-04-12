<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\favorite;
use App\Models\items;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{

   public function addfavorite($usersid, $itemsid, Request $request)
   {


    if(items::find($itemsid) AND User::find($usersid))
    {
        $addTofavorite = new favorite();

         $addTofavorite->users_id  = $usersid;
        // $addTofavorite->users_id  = auth()->user()->id;
        $addTofavorite->items_id = $itemsid;

        $addTofavorite->save();

        // if($usersid AND $itemsid)
        //  {
        items::where('id',$itemsid)->update(['favorite'=>'1']);
                $addTofavorite->refresh();

        return response()->json([
                        'status' => 'success',
                        'data' => $addTofavorite,
                     ]);

   }else{
        return response()->json([
            'status' => 'failure',
            'data' => 'Errrrrrror Not Saved',
         ]);

   // }

   }
 }




/************************************************************************************* */
   public function deletefavorite($usersid, $itemsid)
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

       // 2- delete Data
    //    $favorite->delete($favid);

       items::where('id','=',$itemsid)->update(['favorite'=>'0']);
       User::where('id','=',$usersid);
        $items->refresh();
      favorite::where('items_id',$itemsid)->where('users_id',$usersid)->delete('id');
       if($items AND $users)
         {
            return response()->json([
                                     'status' => 'success',
                                     'data' => $items,
                    ]);
                }
    }


   /***********************************************MY FAVORITE PAGE***************************** */ 
   
   public function myfavorite($usersid)
   {
   
    $users = User::find($usersid);

    if(!($users))
       {
        return response()->json([
                                'status' => 'failure',
                                'data' => 'Not found this userid ' . $usersid .' ' ,
                ]);
       } 

    $favorites = favorite::where('users_id', $usersid)->with('item_rltn')->with('user_rltn')->get();
   // $favorites->get();

    if($favorites->isNotEmpty())
    {
        return response()->json([
            'status' => 'success',
            'data' => $favorites,
         ]);
        
      }else{
         return response()->json([
            'status' => 'failure',
            'data' => 'No Favorite Date Found',
         ]);

      }

   }

   public function deletemyfavorite($favoriteid)
   {
  
     $favorite = favorite::where('id', $favoriteid)->delete();
     
   if($favorite)
   {
      
    return response()->json([
                          'status' => 'success',
                           'data' => $favorite,
            ]);
   } else{
      
         return response()->json([
                               'status' => 'failure',
                                'data' => 'No data',
                 ]);
        }
   
        
        


 
      }


  

  
     
     




//     if($favorite)
//     {
//          return response()->json([
//                   'status' => 'success',
//                   'data' => $favorite,
//        ]);
//     }else{ 
//         return response()->json([
//             'status' => 'failure',
//             'data' => 'No Favorite id Data',
//  ]);
//     }

     
  


   /***********************************************MY FAVORITE PAGE***************************** */ 


}
