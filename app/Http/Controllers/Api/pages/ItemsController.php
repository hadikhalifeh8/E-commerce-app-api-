<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\categories;
use App\Models\favorite;
use App\Models\items;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    public function getallitems()
    {
        //   $items = items::all();
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

            $items->save();
    

    return response()->json([
        'status' => 'success',
        'data' => $items,
    ]);

   }
  }





}
