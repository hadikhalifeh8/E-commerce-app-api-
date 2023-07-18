<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Mail\forget_password_Otp;
use App\Models\delivery_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DeliveryAuthController extends Controller
{
    public function delivery_login(Request $request)
    {
        //     $request->authenticate();


    //     $token = $request->user()->createToken('authtoken');

    //    return response()->json(
    //        [
    //            'message'=>'Logged in baby',
    //            'data'=> [
    //                'user'=> $request->user(),
    //                'token'=> $token->plainTextToken
    //            ]
    //        ]
    //     );

    /////////////************/ 2nd ///////////****************///


  
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        
    ]);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
//     // if (! $token = auth()->attempt($validator->validated())) {
//     //     return response()->json(['error' => 'Unauthorized'], 401);
//     // }
    
    
//     //if user email found and password is correct and user_approve is = 1


    /**  login without userapprove /(login in flutter) بال  user_approve',1 بحط ال  */
     //$user = User::where('email', $request->email)->where('user_approve',1)->first();
       
        $user = delivery_users::where('email', $request->email)->first();  
         if ($user && Hash::check($request->password, $user->password)) {


       return response()->json(
           [
               
               'status' => 'success',   
               'data'  =>  $user

           ]);
        }
        else{
            
            return response()->json([
              
              'status' =>'failure',
                'data'  =>  $user
          ]);
        }
    }

  /******************************************************************************** */

  public function loginWithOtp(Request $request){
       
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users',
        'verify_code' => 'required',
    ]);

     if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }
   
   
    Log::info($request);
    $user  = delivery_users::where([['email','=',request('email')],['verify_code','=',request('verify_code')]])->first();
   
    
   
   if($user){
        Auth::login($user, true);
        delivery_users::where('email','=',$request->email)->update(['user_approve'=>'1', 'verify_code' => request('verify_code')]);
        
        $user->refresh();
        return response()->json([
            //'success' => 'verify code success login',
            'status' =>'success',
             // 'data'  =>  $user
        ]);
   
   }
   
    else{
      //  return ("The Verification Code not Correct s");
      return response()->json([
        //'success' => 'verify code success login',
        'status' =>'failure',
         // 'data'  =>  $user
    ]);
    }

          // error in code false entered
}

/****************************************************************************** */


public function sendOtp(Request $request){

    $validator = Validator::make($request->all(), [
        'email' => 'required|email', //exists:users:if email not registered yet
    ]);

     if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    
    
    $otp = random_int(10000,99999);
    Log::info("verify_code = ".$otp);
   
    $users = delivery_users::where('email','=',$request->email)->first();
   
    if($users){
   
    $users->update(['verify_code'=>$otp]);
    $users->refresh();
       
   
        Mail::to($request->email)->send(new forget_password_Otp($users));
       
        return response()->json(
                    [
                        'status' => 'success', 
                        'data' => $users
                    ]
                 );
                 } else{
        return response()->json(
            [
                'status' => 'failure', 
               // 'data' => $users
            ]
         );

    }

    

}





/***************************Reset password // after otp veify***************************************** */ 

public function reset(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users',
        'password' => 'required|min:5',
    ]);

    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }

    $users = delivery_users::where('email','=',$request->email)->first();
    $users->update(['password'=>bcrypt($request->password)]);
    return response()->json(
        [
            'status' => 'success', 
            'data' => $users
        ]
     );

   

}
/***************************Reset password // after otp veify***************************************** */ 




}
