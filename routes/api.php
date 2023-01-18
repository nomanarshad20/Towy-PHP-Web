<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Passenger\Auth\AuthController;
use App\Http\Controllers\API\Driver\Auth\AuthController as DriverAuthController;
use App\Http\Controllers\API\Driver\DriverInformationController;
use App\Http\Controllers\API\Driver\ProfileController;
use App\Http\Controllers\API\Driver\DriverLocationController;
use App\Http\Controllers\API\Driver\TripsController;
use App\Http\Controllers\API\Passenger\RideInitialDistanceController;
use App\Http\Controllers\API\Passenger\CreateRideController;
use App\Http\Controllers\API\Passenger\FindDriversController;
use App\Http\Controllers\API\Driver\DriverCancelRideController;
use App\Http\Controllers\API\CancelReasonController;
use App\Http\Controllers\API\Passenger\PassengerCancelRideController;
use App\Http\Controllers\API\Driver\DriverController;
use App\Http\Controllers\API\FareDistributionsController;
use App\Http\Controllers\API\Driver\BookingRatingController;
use App\Http\Controllers\API\Passenger\BookingRatingController as PassengerRatingController;
use App\Http\Controllers\API\Passenger\TripHistoryController;
use App\Http\Controllers\API\Passenger\NotificationController;
use App\Http\Controllers\API\Passenger\VoucherController;
use App\Http\Controllers\API\Passenger\BannerImageController;
use App\Http\Controllers\API\Passenger\SettingController;
use App\Http\Controllers\API\Passenger\CurrentStatusController;
//use App\Http\Controllers\API\PagesContentController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\Driver\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Passenger\StripeController;
use App\Http\Controllers\API\Driver\ServicesController;
use App\Http\Controllers\API\Passenger\ServiceController;
use App\Http\Controllers\API\Passenger\ServiceBookingController;
use App\Http\Controllers\API\Passenger\AddToWalletController;
use App\Http\Controllers\API\Driver\StripeController as DriverStripeController;
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
Route::group(['middleware' => ['json.response']], function () {

    Route::namespace('API')->group(function () {

        Route::namespace('Passenger')->group(function () {

            Route::namespace('Auth')->group(function () {
                Route::post('passenger-login', [AuthController::class, 'login']);
                Route::post('passenger-register', [AuthController::class, 'register']);
                Route::post('passenger-check-phone-number', [AuthController::class, 'checkPhoneNumber']);

                Route::post('passenger-social-login', [AuthController::class, 'socialLoginMobile']);
                Route::post('passenger-forget-password',[AuthController::class,'forgetPassword']);

            });



        });



        Route::namespace('Driver')->group(function () {
            Route::namespace('Auth')->group(function () {
                Route::post('driver-login', [DriverAuthController::class, 'login']);
                Route::post('driver-register', [DriverAuthController::class, 'register']);
                Route::post('send-otp',[DriverAuthController::class,'sendOtp']);
                Route::post('reset-password',[ForgotPasswordController::class,'resetPassword']);

                Route::post('driver-verify-otp',[DriverAuthController::class,'verifyOtp']);
                Route::post('driver-resend-otp',[DriverAuthController::class,'resendOtp']);
            });
        });


        Route::middleware('auth:sanctum')->group(function () {
            Route::namespace('Passenger')->group(function () {
                Route::post('passenger-user-info-update', [AuthController::class, 'userDataUpdate']);
                Route::post('passenger-find-near-drivers', [FindDriversController::class, 'findDriversByVehicle']);
                Route::post('passenger-calculating-distance-and-fare',[RideInitialDistanceController::class,'findDistance']);
                Route::post('passenger-create-booking',[CreateRideController::class,'booking']);
                Route::post('passenger-logout',[AuthController::class,'logout']);
                Route::post('passenger-update-password',[AuthController::class,'passwordUpdate']);
                Route::post('passenger-cancel-ride',[PassengerCancelRideController::class,'cancelRide']);

                Route::post('passenger-save-rating',[PassengerRatingController::class,'giveRating']);
                Route::get('passenger-trip-history',[TripHistoryController::class,'history']);
                Route::get('passenger-get-notifications',[NotificationController::class,'index']);
                Route::get('passenger-apply-voucher',[VoucherController::class,'apply']);
                Route::get('passenger-dashboard',[BannerImageController::class,'index']);
                Route::get('passenger-get-help',[SettingController::class,'index']);
                Route::get('get-passenger-status',[CurrentStatusController::class,'index']);
                Route::get('get-active-vouchers',[VoucherController::class,'voucherList']);

                Route::post('passenger-create-stripe-customer',[StripeController::class,'createCustomer']);

                //services api
                Route::get('passenger-service-list',[ServiceController::class,'index']);
                Route::post('passenger-create-service-booking',[ServiceBookingController::class,'create']);
                Route::post('send-ride-request-to-driver',[ServiceBookingController::class,'sendRideRequest']);

                Route::post('passenger-amount-add-to-wallet',[AddToWalletController::class,'save']);


            });

            Route::namespace('Driver')->group(function(){
                Route::post('driver-save-information',[DriverInformationController::class,'save']);
                Route::post('driver-save-document',[DriverInformationController::class,'saveDocument']);
                Route::post('driver-save-vehicle-information',[DriverInformationController::class,'saveVehicleInformation']);
                Route::post('driver-profile-update',[ProfileController::class,'saveProfile']);
                Route::get('driver-document-complete',[DriverInformationController::class,'documentComplete']);
                Route::get('driver-get-vehicle-types',[DriverInformationController::class,'getVehicleType']);
                Route::get('driver-save-vehicle-type',[DriverInformationController::class,'saveVehicleType']);
                Route::get('driver-save-social-security-number',[DriverInformationController::class,'saveSocialSecurityNumber']);
                Route::post('resend-approval-request',[DriverInformationController::class,'resendRequest']);

                Route::get('recommended-vehicle-types',[DriverInformationController::class,'recommendedVehicle']);

                Route::post('driver-save-location',[DriverLocationController::class,'save']);

                Route::get('driver-trip-history',[TripsController::class,'index']);

                Route::post('driver-cancel-ride',[DriverCancelRideController::class,'cancelRide']);

                Route::get('change-driver-status',[DriverController::class,'changeDriverOnlineStatus']);

                Route::get('get-driver-current-status',[DriverController::class,'getCurrentStatus']);
                Route::get('driver-logout',[DriverController::class,'driverLogout']);

                Route::post('driver-save-rating',[BookingRatingController::class,'giveRating']);
                Route::post('driver-portal',[FareDistributionsController::class,'driverWalletPortal']);


                Route::get('services-list',[ServicesController::class,'index']);
                Route::post('save-driver-service',[ServicesController::class,'save']);

                Route::post('driver-create-stripe-customer',[DriverStripeController::class,'createCustomer']);

            });

            Route::get('get-cancel-reason',[CancelReasonController::class,'index']);
//            Route::get('get-pages-content',[PagesContentController::class,'index']);
            Route::get('save-user-type',[UserController::class,'saveUserType']);


        });


    });


    Route::get('passenger-help',function(){
       return view('passenger_help');
    });

    Route::get('driver-help',function(){
        return view('driver_help');
    });
});
