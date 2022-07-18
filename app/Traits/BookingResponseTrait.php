<?php


namespace App\Traits;


use App\Models\BookingRating;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait BookingResponseTrait
{
    public function driverBookingResponse($booking)
    {
        $passenger_rating = null;
        $driver_comment = null;

        $driver_rating = null;
        $passenger_comment = null;
        if ($booking->ride_status == 4) {
            if ($booking->is_passenger_rating_given == 1) {
                $passengerRating = BookingRating::where('booking_id', $booking->id)
                    ->where('receiver_id', $booking->passenger_id)->first();

                if ($passengerRating) {
                    $passenger_rating = $passengerRating->rating;
                    $passenger_comment = $passengerRating->description;
                }
            }

            if ($booking->is_passenger_rating_given == 1) {
                $driverRating = BookingRating::where('booking_id', $booking->id)
                    ->where('receiver_id', $booking->driver_id)->first();

                if ($driverRating) {
                    $driver_rating = $driverRating->rating;
                    $driver_comment = $driverRating->description;
                }
            }

        }


        $bookingArray = [
            'id' => $booking->id,
            'booking_unique_id' => $booking->booking_unique_id,
            'passenger_id' => $booking->passenger_id,
            'vehicle_type_id' => $booking->vehicle_type_id,
            'booking_type' => $booking->booking_type,
            'pick_up_area' => $booking->pick_up_area,
            'pick_up_latitude' => $booking->pick_up_latitude,
            'pick_up_longitude' => $booking->pick_up_longitude,
            'pick_up_date' => $booking->pick_up_date,
            'pick_up_time' => $booking->pick_up_time,
            'drop_off_area' => $booking->drop_off_area,
            'drop_off_latitude' => $booking->drop_off_latitude,
            'drop_off_longitude' => $booking->drop_off_longitude,
            'total_distance' => $booking->total_distance,
            'payment_type' => $booking->payment_type,
            'estimated_fare' => $booking->estimated_fare,
            'actual_fare' => $booking->actual_fare,
            'ride_status' => $booking->ride_status,
            'created_at' => $booking->created_at,
            'created_ago' => Carbon::parse($booking->created_at)->diffForHumans(),
            'booking_detail_id' => $booking->bookingDetail->id,
            'waiting_price_per_min' => $booking->bookingDetail->waiting_price_per_min,
            'vehicle_tax' => $booking->bookingDetail->tax_rate,
            'vehicle_per_km_rate' => $booking->bookingDetail->per_km_rate,
            'vehicle_per_min_rate' => $booking->bookingDetail->per_min_rate,
            'min_vehicle_fare' => $booking->bookingDetail->min_fare,
            'passenger_name' => isset($booking->passeger) ? $booking->passeger->name : null,
            'driver_name' => isset($booking->driver) ? $booking->driver->name : null,
            'driver_status' => (int)$booking->driver_status,
            'driver_id' => $booking->driver_id,

            'peak_factor_rate' => $booking->bookingDetail->peak_factor_rate,
            'driver_waiting_time' => $booking->bookingDetail->driver_waiting_time,
            'ride_pick_up_time' => $booking->bookingDetail->ride_pick_up_time,
            'ride_start_time' => $booking->bookingDetail->ride_start_time,
            'ride_end_time' => $booking->bookingDetail->ride_end_time,
            'total_minutes_to_reach_pick_up_point' => $booking->bookingDetail->total_minutes_to_reach_pick_up_point,
            'total_ride_minutes' => $booking->bookingDetail->total_ride_minutes,
            'initial_distance_rate' => $booking->bookingDetail->initial_distance_rate,
            'initial_time_rate' => $booking->bookingDetail->initial_time_rate,
            'total_calculated_distance' => $booking->total_calculated_distance,
            'p2p_before_pick_up_distance' => $booking->p2p_before_pick_up_distance,
            'p2p_after_pick_up_distance' => $booking->p2p_after_pick_up_distance,

            'is_passenger_rating_given' => $booking->is_passenger_rating_given,
            'is_driver_rating_given' => $booking->is_driver_rating_given,

            'passenger_image' => $booking->passenger->image,
            'passenger_mobile_no' => $booking->passenger->mobile_no,
            'passenger_rating' => isset($booking->passenger->rating) ? $booking->passenger->rating->avg('rating') : 0,

            'driver_image' => isset($booking->driver) ? $booking->driver->image : null,
            'driver_mobile_no' => isset($booking->driver) ? $booking->driver->mobile_no : null,
            'driver_rating' => isset($booking->driver) ? $booking->driver->rating->avg('rating') : 0,

            'vehicle_name' => isset($booking->vehicle) ? $booking->vehicle->name : null,
            'vehicle_registration_number' => isset($booking->vehicle) ? $booking->vehicle->registration_number : null,

            'driver_ride_rating' => $driver_rating,
            'driver_comment' => $driver_comment,
            'passenger_ride_rating' => $passenger_rating,
            'passenger_comment' => $passenger_comment,



        ];

        return $bookingArray;
    }
}
