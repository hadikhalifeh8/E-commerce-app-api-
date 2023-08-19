<?php

namespace App\Http\Controllers\Api\Admin_App;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Mail\forget_password_Otp;
use App\Mail\Send_otp_VerifyCode;
use App\Models\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function Admin_register(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'phone_no' => 'required|string|unique:users',
        //     'password' => ['required', 'confirmed', Password::defaults()],
        // ]);
        // if($validator->fails()){
        //     return response()->json($validator->errors()->toJson(), 400);
        // }

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'phone_no' => $request-> phone_no,
        //     'password' => Hash::make($request->password),
        // ]);

        // event(new Registered($user));

        // $token = $user->createToken('authtoken');

        // return response()->json(
        //     [
        //         'message'=>'User Registered',
        //         'data'=> ['token' => $token->plainTextToken, 'user' => $user]
        //     ]
        // );

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'phone_no' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            
        ]);
        if($validator->fails()){
            //return response()->json($validator->errors()->toJson(), 422);
            return response()->json([
            
                'status' =>'failure', 
                  'data'  =>  'Failure sss'
            ]);
        }
        $user = AdminModel::create(array_merge(
                    $validator->validated(),
                    ['name' => $request->name,
                     'email' => $request->email,
                     'verify_code' => random_int(10000, 99999),
                     'phone_no' => $request-> phone_no,
                      'password' => bcrypt($request->password),
                   // 'password' => Hash::make($request->password),
                 

                    ]
                ));
                if($user == true){
                Mail::to($request->email)->send(new Send_otp_VerifyCode($user));
             
                //$token = $user->createToken('authtoken');
                
        return response()->json([
            'status' => 'success',
            //'token'=> $token->plainTextToken,
            'data' => $user
        ]);
    }
    // else{
    //     return response()->json([
            
    //         'status' =>'failure', 
    //           'data'  =>  'Failure sss'
    //     ]);

    // }

    }


    public function Admin_login(Request $request)
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
       
        $user = AdminModel::where('email', $request->email)->first();  
         if ($user && Hash::check($request->password, $user->password)) {


       return response()->json(
           [
               //'message'=>'Logged in baby',
               'status' => 'success',   
            //    'data'=> [
            //        'user'=> $request->user(),
            //        //'token'=> $token->plainTextToken
            //    ]
            'data'  =>  $user

           ]);
        }
        else{
            //  return ("The Verification Code not Correct s");
            return response()->json([
              //'success' => 'verify code success login',
              'status' =>'failure',
                'data'  =>  $user
          ]);
        }
        
   


         /////////////************/ 2nd ///////////****************///
   
    }


    public function Admin_loginWithOtp(Request $request){
       
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:admin',
            'verify_code' => 'required',
        ]);
    
         if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
       
       
        Log::info($request);
        $admin  = AdminModel::where([['email','=',request('email')],['verify_code','=',request('verify_code')]])->first();
       
        
       
       if($admin){
           // Auth::login($admin, true);
            AdminModel::where('email','=',$request->email)->update(['admin_approve'=>'1', 'verify_code' => request('verify_code')]);
            
            $admin->refresh();
            return response()->json([
                //'success' => 'verify code success login',
                'status' =>'success',
                 'data'  =>  $admin
            ]);
       
       }
       
        else{
          //  return ("The Verification Code not Correct s");
          return response()->json([
            //'success' => 'verify code success login',
            'status' =>'failure',
              'data'  =>  $admin
        ]);
        }
    
              // error in code false entered
    }






    public function Admin_sendOtp(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email', //exists:users:if email not registered yet
        ]);
    
         if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        
        $otp = random_int(10000,99999);
        Log::info("verify_code = ".$otp);
       
        $users = AdminModel::where('email','=',$request->email)->first();
       
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

public function Admin_reset_password(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:admin',
        'password' => 'required|min:5',
    ]);

    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }

    $users = AdminModel::where('email','=',$request->email)->first();
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
