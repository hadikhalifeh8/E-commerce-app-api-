<?php

use App\Http\Controllers\Api\Admin_App\AdminController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Delivery\DeliveryAuthController;
use App\Http\Controllers\Api\pages\AddressController;
use App\Http\Controllers\Api\pages\CartController;
use App\Http\Controllers\Api\pages\CategoriesController;
use App\Http\Controllers\Api\pages\CouponController;

use App\Http\Controllers\Api\pages\FavoriteController;
use App\Http\Controllers\Api\pages\HomePageController;
use App\Http\Controllers\Api\pages\ItemsController;
use App\Http\Controllers\Api\pages\OffersController;
use App\Http\Controllers\Api\pages\ordersController;
use App\Http\Controllers\Api\pages\PushNotificationController;
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
Route::any('/insertcategory', [CategoriesController::class, 'insertcategory']);
Route::any('/updatecategory/{category_id}', [CategoriesController::class, 'updatecategory']);
Route::any('/deletecategory/{category_id}', [CategoriesController::class, 'deletecategory']);





// itemsController
Route::any('/getallitems', [ItemsController::class, 'getallitems']);
Route::any('/insertitem', [ItemsController::class, 'insertitem']);
Route::any('/updateitem/{item_id}', [ItemsController::class, 'updateitem']);
Route::any('/deleteitem/{item_id}', [ItemsController::class, 'deleteitem']);


Route::any('/getitemsbycategory/{category_}', [ItemsController::class, 'getitemsbycategory']);
Route::any('/searchitem', [ItemsController::class, 'searchitem']);




 // FavoriteController
Route::any('/addfavorite/{usersid}/{itemsid}', [FavoriteController::class, 'addfavorite']);
Route::any('/deletefavorite/{usersid}/{itemsid}', [FavoriteController::class, 'deletefavorite']);

Route::any('/myfavorite/{usersid}', [FavoriteController::class, 'myfavorite']); // show {my favorite} to specific user 
Route::any('/deletemyfavorite/{favoriteid}', [FavoriteController::class, 'deletemyfavorite']); // show {my favorite} to specific user 


// CartController
Route::any('/addtocart/{usersid}/{itemsid}', [CartController::class, 'addtocart']);
Route::any('/deletecart/{usersid}/{itemsid}', [CartController::class, 'deletecart']);
Route::any('/getItemsCount/{usersid}/{itemsid}', [CartController::class, 'getItemsCount']);
Route::any('/cartView/{usersid}', [CartController::class, 'cartView']); 


// AdressController
Route::any('/add_address/{usersid}', [AddressController::class, 'addaddress']);
Route::any('/edit_address/{usersid}', [AddressController::class, 'editaddress']);
Route::any('/view_address/{usersid}', [AddressController::class, 'viewaddress']);
Route::any('/delete_address/{addressid}', [AddressController::class, 'deleteaddress']);



// CouponController
Route::any('/check_Coupon/{couponName}', [CouponController::class, 'checkCoupon']);


// checkout / OrdersController
// Route::any('/add_order/{usersid}/{addressid}', [ordersController::class, 'addorder']);

// checkout / OrdersController
Route::any('add_order', [ordersController::class, 'addorder']);
Route::any('view_Pending_Order/{usersid}', [ordersController::class, 'viewPendingOrder']);
Route::any('details_Order/{orderid}', [ordersController::class, 'detailsOrder']);
Route::any('delete_Order/{orderid}', [ordersController::class, 'deleteOrder']);
Route::any('archive_Order/{usersid}',[ordersController::class, 'archiveOrder']);




// Notification Controllers
Route::post('send/{usersid}',[PushNotificationController::class, 'bulksend'])->name('bulksend');
Route::get('all-notifications', [PushNotificationController::class, 'index']); // not used // for web only
Route::get('get-notification-form', [PushNotificationController::class, 'create']); // not used // for web only
Route::any('get_Notification/{usersid}',[PushNotificationController::class, 'getNotification']);



// OffersController 
Route::any('/offers', [OffersController::class, 'offers']);


///////////////////////////////////////////// ADMIN ///////////////////////////////////////

// 1- get all orders for all users where status =0
Route::any('view_Pending_Order_To_Admin', [ordersController::class, 'viewPendingOrderToAdmin']);

 // 2- get all orders where status !=0 (not pending), and status !=4 (not done)
Route::any('view_Accepted_Orders_To_Admin', [ordersController::class, 'viewAcceptedOrdersToAdmin']);

// 3- get notification to user when the admin approved for the order
Route::any('approved_Order/{orderid}/{userid}', [ordersController::class, 'approvedOrder']);






Route::any('prepared_Orders_Show/{orderid}/{userid}/', [ordersController::class, 'preparedOrdersShow']);



// archive orders where('status',4)
Route::any('archive_Order_To_Admin',[ordersController::class, 'archiveOrderToAdmin']);


//checkout / OrdersController بستخدم اللي فوق نقس الشي الي بال 
// Route::any('details_Order/{orderid}', [ordersController::class, 'detailsOrder']);


///////////////////////////////////////////// ADMIN ///////////////////////////////////////

// Rating for orders
Route::any('rating_for_archive_Order/{orderid}', [ordersController::class, 'rating_for_archive_Order']);








// HomePageController + top selling items
Route::any('/getalldata', [HomePageController::class, 'getalldata']); 
// Route::any('/topselling', [HomePageController::class, 'topselling']);




/**************************************************************************************** */
        //********* */ Delivery Users Api  //********* */

 Route::post('delivery_login', [DeliveryAuthController::class, 'delivery_login']);

 // OTP Verification Code 
Route::any('Delivery_sendOtp', [DeliveryAuthController::class, 'sendOtp']); // resend otp /...
Route::post('Delivery_loginWithOtp', [DeliveryAuthController::class, 'loginWithOtp']); // check if the otp and email is OK.
Route::post('Delivery_reset-password', [DeliveryAuthController::class, 'reset']); // update password 

//  status=>2 عمليه تحضير/ تجهيز الطلب لعامل الدليفري
Route::any('preparedtoDeliveryMan/{orderid}/{userid}', [ordersController::class, 'preparedtoDeliveryMan']);

//  status=>3  معين  انو يوصلها(واحد) delivery المرحله التي يوافق عليها عامل 
Route::any('Delivery_Man_Approved/{orderid}/{userid}/{deliveryid}', [ordersController::class, 'DeliveryManApproved']);


//  status=> 4  مرحلة تسليم الطلب 
Route::any('done/{orderid}/{userid}', [ordersController::class, 'done']);



// 
Route::any('view_Pending_Order_To_DeliveryMan', [ordersController::class, 'viewPendingOrderToDeliveryMan']);

// accepted Orders (where status =>3)
Route::any('view_Accepted_Order_To_DeliveryMan/{delivery_id}', [ordersController::class, 'viewAcceptedOrderToDeliveryMan']);



Route::any('archive_Order_To_DeliveryMan/{delivery_id}',[ordersController::class, 'archiveOrderToDeliveryMan']);






/*******************************************Start Admin************************************* */
Route::post('Admin_register', [AdminController::class, 'Admin_register']);
Route::post('Admin_login', [AdminController::class, 'Admin_login']);

 // OTP Verification Code 
Route::any('Admin_sendOtp', [AdminController::class, 'Admin_sendOtp']); // resend otp /...
Route::post('Admin_loginWithOtp', [AdminController::class, 'Admin_loginWithOtp']); // check if the otp and email is OK.
Route::post('Admin_reset-password', [AdminController::class, 'Admin_reset_password']); // update password