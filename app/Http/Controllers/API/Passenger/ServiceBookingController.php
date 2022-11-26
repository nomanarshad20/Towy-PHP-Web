<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Passenger\ServiceBookingCreate;
use App\Models\AssignBookingDriver;
use App\Models\Booking;
use App\Models\DriversCoordinate;
use App\Services\API\Passenger\ServiceBookingService;
use App\Services\API\Passenger\StripeService;
use App\Traits\FindServiceDriverTrait;
use App\Traits\SendFirebaseNotificationTrait;
use App\Traits\ServiceBookingTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceBookingController extends Controller
{
    use SendFirebaseNotificationTrait, ServiceBookingTrait;

    public $serviceBooking;
    public $stripeService;

    public function __construct(ServiceBookingService $bookingService, StripeService $stripeService)
    {
        $this->serviceBooking = $bookingService;
        $this->stripeService = $stripeService;
    }

    public function create(ServiceBookingCreate $request)
    {
        if (!Auth::user()->stripe_customer_id) {
            return makeResponse('error', 'Customer Card Not Found. Please Add Your Card To Make Charge', 500);
        }


        DB::beginTransaction();
        try {
            $createBooking = $this->serviceBooking->create($request);
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Create Booking: ' . $e, '500');
        }

        if ($createBooking['result'] == 'error') {
            DB::rollBack();
            return makeResponse('error', $createBooking['message'], 500);
        }

        //find drivers according to pick up lat and lng
        try {
            $availableDrivers = $this->serviceBooking->findNearestDrivers($createBooking['data'], $request->services);

            if ($availableDrivers['result'] == 'error') {
                DB::rollBack();
                return makeResponse('error', $availableDrivers['message'], $availableDrivers['code'], $availableDrivers['data']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Driver Find: ' . $e, '500');
        }

        try {
            $saveDrivers = $this->serviceBooking->saveAvailableDrivers($availableDrivers['data'], $createBooking['data'],);

            if ($saveDrivers['result'] == 'error') {
                DB::rollBack();
                return makeResponse('error', $saveDrivers['message'], $saveDrivers['code']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Driver Save: ' . $e, '500');
        }

        try {
            $fareArray = $this->serviceBooking->calculateEstimatedFare($availableDrivers['data'], $createBooking['data']);
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Create Estimated Fare: ' . $e, '500');
        }
        DB::commit();

        $data = [
            'bookingRecord' => $createBooking['data'],
            'driverList' => $fareArray
        ];

        return makeResponse('success', 'Booking Created', 200, $data);
    }

    public function sendRideRequest(Request $request)
    {
        if (!Auth::user()->stripe_customer_id) {
            return makeResponse('error', 'Customer Card Not Found. Please Add Your Card To Make Charge', 500);
        }

        DB::beginTransaction();
        $updateServices = $this->serviceBooking->updateServiceForRequest($request);

        if ($updateServices['result'] == 'error') {
            DB::rollBack();
            return makeResponse($updateServices['result'], $updateServices['message'], $updateServices['code']);
        }


        try {
            $booking['id'] = $request->booking_id;
            $driverArray = array([
                'id' => $request->driver['driver_id'], 'distance' => $request->driver['distance']
            ]);

            $fareArray = $this->serviceBooking->calculateEstimatedFare($driverArray, $booking);
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Create Estimated Fare: ' . $e, '500');
        }

        $findBooking = Booking::find($request->booking_id);
        $findBooking->update(['estimated_fare' => $fareArray[0]['total_fare']]);

        //hold amount on user account
        $holdFare = $this->stripeService->holdAmountForService($fareArray[0]['total_fare'],$findBooking);

        if (isset($holdFare['type']) && $holdFare['type'] == 'error') {
            return makeResponse('error', $holdFare['message'], 500);
        }


        try {


            $driver = DriversCoordinate::where('driver_id', $fareArray[0]['driver_id'])->first();
            if ($driver->status == 1) {
                $driver->status = 3;
                $driver->save();
                AssignBookingDriver::where('driver_id', $fareArray[0]['driver_id'])
                    ->where('booking_id', $request->booking_id)
                    ->whereNull('status')
                    ->update(['ride_send_time' => Carbon::now()->format('Y-m-d H:i:s')]);
            }

            Booking::where('id', $request->booking_id)->update(['stripe_charge_id' => $holdFare]);


            $bookingRecord = $this->bookingResponse($findBooking);

            $notification_type = 13;
            $sendNotificationToDriver = $this->serviceRideRequestNotification($driver->user->fcm_token, $bookingRecord, $notification_type);
            DB::commit();

            return makeResponse('success', 'Booking Created Successfully', 200, $bookingRecord);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Sending Notification To Driver: ' . $e, '500');
        }


    }
}
