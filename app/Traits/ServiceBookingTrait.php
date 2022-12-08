<?php

namespace App\Traits;

use App\Models\BookingRating;
use App\Models\BookingService;
use Carbon\Carbon;

trait ServiceBookingTrait
{
    /**
     * Create regular or static methods here
     */


    public function bookingResponse($booking)
    {
        $passenger_rating = null;
        $driver_comment = null;

        $driver_rating = null;
        $passenger_comment = null;

        if ($booking->ride_status == 4) {
            if ($booking->is_passenger_rating_given == 1) {
                $passengerRating = BookingRating::where('booking_id', $booking->id)
                    ->where('giver_id', $booking->passenger_id)->first();

                if ($passengerRating) {
                    $passenger_rating = $passengerRating->rating;
                    $passenger_comment = $passengerRating->description;
                }
            }

            if ($booking->is_driver_rating_given == 1) {
                $driverRating = BookingRating::where('booking_id', $booking->id)
                    ->where('giver_id', $booking->driver_id)->first();

                if ($driverRating) {
                    $driver_rating = $driverRating->rating;
                    $driver_comment = $driverRating->description;
                }
            }

        }

        $servicesArray = array();
        $services = BookingService::where('booking_id', $booking->id)->get();

        foreach ($services as $service) {
            $servicesArray[] = ['id' => $service->service_id, 'name' => $service->service->name,
                'quantity' => $service->quantity, 'base_fare' => $service->base_fare
            ];
        }


        $bookingArray = [
            'id' => $booking->id,
            'booking_unique_id' => $booking->booking_unique_id,
            'passenger_id' => $booking->passenger_id,
            'franchise_id' => $booking->franchise_id,
            'vehicle_type_id' => $booking->vehicle_type_id,
            'booking_type' => $booking->booking_type,
            'pick_up_area' => $booking->pick_up_area,
            'pick_up_latitude' => $booking->pick_up_latitude,
            'pick_up_longitude' => $booking->pick_up_longitude,

            'total_distance' => $booking->total_distance,
            'payment_type' => $booking->payment_type,
            'estimated_fare' => $booking->estimated_fare,
            'actual_fare' => $booking->actual_fare,
            'ride_status' => $booking->ride_status,
            'created_at' => $booking->created_at,
            'created_ago' => Carbon::parse($booking->created_at)->diffForHumans(),

            'booking_detail_id' => $booking->bookingDetail->id,
            'waiting_price_per_min' => $booking->bookingDetail->waiting_price_per_min,
            'vehicle_tax' => $booking->bookingDetail->vehicle_tax,
            'service_per_km_rate' => $booking->bookingDetail->vehicle_per_km_rate,
            'service_per_min_rate' => $booking->bookingDetail->vehicle_per_min_rate,
            'base_fare_service' => $booking->bookingDetail->min_vehicle_fare,
            'passenger_first_name' => isset($booking->passenger) ? $booking->passenger->first_name : null,
            'passenger_last_name' => isset($booking->passenger) ? $booking->passenger->last_name : null,
            'driver_first_name' => isset($booking->driver) ? $booking->driver->first_name : null,
            'driver_last_name' => isset($booking->driver) ? $booking->driver->last_name : null,
            'driver_status' => (int)$booking->driver_status,
            'driver_id' => $booking->driver_id,

            'peak_factor_rate' => $booking->bookingDetail->peak_factor_rate,
            'driver_waiting_time' => $booking->bookingDetail->driver_waiting_time,
            'ride_pick_up_time' => $booking->bookingDetail->ride_pickup_time,
            'service_start_time' => $booking->bookingDetail->ride_start_time,
            'service_end_time' => $booking->bookingDetail->ride_end_time,
            'total_minutes_to_reach_pick_up_point' => $booking->bookingDetail->total_minutes_to_reach_pick_up_point,
            'total_service_minutes' => $booking->bookingDetail->total_ride_minutes,
            'initial_distance_rate' => $booking->bookingDetail->initial_distance_rate,
            'initial_time_rate' => $booking->bookingDetail->initial_time_rate,
            'total_calculated_distance' => $booking->total_calculated_distance,
            'p2p_before_pick_up_distance' => $booking->bookingDetail->p2p_before_pick_up_distance,
            'p2p_after_pick_up_distance' => $booking->bookingDetail->p2p_after_pick_up_distance,

            'is_passenger_rating_given' => $booking->is_passenger_rating_given,
            'is_driver_rating_given' => $booking->is_driver_rating_given,

            'passenger_image' => $booking->passenger->image,
            'passenger_mobile_no' => $booking->passenger->mobile_no,
            'passenger_rating' => isset($booking->passenger->rating) ? (float)$booking->passenger->rating->avg('rating') : 0,

            'driver_image' => isset($booking->driver) ? $booking->driver->image : null,
            'driver_mobile_no' => isset($booking->driver) ? $booking->driver->mobile_no : null,
            'driver_rating' => isset($booking->driver->rating) ? (float)$booking->driver->rating->avg('rating') : 0,

            'driver_rating_from_passenger' => $driver_rating,
            'driver_comment_from_passenger' => $driver_comment,
            'passenger_rating_from_driver' => $passenger_rating,
            'passenger_comment_from_driver' => $passenger_comment,

            'services' => $servicesArray

        ];

        return $bookingArray;
    }


