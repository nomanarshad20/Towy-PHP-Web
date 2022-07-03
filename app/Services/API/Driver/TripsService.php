<?php


namespace App\Services\API\Driver;


use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TripsService
{
    public function upcomingTrip()
    {
        $bookings = Booking::where('driver_id',Auth::user()->id)
            ->where('booking_type','book_later')
            ->where('ride_status',1)->get();

        $upcomingBookingArray = array();
        foreach($bookings as $booking)
        {
            $upcomingBookingArray[] = [
                'booking_id'=>$booking->id,
                'passenger_id'=>$booking->passenger_id,
                'passenger_name' => $booking->passenger->name,
                'passenger_mobile_no' => $booking->passenger->mobile_no,
                'booking_type' => $booking->booking_type,
                'pick_up_date' => Carbon::parse($booking->pick_up_date)->format('d M Y'),
                'pick_up_time' => Carbon::parse($booking->pick_up_time)->format('H:i:s A'),
                'payment_type' => $booking->payment_type,
                'estimated_fare' => $booking->estimated_fare,
                'vehicle_id' => $booking->vehicle_id,
                'vehicle_number' => $booking->vehicle->registration_number,
                'vehicle_model' => $booking->model,
                'vehicle_model_year' =>$booking->model_year,
                'actual_fare' => $booking->actual_fare

            ];
        }

        return $upcomingBookingArray;

    }


    public function pastTrip()
    {
        $bookings = Booking::where('driver_id',Auth::user()->id)
            ->where('ride_status',4)->get();

        $pastBookingArray = array();
        foreach($bookings as $booking)
        {
            $pastBookingArray[] = [
                'booking_id'=>$booking->id,
                'passenger_id'=>$booking->passenger_id,
                'passenger_name' => $booking->passenger->name,
                'passenger_mobile_no' => $booking->passenger->mobile_no,
                'booking_type' => $booking->booking_type,
                'pick_up_date' => isset($booking->pick_up_date) ? Carbon::parse($booking->pick_up_date)->format('d M Y'):'N/A',
                'pick_up_time' => isset($booking->pick_up_time) ? Carbon::parse($booking->pick_up_time)->format('H:i:s A'):'N/A',
                'payment_type' => $booking->payment_type,
                'estimated_fare' => $booking->estimated_fare,
                'vehicle_id' => $booking->vehicle_id,
                'vehicle_number' => $booking->vehicle->registration_number,
                'vehicle_model' => $booking->model,
                'vehicle_model_year' =>$booking->model_year,
                'actual_fare' => $booking->actual_fare
            ];
        }

        return $pastBookingArray;
    }
}