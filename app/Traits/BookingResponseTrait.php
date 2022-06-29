<?php


namespace App\Traits;


use Carbon\Carbon;

trait BookingResponseTrait
{
    public function driverBookingResponse($booking)
    {
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
            'passenger_name' => isset($booking->passeger) ? $booking->passeger->name:null,
            'driver_name' => isset($booking->driver) ? $booking->driver->name:null,
            'driver_status' => (int)$booking->driver_status,
            'driver_id' =>  $booking->driver_id

        ];

        return $bookingArray;
    }
}
