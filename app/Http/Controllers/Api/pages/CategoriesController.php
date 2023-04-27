<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function getallcategories()
    {
        $categories = categories::all();
       
        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function insertcategory(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,svg',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
       
                if($request->hasfile('image'))
                {
          
                $file=   $request->file('image')  ;
                    
            $name = $file->getClientOriginalName();
            $file->storeAs('attachments/categories/', $file->getClientOriginalName(),'upload_attachments');
    
                    $categories = new categories();
                    $categories->name_ar = $request->name_ar;
                    $categories->name_en = $request->name_en;
                    $categories->image = $name;
                    $categories ->save();
            

                  
                if($categories)    {
                
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ]);
        
        }else{
            return response()->json([
                'status' => 'failure',
                'data' => 'null',
            ]);

        }

        }
        
             
            // $categories->image = $name; 
           

           
           
        


        
    }
}
