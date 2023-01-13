<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\pages\CategoriesController;
use App\Http\Controllers\Api\pages\FavoriteController;
use App\Http\Controllers\Api\pages\HomePageController;
use App\Http\Controllers\Api\pages\ItemsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum','verified')->get('/user', function (Request $request) {
//     return $request->user();
//     // postman in the headers tab (Authorization => token ) => no
//     // postman in Authorizaion:  Bearer Token : $token => yes
// });

// Auth::routes(['verify' => true]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
 // dd($request);
    // postman in the headers tab (Authorization => token ) => no
    // postman in Authorizaion:  Bearer Token : $token => yes
});

Route::get('/getallusers', [AuthController::class, 'getallusers']);


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
// logout :  postman in the Authorization / Bearer Token 
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');
 
// postman: body email : $email
Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']); //Send Password Reset Link  /**  مش مطلوب*/
Route::post('reset-password', [NewPasswordController::class, 'reset']); // update password 


// OTP Verification Code 
Route::any('sendOtp', [EmailVerificationController::class, 'sendOtp']); // resend otp /...
Route::post('loginWithOtp', [EmailVerificationController::class, 'loginWithOtp']); // check if the otp and email is OK.




// CategoriesController
Route::any('/getallcategories', [CategoriesController::class, 'getallcategories']);
Route::post('/insertcategory', [CategoriesController::class, 'insertcategory']);



// itemsController
Route::any('/getallitems', [ItemsController::class, 'getallitems']);
Route::post('/insertitem', [ItemsController::class, 'insertitem']);
Route::any('/getitemsbycategory/{category_}', [ItemsController::class, 'getitemsbycategory']);



 // FavoriteController
Route::any('/addfavorite/{usersid}/{itemsid}', [FavoriteController::class, 'addfavorite']);
Route::any('/deletefavorite/{usersid}/{itemsid}', [FavoriteController::class, 'deletefavorite']);

Route::any('/myfavorite/{usersid}', [FavoriteController::class, 'myfavorite']); // show {my favorite} to each user 
Route::any('/deletemyfavorite/{favoriteid}', [FavoriteController::class, 'deletemyfavorite']); // show {my favorite} to each user 









// HomePageController
Route::any('/getalldata', [HomePageController::class, 'getalldata']);