    public function driverServiceTripHistoryResponse($booking)
    {
        $servicesArray = array();
        $services = BookingService::where('booking_id', $booking->id)->get();

        foreach ($services as $service) {
            $servicesArray[] = ['id' => $service->service_id, 'name' => $service->service->name,
                'quantity' => $service->quantity, 'base_fare' => $service->base_fare
            ];
        }


        $passenger_rating = null;
        $driver_comment = null;
        $driver_rating = null;
        $passenger_comment = null;

        if ($booking->ride_status == 4) {
            if ($booking->is_passenger_rating_given == 1) {
                $passengerRating = BookingRating::where('booking_id', $booking->id)
                    ->where('giver_id', $booking->passenger_id)->first();

                if ($passengerRating) {
                    $passenger_rating = $passengerRating->rating;
                    $passenger_comment = $passengerRating->description;
                }
            }

            if ($booking->is_driver_rating_given == 1) {
                $driverRating = BookingRating::where('booking_id', $booking->id)
                    ->where('giver_id', $booking->driver_id)->first();

                if ($driverRating) {
                    $driver_rating = $driverRating->rating;
                    $driver_comment = $driverRating->description;
                }
            }

        }


        $cancelReason = null;

        if ($booking->ride_status == 5) {
            if ($booking->cancel) {
                $cancelReason = $booking->cancel->reason;
            }

        }


        $bookingArray = [
            'id' => $booking->id,
            'booking_unique_id' => $booking->booking_unique_id,
            'franchise_id' => $booking->franchise_id,

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
//            'created_ago' => Carbon::parse($booking->created_at)->diffForHumans(),


            'passenger_first_name' => isset($booking->passenger) ? $booking->passenger->first_name : null,
            'passenger_last_name' => isset($booking->passenger) ? $booking->passenger->last_name : null,
            'passenger_id' => $booking->passenger_id,

//            'driver_name' => isset($booking->driver) ? $booking->driver->name : null,
//            'driver_status' => (int)$booking->driver_status,
//            'driver_id' => $booking->driver_id,

            'is_passenger_rating_given' => $booking->is_passenger_rating_given,
            'is_driver_rating_given' => $booking->is_driver_rating_given,

            'passenger_image' => $booking->passenger->image,
            'passenger_mobile_no' => $booking->passenger->mobile_no,
            'passenger_rating' => isset($booking->passenger->rating) ? (float)$booking->passenger->rating->avg('rating') : 0,

//            'driver_image' => isset($booking->driver) ? $booking->driver->image : null,
//            'driver_mobile_no' => isset($booking->driver) ? $booking->driver->mobile_no : null,
//            'driver_rating' => isset($booking->driver) ? (float)$booking->driver->rating->avg('rating') : 0,

            'vehicle_name' => isset($booking->vehicle) ? $booking->vehicle->name : null,
            'vehicle_registration_number' => isset($booking->vehicle) ? $booking->vehicle->registration_number : null,

            'driver_rating_from_passenger' => $driver_rating,
            'driver_comment_from_passenger' => $driver_comment,
            'passenger_rating_from_driver' => $passenger_rating,
            'passenger_comment_from_driver' => $passenger_comment,

            'otp' => $booking->otp,

            'cancel_reason' => $cancelReason,

            'fine_amount' => $booking->fine_amount,

            'services' => $servicesArray


        ];

        return $bookingArray;
    }

