<?php


namespace App\Services\API\Passenger;


use App\Models\Booking;
use App\Traits\BookingResponseTrait;
use Illuminate\Support\Facades\Auth;

class CurrentStatusService
{
    use BookingResponseTrait;

    public function index()
    {
//        $booking =  Booking::where('ride_status',1)
//            ->where('passenger_id',Auth::user()->id)->first();

//        $booking = Booking::where('passenger_id', Auth::user()->id)
////                ->where('ride_status', 1)
//            ->where(function ($query){
//                $query->where('ride_status',1)
//                    ->orwhere(function($query1){
//                        $query1->where('is_passenger_rating_given',0);
//                    });
//            })
//            ->whereNotNull('driver_status')
//            ->orderBy('id','desc')
//            ->first();

        $booking = Booking::where('passenger_id', Auth::user()->id)
            ->whereIn('ride_status', [1, 4])
            ->where('is_passenger_rating_given', 0)
            ->orderBy('id', 'desc')
            ->first();


        $bookingResponse = null;
        if ($booking) {
            $bookingResponse = $this->driverBookingResponse($booking);
            return makeResponse('success', 'Booking Found', 200, $bookingResponse);
        }
        return makeResponse('error', 'Booking Not Found', 404);


    }
}
