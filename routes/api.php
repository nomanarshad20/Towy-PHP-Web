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

            Route::namespace('Auth')->prefix('passenger')->group(function () {
                Route::post('login', [AuthController::class, 'login']);
                Route::post('register', [AuthController::class, 'register']);
                Route::post('check-phone-number', [AuthController::class, 'checkPhoneNumber']);

            });



        });

        Route::namespace('Common')->prefix('common')->group(function () {

            Route::post('social-login', [AuthController::class, 'socialLoginMobile']);
            Route::post('forget-password',[AuthController::class,'forgetPassword']);

        });

        Route::namespace('Driver')->group(function () {
            Route::namespace('Auth')->group(function () {
                Route::post('driver-login', [DriverAuthController::class, 'login']);
                Route::post('driver-register', [DriverAuthController::class, 'register']);

            });
        });


        Route::middleware('auth:sanctum')->group(function () {
            Route::namespace('Passenger')->prefix('passenger')->group(function () {
                Route::post('user-info-update', [AuthController::class, 'userDataUpdate']);
                Route::post('find-near-drivers', [FindDriversController::class, 'findDriversByVehicle']);
                Route::post('calculating-distance-and-fare',[RideInitialDistanceController::class,'findDistance']);
                Route::post('create-booking',[CreateRideController::class,'booking']);
                Route::post('logout',[AuthController::class,'logout']);
                Route::post('update-password',[AuthController::class,'passwordUpdate']);
            });

            Route::namespace('Driver')->group(function(){
                Route::post('driver-save-information',[DriverInformationController::class,'save']);
                Route::post('driver-save-document',[DriverInformationController::class,'saveDocument']);
                Route::post('driver-save-vehicle-information',[DriverInformationController::class,'saveVehicleInformation']);
                Route::post('driver-profile-update',[ProfileController::class,'saveProfile']);

                Route::post('driver-save-location',[DriverLocationController::class,'save']);

                Route::get('driver-trip-history',[TripsController::class,'index']);

                Route::get('driver-cancel-ride',[DriverCancelRideController::class,'cancelRide']);
            });
    
            Route::get('get-cancel-reason',[CancelReasonController::class,'index']);

        });
    });


});
