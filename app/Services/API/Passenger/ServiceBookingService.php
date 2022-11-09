<?php


namespace App\Services\API\Passenger;


use App\Models\AssignBookingDriver;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Service;
use App\Models\ServiceRate;
use App\Models\User;
use App\Services\API\Driver\DriverService;
use App\Traits\FindServiceDriverTrait;
use App\Traits\ServiceBookingTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceBookingService
{

    use ServiceBookingTrait, FindServiceDriverTrait;

    public function create($request)
    {
        DB::beginTransaction();

        //save booking record
        try {
            $otpCode = mt_rand(1000, 9999);
            $pick_up_date = $pick_up_time = null;

            $bookingArray = [
                'booking_unique_id' => uniqid('TOWY-'),
                'passenger_id' => Auth::user()->id,
                'booking_type' => $request->booking_type,
                'request_type' => 'service',
                'pick_up_area' => $request->pick_up_area,
                'pick_up_latitude' => $request->pick_up_lat,
                'pick_up_longitude' => $request->pick_up_lng,
                'pick_up_date' => $pick_up_date,
                'pick_up_time' => $pick_up_time,
                'payment_type' => 'payment_gateway',
                'actual_fare' => 0,
                'estimated_fare' => 0,
                'ride_status' => 0,
                'otp' => $otpCode
            ];

            if ($request->booking_id) {
                $findBooking = Booking::find($request->booking_id);
                if ($findBooking) {
                    $bookingTable = $findBooking;

                    $bookingTable->update($bookingArray);
                }
            } else {
                $bookingTable = Booking::create($bookingArray);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error in Create Booking:' . $e, 'code' => 500];
            return $response;
        }

        //save services record
        try {
            $serviceType = ServiceRate::first();
            BookingService::where('booking_id', $bookingTable->id)->delete();
            foreach ($request->services as $service) {

                $findService = Service::find($service);

                $serviceArray = [
                    'booking_id' => $bookingTable->id,
                    'service_id' => $service,
                    'base_fare' => $findService->base_rate,
                    'service_per_min_rate' => $serviceType->initial_distance_rate,
                    'service_per_km_rate' => $serviceType->initial_time_rate,
                    'service_time_rate' => $serviceType->service_time_rate,
                ];

                BookingService::create($serviceArray);

            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error in Create Service Record:' . $e, 'code' => 500];
            return $response;
        }

        //save booking detail record
        try {
            $bookingDetailArray = [
                'booking_id' => $bookingTable->id,
                'vehicle_per_km_rate' =>  $serviceType->initial_distance_rate,
                'vehicle_per_min_rate' => $serviceType->initial_time_rate,
                'service_time_rate' => $serviceType->service_time_rate,
                'min_vehicle_fare' => 0
            ];

            if($bookingTable->bookingDetail)
            {
                $bookingDetail = $bookingTable->bookingDetail;

                $bookingDetail->update($bookingDetailArray);

            }
            else{
                $bookingDetail = $bookingTable->bookingDetail()->create($bookingDetailArray);
            }

            $bookingTable->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error in Create Booking Detail Record:' . $e, 'code' => 500];
            return $response;
        }

        $data = $this->bookingResponse($bookingTable);

        DB::commit();
        $response = ['result' => 'success', 'message' => 'Booking Created Successfully', 'code' => 200, 'data' => $data];
        return $response;


    }


    public function findNearestDrivers($booking,$services)
    {
        //calculating estimated fare and getting vehicle type record
        try {

            $gettingDrivers = $this->fetchDrivers($booking,$services);


            if (sizeof($gettingDrivers) > 0) {
                $response = ['result' => 'success', 'data' => $gettingDrivers, 'message' => 'Nearest Drivers Find Successfully'];
            } else {
                $response = ['result' => 'error', 'data' => $booking, 'message' => 'Driver Not Found. Try Again Later', 'code' => 404];
            }


            return $response;

        } catch (\Exception $e) {
//            DB::rollBack();
            $response = ['result' => 'error', 'data' => $booking, 'message' => 'Error in find nearest drivers : ' . $e, 'code' => 500];
            return $response;
        }
    }

    public function saveAvailableDrivers($driversList, $booking)
    {

        try {
            $firstDriver = null;
            foreach ($driversList as $key => $driver) {


                if ($key == 0) {
                    $firstDriver = $driver;
                }

                AssignBookingDriver::create(['booking_id' => $booking['id'],
                    'driver_id' => $driver['id']
                ]);

            }


            $response = ['result' => 'success', 'message' => 'Driver Save', 'code' => 200, 'data' => $firstDriver];
            return $response;
        } catch (\Exception $e) {
            $response = ['result' => 'error', 'message' => 'Error in Saving Driver: ' . $e, 'code' => 500];
            return $response;
        }


    }


    public function calculateEstimatedFare($driversList, $booking)
    {
        $findBooking = Booking::find($booking['id']);
        $findBookingServices = BookingService::where('booking_id', $booking['id'])->get();
        $fareArray = $serviceArray =   array();
        $totalBaseFare = 0;
        foreach ($findBookingServices as $bookingService) {
            $serviceArray[] = [
                'service_name' => $bookingService->service->name,
                'service_id' => $bookingService->service_id,
                'base_fare' => $bookingService->base_fare
            ];

            $totalBaseFare = $totalBaseFare +$bookingService->base_fare;

            $fareArray = [
                'service_per_min_rate' => $bookingService->service_per_min_rate,
                'service_per_km_rate' => $bookingService->service_per_km_rate,
                'service_time_rate' => $bookingService->service_time_rate,
            ];
        }

        $calculateFare = array();
        foreach ($driversList as $key => $driver) {
            $fareDistance = 0;
            $findDriver = User::find($driver['id']);
            $fareDistance = $driver['distance'] *  $findBooking->bookingDetail->vehicle_per_min_rate;

            $serviceProvided = array();

            $getDriverService =  \App\Models\DriverService::where('user_id',$driver['id'])->get();

            foreach($getDriverService as $driverService)
            {
                $serviceProvided[] = [
                    'service_id' => $driverService->service_id,
                    'service_name' => $driverService->service->name
                ];

                $fareDistance = $fareDistance + $driverService->service->base_rate;
            }

            $calculateFare[]  = [
                'driver_id' => $driver['id'],
                'first_name' => $findDriver->first_name,
                'last_name' => $findDriver->last_name,
                'email' => $findDriver->email,
                'distance' => $driver['distance'],
                'estimatedFare' => $fareDistance,
                'service' => $serviceProvided
            ];


        }

        return $calculateFare;

    }
}
