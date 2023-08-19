<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\categories;
use App\Models\favorite;
use App\Models\items;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    public function getallitems()
    {
           //$items = items::all();

        $items = items::with('category_rltn')->get();
      

        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }


/******************************************************************************************** */
    public function getitemsbycategory($category_) // without favorite 
    {
        // $users = User::where('id', $users_id)->first();
        $cat = categories::where('id', $category_)->first();
        if($cat){
        $items = items::with('category_rltn')->where('category_id',$cat->id)->get();

        if($items->count()> 0)
           {
            return response()->json([
                'status' => 'success',
                'data' => $items,
            ]);
     } else{
        return response()->json([
            'status' => 'failure',
            'data' => "no items found",
        ]);
     } 
    } 
    }




    public function insertitem(Request $request)
    {
        $validator = Validator::make($request->all(), [
          
            'category_id' => 'required',
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,svg',
            'count' => 'required',
            'price' => 'required',
            'discount' => 'required',
            
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


        if($request->hasfile('image'))
        {
         $file = $request->file('image')  ;
            
    $name = $file->getClientOriginalName();
    $file->storeAs('attachments/items/', $file->getClientOriginalName(),'upload_attachments');

            $items = new items();
            $items->category_id = $request->category_id;
            $items->name_ar = $request->name_ar;
            $items->name_en = $request->name_en;
            $items->description_ar = $request->description_ar;
            $items->description_en = $request->description_en;
            $items->image = $name;
            $items->count = $request->count;
            $items->price = $request->price;
            $items->discount = $request->discount;
            $items->itemspricediscount = ($request->price-($request->price * $request->discount / 100));



 
            $items->save();
    

    return response()->json([
        'status' => 'success',
        'data' => $items,
    ]);

   }
  }



   public function updateitem(Request $request,$item_id)
   {
    $validator = Validator::make($request->all(), [
          
        'category_id' => 'required',
        'name_ar' => 'required|string',
        'name_en' => 'required|string',
        'description_ar' => 'required|string',
        'description_en' => 'required|string',
        'image' => 'required|image|mimes:jpg,jpeg,png,svg',
        'count' => 'required',
        'price' => 'required',
        'discount' => 'required',
        
    ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }
     

    $item = items::find($item_id);
   // $category = items::find($item_id);

    if(!($item))
     {
      return response()->json([
                              'status' => 'failure',
                              'data' => 'Not found this itemid ' . $item_id ,
                             ]);
     }


     if($request->hasfile('image'))
     {
        // delete the old photo from a file if update a photo first
        File::delete(public_path('attachments/items/'. $item->image));

     $file = $request->file('image')  ;
         
 $name = $file->getClientOriginalName();
 $file->storeAs('attachments/items/', $file->getClientOriginalName(),'upload_attachments');



    /*  $item->update([*/

            $item->category_id = $request->category_id;
            $item->name_ar = $request->name_ar;
            $item->name_en = $request->name_en;
            $item->description_ar = $request->description_ar;
            $item->description_en = $request->description_en;
            $item->image = $name ;
            $item->count = $request->count;
            $item->active = $request->active;
            $item->price = $request->price;
            $item->discount = $request->discount;
            $item->itemspricediscount = ($request->price-($request->price * $request->discount / 100));
            $item->favorite = $request->favorite;
            $item->cart_qty = $request->cart_qty;



             $item->save();
            //   dd($item),
     
     
        // ]);
 

       
          if($item)    {
     
               return response()->json([
                   'status' => 'success',
                   'data' => $item,
               ]);

              }else{
               return response()->json([
                   'status' => 'failure',
                   'data' => 'null',
               ]);

}

}
   }







public function deleteitem($item_id)
{
    $item = items::find($item_id);

    if(!($item))
     {
      return response()->json([
                              'status' => 'failure',
                              'data' => 'Not found this itemid ' . $item_id ,
                             ]);
     }



// delete a photo from a file 
File::delete(public_path('attachments/items/'. $item->image));
    



$item = items::find($item->id)->delete();

     if($item)    {
                
        return response()->json([
            'status' => 'success',
            'data' => $item,
        ]);
    
    }else{
        return response()->json([
            'status' => 'failure',
            'data' => 'null',
        ]);

    }
}












  public function searchitem(Request $request)
  {

    // $searchTerm = $request->input('q');

    // $items = items::where('name_en','LIKE','%'.$searchTerm.'%')->get();

    // return response()->json([
    //     'items' => $items
    // ]);


      $searchitems = items::orderBy('id','Desc')
                          ->where('name_en','LIKE','%'.$request->name_en.'%')
                         ->where('name_ar','LIKE','%'.$request->name_ar.'%')
                          ->get();
    
    
                        //   if($searchitems->count() > 0)
                        //           {
                        //                  return response()->json([
                        //                                   'status' => 'success',
                        //                                   'data' => $searchitems,
                        //                               ]);
                        
                        //            }  else{
                        //               return response()->json([
                        //                   'status' => 'failure',
                        //                   'data' => 'No Data found',
                        //               ]);
                        //            }       
                                }      

   
//   public function searchitem($search)
//   {
//     $searchitems = items::orderBy('id','Desc')
//                         ->where('name_en','LIKE','%'.$search.'%')
//                         ->orwhere('name_ar','LIKE','%'.$search.'%')
//                         ->get();
    
//     if($searchitems->count() > 0)
//         {
//                return response()->json([
//                                 'status' => 'success',
//                                 'data' => $searchitems,
//                             ]);

//          }  else{
//             return response()->json([
//                 'status' => 'failure',
//                 'data' => 'No Data found',
//             ]);
//          }               
//   }















}
