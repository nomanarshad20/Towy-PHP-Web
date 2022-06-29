<?php

namespace App\Traits;

use Carbon\Carbon;

trait CalculateCancelPercentageAmountTrait
{
    /**
     * Create regular or static methods here
     */

    public function calculatePercentage($booking)
    {

        if ($booking->booking_type == 'book_later') {
            $rideDate = Carbon::createFromTimestamp($booking->pick_up_date . $booking->pick_up_time)->format('Y-m-d H:i:s');
        }
        else {
            $rideDate = Carbon::parse($booking->created_at)->format('Y-m-d H:i:s');
        }

        $fine = 0;
        $allowed_time = 0;

        if($booking->bookingDetail->cancel_ride_time)
        {
            $allowed_time = $booking->bookingDetail->cancel_ride_time;
        }



        if (Carbon::now() > Carbon::parse($rideDate)->subMinutes($allowed_time)) {

            $percentageAmount = 0;
            if($booking->bookingDetail->cancel_ride_driver_fine_amount)
            {
                $percentageAmount = $booking->bookingDetail->cancel_ride_driver_fine_amount;
            }


            if ($booking->estimated_fare && $booking->estimated_fare != 0) {
                $fine = ($booking->estimated_fare / 100) * $percentageAmount;
            }
        }


        return $fine;
    }
}
