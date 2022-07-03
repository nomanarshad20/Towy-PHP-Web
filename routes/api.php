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

//        Route::namespace('Common')->group(function () {
//
//            Route::post('social-login', [AuthController::class, 'socialLoginMobile']);
//            Route::post('forget-password',[AuthController::class,'forgetPassword']);
//
//        });

        Route::namespace('Driver')->group(function () {
            Route::namespace('Auth')->group(function () {
                Route::post('driver-login', [DriverAuthController::class, 'login']);
                Route::post('driver-register', [DriverAuthController::class, 'register']);

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

                Route::post('driver-save-location',[DriverLocationController::class,'save']);

                Route::get('driver-trip-history',[TripsController::class,'index']);

                Route::post('driver-cancel-ride',[DriverCancelRideController::class,'cancelRide']);

                Route::get('change-driver-status',[DriverController::class,'changeDriverOnlineStatus']);

                Route::get('get-driver-current-status',[DriverController::class,'getCurrentStatus']);
                Route::get('driver-logout',[DriverController::class,'driverLogout']);

            });

            Route::get('get-cancel-reason',[CancelReasonController::class,'index']);

        });
    });


});
