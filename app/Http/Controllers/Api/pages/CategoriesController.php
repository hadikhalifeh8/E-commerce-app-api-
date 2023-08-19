<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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

                    // $categories->name_ar = "nameArabic";
                    // $categories->name_en = "nameEnglish";
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

/******************************************old update *************************************** */
//     public function updatecategory(Request $request,$category_id)
//     {

//         // $validator = Validator::make($request->all(), [
//         //     'name_ar' => 'required|string',
//         //     'name_en' => 'required|string',
//         //    // 'image' => 'required|image|mimes:jpg,jpeg,png,svg',
//         // ]);
//         // if($validator->fails()){
//         //     return response()->json($validator->errors()->toJson(), 400);
//         // }



//        $category = categories::find($category_id);
//        if(!($category))
//         {
//          return response()->json([
//                                  'status' => 'failure',
//                                  'data' => 'Not found this categoryid ' . $category_id ,
//                                 ]);
//         }

//     //    $category->name_ar =  $request->name_ar;
//     //    $category->name_en =  $request->name_en;
//      //  $category->save();


//        if($request->hasfile('image'))
//      {

//     // delete the old photo from a file if update a photo first
//      File::delete(public_path('attachments/categories/'. $category->image));
          
//                 $file=   $request->file('image')  ;
                    
//             $name = $file->getClientOriginalName();
//             $file->storeAs('attachments/categories/', $file->getClientOriginalName(),'upload_attachments');
    
//                 $category->update([
                    

//                     $category->name_ar =  $request->name_ar,
//                     $category->name_en =  $request->name_en,
//                     $category->image =  $name,

//                     $category->save(),
                
                
//                ]);
//             }else {
//                 // Update only other data (name_ar and name_en)
//                 $category->name_ar = $request->name_ar;
//                 $category->name_en = $request->name_en;
//                 $category->save();
//             }
            

                  
//                 if($category)    {
                
//             return response()->json([
//                 'status' => 'success',
//                 'data' => $category,
//             ]);
        
//         }else{
//             return response()->json([
//                 'status' => 'failure',
//                 'data' => 'null',
//             ]);

//         }



   
// }

/******************************************End old update *************************************** */


public function updatecategory(Request $request, $category_id)
{
    $category = categories::find($category_id);
    if (!$category) {
        return response()->json([
            'status' => 'failure',
            'data' => 'Not found this categoryid ' . $category_id,
        ]);
    }
   
    $category->name_ar = $request->name_ar;
    $category->name_en = $request->name_en;

    if ($request->hasfile('image')) {
        // Delete the old photo file if it exists
        File::delete(public_path('attachments/categories/' . $category->image));

        $file = $request->file('image');
        $name = $file->getClientOriginalName();
        $file->storeAs('attachments/categories/', $file->getClientOriginalName(), 'upload_attachments');
        $category->image = $name;
    }

    $category->save();

    
     if($category)    {   
                    return response()->json([
                        'status' => 'success',
                        'data' => $category,
                    ]);
                
                }
                else{
                    return response()->json([
                        'status' => 'failure',
                        'data' => 'null',
                    ]);
        
                }
}


   public function deletecategory(Request $request,$category_id)
   {

    $category = categories::find($category_id);

    if(!($category))
     {
      return response()->json([
                              'status' => 'failure',
                              'data' => 'Not found this categoryid ' . $category_id ,
                             ]);
     }



// delete a photo from a file 
File::delete(public_path('attachments/categories/'. $category->image));
    



$category = categories::find($category->id)->delete();

     if($category)    {
                
        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    
    }else{
        return response()->json([
            'status' => 'failure',
            'data' => 'null',
        ]);

    }

   }
   }

   




