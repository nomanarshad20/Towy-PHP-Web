<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CreateBookingRequest;
use App\Traits\SendFirebaseNotificationTrait;
use Illuminate\Http\Request;

//use App\Traits\FindDriversTraits;
use App\Models\User;
use App\Services\API\Passenger\RideService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\VehicleFareSetting;

class CreateRideController extends Controller
{
    use SendFirebaseNotificationTrait;
    public $rideService;

    public function __construct(RideService $rideService)
    {
        $this->rideService = $rideService;
    }

    public function booking(CreateBookingRequest $request)
    {
        try {

            DB::beginTransaction();


//            if (Auth::user()->user_type != 1)
//                return makeResponse('error', 'Invalid User Type,Only Passenger Can Make A Booking Request', 422);


            $bookingData = $this->rideService->saveBooking($request);


            if ($bookingData['result'] == 'error') {
                DB::rollBack();
                return makeResponse('error', $bookingData['message'], 500);
            }

            DB::commit();

            $availableDrivers = $this->rideService->findNearestDrivers($bookingData['data']);

            if ($availableDrivers['result'] == 'error') {
                return makeResponse('error', $availableDrivers['message'], $availableDrivers['code'], $availableDrivers['data']);
            }

            $saveDrivers = $this->rideService->saveAvailableDrivers($availableDrivers['data'], $bookingData['data']);

            if ($saveDrivers['result'] == 'error') {
                return makeResponse('error', $saveDrivers['message'], $saveDrivers['code']);
            }


            //
            if (isset($saveDrivers['data']) && $saveDrivers['data']) {

                $driver =  $saveDrivers['data']['id']->driver->update(['status'=>3]);

                $notification_type = 11;
                $sendNotificationToDriver = $this->rideRequestNotification($saveDrivers['data'],$bookingData['data'],$notification_type);
            }


            return makeResponse('success', $bookingData['message'], 200, $bookingData['data']);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Saving User Record: ' . $e, 500);
        }
    }

}
