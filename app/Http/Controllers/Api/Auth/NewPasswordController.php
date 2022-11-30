<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as RulesPassword;

class NewPasswordController extends Controller
{
    // Send Password Reset Link 
    public function forgotPassword(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
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

    $users = User::where('email','=',$request->email)->first();
    $users->update(['password'=>bcrypt($request->password)]);
    return response()->json(
        [
            'status' => 'success', 
            'data' => $users
        ]
     );

   

}
/***************************Reset password // after otp veify***************************************** */ 







// public function reset(Request $request)
// {

//     $validator = Validator::make($request->all(), [
            
//       //  'token' => 'required',
//         'email' => 'required|email',
//         'password' => ['required', 'confirmed', RulesPassword::defaults()],
//     ]);

//     if($validator->fails()){
//         return response()->json($validator->errors()->toJson(), 400);
//     }
  

//     $status = Password::reset(
//         $request->only('email', 'password', 'password_confirmation', 'token'),
//         function ($user) use ($request) {
//             $user->forceFill([
//                 'password' => Hash::make($request->password),
//                 'remember_token' => Str::random(60),
//             ])->save();

//             $user->tokens()->delete();

//             event(new PasswordReset($user));
//         }
//     );

//     if ($status == Password::PASSWORD_RESET) {
//         return response([
//             'message'=> 'Password reset successfully'
//         ]);
//     }

//     return response([
//         'message'=> __($status)
//     ], 500);

// }



}
