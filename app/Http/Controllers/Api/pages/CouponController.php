<?php

namespace App\Http\Controllers\Api\pages;

use App\Http\Controllers\Controller;
use App\Models\couponModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function checkCoupon($couponName , Request $request)
    { 

          // if name equal $couponName
        // if now dateTime أصغر من expireyDate 
        // count > 0

        // $couponcheckname = couponModel::where('name',$couponName)->first();
        // if(!($couponcheckname))
        // {
        //     return response()->json([
        //         'status' => 'failure',
        //        'data' => 'No coupon name'.  $couponName ,
        //    ]);
        // }

 
        $now = date("Y-m-d H:i:s");

        $couponcheck = couponModel::where('name',$couponName)
                                  ->where('expiry_date','>',$now)
                                  ->where('count','>',0)
                                  ->get();
                                  

      if($couponcheck->isNotEmpty()) {
        return response()->json([
            'status' => 'success',
           // 'data' => $couponcheck , // 
             //'MapData' => ['data1' =>$couponcheck], // List array
            'MapData' => $couponcheck[0], // point directly to the first item {Map}
        ]);
    }else{
        return response()->json([
            'status' => 'failure',
            'MapData' => 'Not coupon data found' ,
]);
    }
}

}