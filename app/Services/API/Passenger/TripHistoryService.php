<?php


namespace App\Services\API\Passenger;


use App\Models\Booking;
use App\Traits\BookingResponseTrait;
use App\Traits\ServiceBookingTrait;
use Illuminate\Support\Facades\Auth;

class TripHistoryService
{

    use BookingResponseTrait,ServiceBookingTrait;
    public function upcomingTrip()
    {
        $bookings = Booking::where('passenger_id',Auth::user()->id)
            ->where('booking_type','book_later')
            ->where('ride_status',0)->orderBy('id','desc')->get();

        $upcomingBookingArray = array();
        foreach($bookings as $booking)
        {
//            $upcomingBookingArray[] = [
//                'booking_id'=>$booking->id,
//                'passenger_id'=>$booking->passenger_id,
//                'passenger_name' => $booking->passenger->name,
//                'passenger_mobile_no' => $booking->passenger->mobile_no,
//                'booking_type' => $booking->booking_type,
//                'pick_up_date' => Carbon::parse($booking->pick_up_date)->format('d M Y'),
//                'pick_up_time' => Carbon::parse($booking->pick_up_time)->format('H:i:s A'),
//                'payment_type' => $booking->payment_type,
//                'estimated_fare' => $booking->estimated_fare,
//                'vehicle_id' => $booking->vehicle_id,
//                'vehicle_number' => $booking->vehicle->registration_number,
//                'vehicle_model' => $booking->model,
//                'vehicle_model_year' =>$booking->model_year,
//                'actual_fare' => $booking->actual_fare
//
//            ];

            if($booking->request_type == 'service')
            {
                $upcomingBookingArray[] = $this->passengerServiceTripHistoryResponse($booking);

            }
            else{
                $upcomingBookingArray[] = $this->passengerTripHistoryResponse($booking);

            }

        }

        return $upcomingBookingArray;

    }


    public function pastTrip()
    {
        $bookings = Booking::where('passenger_id',Auth::user()->id)
            ->whereIn('ride_status',[2,3,4,5])->orderBy('id','desc')
            ->get();

        $pastBookingArray = array();
        foreach($bookings as $booking)
        {
            if($booking->request_type == 'service')
            {
                $pastBookingArray[] = $this->passengerServiceTripHistoryResponse($booking);
            }
            else{
                $pastBookingArray[] = $this->passengerTripHistoryResponse($booking);
            }
        }

        return $pastBookingArray;
    }
}
