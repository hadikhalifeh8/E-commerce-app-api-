<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\Send_otp_VerifyCode;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;



class AuthController extends Controller
{

public function getallusers()

{
    $data = User::get();
    return response()->json([
        'status' => 'success',        
        'data' => $data
    ]);
}




    public function register(Request $request)
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
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['name' => $request->name,
                     'email' => $request->email,
                     'verify_code' => random_int(10000, 99999),
                     'phone_no' => $request-> phone_no,
                      'password' => bcrypt($request->password),
                   // 'password' => Hash::make($request->password),
                 

                    ]
                ));
                Mail::to($request->email)->send(new Send_otp_VerifyCode($user));
             
                $token = $user->createToken('authtoken');
        return response()->json([
            'status' => 'success',
            'token'=> $token->plainTextToken,
            'data' => $user
        ]);

    }


    public function login(Request $request)
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

     $user = User::where('email', $request->email)->where('user_approve',1)->first();
         
         if ($user && Hash::check($request->password, $user->password)) {


       return response()->json(
           [
               //'message'=>'Logged in baby',
               'status' => 'success',   
               'data'=> [
                   'user'=> $request->user(),
                   //'token'=> $token->plainTextToken
               ]
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

    

    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->json(
            [
                'message' => 'Logged out'
            ]
        );

    }

}
