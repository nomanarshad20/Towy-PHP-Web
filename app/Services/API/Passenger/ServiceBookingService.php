<?php


namespace App\Services\API\Passenger;


use App\Models\Booking;
use App\Traits\ServiceBookingTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceBookingService
{
    use ServiceBookingTrait;
    public function create($request)
    {
        DB::beginTransaction();

        try {
            $otpCode = mt_rand(1000, 9999);
            $pick_up_date = $pick_up_time = null;

            $bookingArray = [
                'booking_unique_id' => uniqid('TOWY-'),
                'passenger_id' => Auth::user()->id,
                'booking_type' => $request->booking_type,
                'request_type' => 'service',
                'pick_up_area' => $request->pick_up_area,
                'pick_up_latitude' => $request->pick_up_latitude,
                'pick_up_longitude' => $request->pick_up_longitude,
                'pick_up_date' => $pick_up_date,
                'pick_up_time' => $pick_up_time,
                'payment_type' => 'payment_gateway',
                'actual_fare' => 0,
                'estimated_fare' => 0,
                'ride_status' => 0,
                'otp' => $otpCode
            ];

            if($request->booking_id)
            {
                $findBooking = Booking::find($request->booking_id);
                if($findBooking)
                {
                    $bookingTable = $findBooking;

                    $bookingTable->update($bookingArray);
                }
            }
            else{
                $bookingTable = Booking::create($bookingArray);
            }

            $data = $this->bookingResponse($bookingTable);

            $response = ['result' => 'error', 'message' => 'Booking Created Successfully','code'=>200,'data'=>$data];
            return $response;
        }
        catch (\Exception $e) {
            $response = ['result' => 'error', 'message' => 'Error in Create Booking:'.$e,'code'=>500];
            return $response;
        }


    }
}
