<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CreateBookingRequest;
use App\Models\AssignBookingDriver;
use App\Models\DriversCoordinate;
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

            $bookingData = $this->rideService->saveBooking($request);


            if ($bookingData['result'] == 'error') {
                DB::rollBack();
                return makeResponse('error', $bookingData['message'], 500);
            }

            if($request->booking_type == 'book_now')
            {
                $availableDrivers = $this->rideService->findNearestDrivers($bookingData['data']);

                if ($availableDrivers['result'] == 'error') {
                    return makeResponse('error', $availableDrivers['message'], $availableDrivers['code'], $availableDrivers['data']);
                }

                DB::commit();

                $saveDrivers = $this->rideService->saveAvailableDrivers($availableDrivers['data'], $bookingData['data']);

                if ($saveDrivers['result'] == 'error') {
                    return makeResponse('error', $saveDrivers['message'], $saveDrivers['code']);
                }

                //
                if (isset($saveDrivers['data']) && $saveDrivers['data']) {
                    $driver =  DriversCoordinate::where('driver_id',$saveDrivers['data']['id'])->first();
                    if($driver->status == 1)
                    {
                        $driver->status =3;
                        $driver->save();
                        AssignBookingDriver::where('driver_id',$saveDrivers['data']['id'])
                            ->where('booking_id',$bookingData['data']['id'])
                            ->whereNull('status')
                            ->update(['ride_send_time'=>Carbon::now()->format('Y-m-d H:i:s')]);
                    }

                    $notification_type = 11;
                    $sendNotificationToDriver = $this->rideRequestNotification($saveDrivers['data'],$bookingData['data'],$notification_type);

                }
            }
            else{
                DB::commit();
            }

            return makeResponse('success', $bookingData['message'], 200, $bookingData['data']);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Saving User Record: ' . $e, 500);
        }
    }

}
