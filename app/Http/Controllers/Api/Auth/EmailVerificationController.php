<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\forget_password_Otp;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Mail;


class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified'
            ];
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }


    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Email already verified'
            ];
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return [
            'message'=>'Email has been verified'
        ];
    }

    //////////////////////////// OTP VERIFICATION CODE //////////////////////////////


    public function loginWithOtp(Request $request){
       
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'verify_code' => 'required',
        ]);

         if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
       
       
        Log::info($request);
        $user  = User::where([['email','=',request('email')],['verify_code','=',request('verify_code')]])->first();
       
        
       
       if($user){
            Auth::login($user, true);
            User::where('email','=',$request->email)->update(['user_approve'=>'1', 'verify_code' => request('verify_code')]);
            
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

    /////////////////////////////////////////////////////////////////////////

  








/////////////////////////////////////////////////////////////////////////////////////////////////////
  // send OTP Message to Email Address
    public function sendOtp(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email', //exists:users:if email not registered yet
        ]);

         if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        
        $otp = random_int(10000,99999);
        Log::info("verify_code = ".$otp);
       
        $users = User::where('email','=',$request->email)->first();
       
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


}