    public function passengerServiceTripHistoryResponse($booking)
    {
        $servicesArray = array();
        $services = BookingService::where('booking_id', $booking->id)->get();

        foreach ($services as $service) {
            $servicesArray[] = ['id' => $service->service_id, 'name' => $service->service->name,
                'quantity' => $service->quantity, 'base_fare' => $service->base_fare
            ];
        }

        $passenger_rating = null;
        $driver_comment = null;
        $driver_rating = null;
        $passenger_comment = null;

        if ($booking->ride_status == 4) {
            if ($booking->is_passenger_rating_given == 1) {
                $passengerRating = BookingRating::where('booking_id', $booking->id)
                    ->where('giver_id', $booking->passenger_id)->first();

                if ($passengerRating) {
                    $passenger_rating = $passengerRating->rating;
                    $passenger_comment = $passengerRating->description;
                }
            }

            if ($booking->is_driver_rating_given == 1) {
                $driverRating = BookingRating::where('booking_id', $booking->id)
                    ->where('giver_id', $booking->driver_id)->first();

                if ($driverRating) {
                    $driver_rating = $driverRating->rating;
                    $driver_comment = $driverRating->description;
                }
            }

        }


        $cancelReason = null;

        if ($booking->ride_status == 2) {
            if ($booking->cancel) {
                $cancelReason = $booking->cancel->reason;
            }
        }


        $bookingArray = [
            'id' => $booking->id,
            'booking_unique_id' => $booking->booking_unique_id,
            'franchise_id' => $booking->franchise_id,

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
//            'created_ago' => Carbon::parse($booking->created_at)->diffForHumans(),


//            'passenger_name' => isset($booking->passenger) ? $booking->passenger->name : null,
//            'passenger_id' => $booking->passenger_id,

            'driver_first_name' => isset($booking->driver) ? $booking->driver->first_name : null,
            'driver_last_name' => isset($booking->driver) ? $booking->driver->last_name : null,
//            'driver_status' => (int)$booking->driver_status,
            'driver_id' => $booking->driver_id,

            'is_passenger_rating_given' => $booking->is_passenger_rating_given,
            'is_driver_rating_given' => $booking->is_driver_rating_given,

//            'passenger_image' => $booking->passenger->image,
//            'passenger_mobile_no' => $booking->passenger->mobile_no,
//            'passenger_rating' => isset($booking->passenger->rating) ? (float)$booking->passenger->rating->avg('rating') : 0,

            'driver_image' => isset($booking->driver) ? $booking->driver->image : null,
            'driver_mobile_no' => isset($booking->driver) ? $booking->driver->mobile_no : null,
            'driver_rating' => isset($booking->driver->rating) ? (float)$booking->driver->rating->avg('rating') : 0,

            'vehicle_name' => isset($booking->vehicle) ? $booking->vehicle->name : null,
            'vehicle_registration_number' => isset($booking->vehicle) ? $booking->vehicle->registration_number : null,

            'driver_rating_from_passenger' => $driver_rating,
            'driver_comment_from_passenger' => $driver_comment,
            'passenger_rating_from_driver' => $passenger_rating,
            'passenger_comment_from_driver' => $passenger_comment,

            'otp' => $booking->otp,

            'cancel_reason' => $cancelReason,

            'fine_amount' => $booking->fine_amount,

            'services' => $servicesArray


        ];

        return $bookingArray;
    }

}
