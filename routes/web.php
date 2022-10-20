<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\FranchiseController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\VehicleFareController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PeakFactorController;
use App\Http\Controllers\Admin\CancelReasonController;
use App\Http\Controllers\Admin\VoucherCodeController;
use App\Http\Controllers\Admin\BannerImageController;
use App\Http\Controllers\Admin\PassengerController;
//use App\Http\Controllers\Admin\HtmlPagesContentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
////    return view('admin.dashboard.dashboard');
//});

//Auth::routes();

Route::namespace('Admin')->group(function () {
    Route::namespace("Auth")->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('/', [LoginController::class, 'loginPage'])->name('loginPage');
            Route::post('login', [LoginController::class, 'login'])->name('loginUser');

            Route::get('/forget-password', [ForgotPasswordController::class, 'forgetPasswordForm'])->name('forgetPasswordForm');
            Route::post('/forget-password', [ForgotPasswordController::class, 'forgetPassword'])->name('forgetPassword');

            Route::get('reset/password/{token}', [ForgotPasswordController::class, 'resetPassword'])->name('resetPassword');
            Route::post('change-password', [ForgotPasswordController::class, 'changePassword'])->name('changePassword');

        });

        Route::middleware(['auth'])->group(function () {
            Route::get('logout', [LogoutController::class, 'logout'])->name('logoutUser');
        });
    });


    Route::middleware(['role:administrator'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('adminDashboard');

        Route::get('driver-listing',[DriverController::class,'index'])->name('driverListing');
        Route::get('driver-create',[DriverController::class,'create'])->name('driverCreate');
        Route::post('driver-save',[DriverController::class,'save'])->name('driverSave');
        Route::get('driver-edit/{id}',[DriverController::class,'edit'])->name('driverEdit');
        Route::post('driver-update',[DriverController::class,'update'])->name('driverUpdate');
        Route::post('driver-delete',[DriverController::class,'delete'])->name('driverDelete');
        Route::get('driver-change-status',[DriverController::class,'changeStatus'])->name('driverChangeStatus');
        Route::get('driver-delete-image',[DriverController::class,'deleteImage'])->name('driverDeleteImage');
        Route::any('driver-portal/{id?}',[DriverController::class,'portal'])->name('driverPortal');
        Route::post('payReceiveFromDriver',[DriverController::class,'payReceiveFromDriver'])->name('payReceiveFromDriver');
        Route::get('driver-approval-request',[DriverController::class,'approvalRequest'])->name('driverApprovalRequest');

        Route::get('passenger-listing',[PassengerController::class,'index'])->name('passengerListing');
        Route::get('passenger-create',[PassengerController::class,'create'])->name('passengerCreate');
        Route::post('passenger-save',[PassengerController::class,'save'])->name('passengerSave');
        Route::get('passenger-edit/{id}',[PassengerController::class,'edit'])->name('passengerEdit');
        Route::post('passenger-update',[PassengerController::class,'update'])->name('passengerUpdate');
        Route::post('passenger-delete',[PassengerController::class,'delete'])->name('passengerDelete');
        Route::get('passenger-change-status',[PassengerController::class,'changeStatus'])->name('passengerChangeStatus');
        Route::get('passenger-delete-image',[PassengerController::class,'deleteImage'])->name('passengerDeleteImage');

        Route::get('franchise-listing',[FranchiseController::class,'index'])->name('franchiseListing');
        Route::get('franchise-create',[FranchiseController::class,'create'])->name('franchiseCreate');
        Route::post('franchise-save',[FranchiseController::class,'save'])->name('franchiseSave');
        Route::get('franchise-edit/{id}',[FranchiseController::class,'edit'])->name('franchiseEdit');
        Route::post('franchise-update',[FranchiseController::class,'update'])->name('franchiseUpdate');
        Route::post('franchise-delete',[FranchiseController::class,'delete'])->name('franchiseDelete');
        Route::get('franchise-change-status',[FranchiseController::class,'changeStatus'])->name('franchiseChangeStatus');
        Route::get('franchise-delete-image',[FranchiseController::class,'deleteImage'])->name('franchiseDeleteImage');

        Route::get('vehicle-listing',[VehicleController::class,'index'])->name('vehicleListing');
        Route::get('vehicle-create',[VehicleController::class,'create'])->name('vehicleCreate');
        Route::post('vehicle-save',[VehicleController::class,'save'])->name('vehicleSave');
        Route::get('vehicle-edit/{id}',[VehicleController::class,'edit'])->name('vehicleEdit');
        Route::post('vehicle-update',[VehicleController::class,'update'])->name('vehicleUpdate');
        Route::post('vehicle-delete',[VehicleController::class,'delete'])->name('vehicleDelete');
//        Route::get('vehicle-change-status',[VehicleController::class,'changeStatus'])->name('vehicleChangeStatus');
        Route::get('vehicle-delete-image',[VehicleController::class,'deleteImage'])->name('vehicleDeleteImage');


        Route::get('vehicle-type-listing',[VehicleTypeController::class,'index'])->name('vehicleTypeListing');
        Route::get('vehicle-type-create',[VehicleTypeController::class,'create'])->name('vehicleTypeCreate');
        Route::post('vehicle-type-save',[VehicleTypeController::class,'save'])->name('vehicleTypeSave');
        Route::get('vehicle-type-edit/{id}',[VehicleTypeController::class,'edit'])->name('vehicleTypeEdit');
        Route::post('vehicle-type-update',[VehicleTypeController::class,'update'])->name('vehicleTypeUpdate');
        Route::post('vehicle-type-delete',[VehicleTypeController::class,'delete'])->name('vehicleTypeDelete');
        Route::get('vehicle-type-change-status',[VehicleTypeController::class,'changeStatus'])->name('vehicleTypeChangeStatus');
        Route::get('vehicle-type-delete-image',[VehicleTypeController::class,'deleteImage'])->name('vehicleTypeDeleteImage');

        Route::get('booking-listing',[BookingController::class,'index'])->name('bookingListing');
        Route::get('booking-create',[BookingController::class,'create'])->name('bookingCreate');
        Route::post('booking-save',[BookingController::class,'save'])->name('bookingSave');
        Route::get('booking-edit/{id}',[BookingController::class,'edit'])->name('bookingEdit');
        Route::post('booking-update',[BookingController::class,'update'])->name('bookingUpdate');
        Route::post('booking-delete',[BookingController::class,'delete'])->name('bookingDelete');
        Route::get('booking-detail/{id}',[BookingController::class,'detail'])->name('bookingDetail');

        Route::get('vehicle-fare-setting',[VehicleFareController::class,'create'])->name('vehicleFareSetting');
        Route::post('save-vehicle-fare',[VehicleFareController::class,'save'])->name('saveVehicleFare');

        Route::get('settings',[SettingController::class,'index'])->name('setting');
        Route::post('save-settings',[SettingController::class,'save'])->name('saveSetting');

        Route::get('peak-factor-listing',[PeakFactorController::class,'index'])->name('peakFactorListing');
        Route::get('peak-factor-create',[PeakFactorController::class,'create'])->name('peakFactorCreate');
        Route::post('peak-factor-save',[PeakFactorController::class,'save'])->name('peakFactorSave');
        Route::get('peak-factor-edit/{id}',[PeakFactorController::class,'edit'])->name('peakFactorEdit');
        Route::post('peak-factor-update',[PeakFactorController::class,'update'])->name('peakFactorUpdate');
        Route::post('peak-factor-delete',[PeakFactorController::class,'delete'])->name('peakFactorDelete');

        Route::get('voucher-code-listing',[VoucherCodeController::class,'index'])->name('voucherCodeListing');
        Route::get('voucher-code-create',[VoucherCodeController::class,'create'])->name('voucherCodeCreate');
        Route::post('voucher-code-save',[VoucherCodeController::class,'save'])->name('voucherCodeSave');
        Route::get('voucher-code-edit/{id}',[VoucherCodeController::class,'edit'])->name('voucherCodeEdit');
        Route::post('voucher-code-update',[VoucherCodeController::class,'update'])->name('voucherCodeUpdate');
        Route::post('voucher-code-delete',[VoucherCodeController::class,'delete'])->name('voucherCodeDelete');
        Route::get('send-voucher-code-passenger/{id}',[VoucherCodeController::class,'sendToPassengerView'])->name('voucherCodeSend');
        Route::post('send-voucher-code-passenger',[VoucherCodeController::class,'send'])->name('voucherCodeSendPassenger');

        Route::get('banner-image-listing',[BannerImageController::class,'index'])->name('bannerImageListing');
        Route::get('create-banner-image',[BannerImageController::class,'create'])->name('bannerImageCreate');
        Route::post('save-banner-image',[BannerImageController::class,'save'])->name('bannerImageSave');
        Route::post('delete-banner-image',[BannerImageController::class,'delete'])->name('bannerImageDelete');
        Route::get('banner-image-change-status',[BannerImageController::class,'changeStatus'])->name('bannerImageChangeStatus');

        Route::get('cancel-reason-listing',[CancelReasonController::class,'index'])->name('cancelReasonListing');
        Route::get('cancel-reason-create',[CancelReasonController::class,'create'])->name('cancelReasonCreate');
        Route::post('cancel-reason-save',[CancelReasonController::class,'save'])->name('cancelReasonSave');
        Route::get('cancel-reason-edit/{id}',[CancelReasonController::class,'edit'])->name('cancelReasonEdit');
        Route::post('cancel-reason-update',[CancelReasonController::class,'update'])->name('cancelReasonUpdate');
        Route::post('cancel-reason-delete',[CancelReasonController::class,'delete'])->name('cancelReasonDelete');


//        Route::get('content-pages',[HtmlPagesContentController::class,'index'])->name('contentPages');
//        Route::get('create-content-pages',[HtmlPagesContentController::class,'create'])->name('contentPagesCreate');
//        Route::post('save-content-pages',[HtmlPagesContentController::class,'store'])->name('contentPagesSave');
//        Route::get('edit-content-pages/{id}',[HtmlPagesContentController::class,'edit'])->name('contentPagesEdit');
//        Route::post('update-content-pages',[HtmlPagesContentController::class,'update'])->name('contentPagesUpdate');
//        Route::get('delete-content-pages/{id}',[HtmlPagesContentController::class,'destroy'])->name('contentPagesDelete');


    });

    Route::get('test',[\App\Http\Controllers\Admin\TestController::class,'test']);

});


