<?php


namespace App\Services\API;


use App\Models\Booking;
use App\Models\DriversCoordinate;
use App\Models\PeakFactor;
use App\Models\Setting;
use Carbon\Carbon;

class PeakService
{
    public function calculateTotalBooking($pick_up_lat, $pick_up_lng)
    {
        $distanceRange = 10;
        $setting = Setting::first();
        if ($setting) {
            if ($setting->search_range) {
                $distanceRange = $setting->search_range;
            }
        }

        $timeInterval = 5;

        $lat = $pick_up_lat;
        $lng = $pick_up_lng;

        $haveClause = $this->distanceFormula($lat, $lng);


        $totalBookings = Booking::select('bookings.id','created_at')
            ->selectRaw("{$haveClause} AS distance")
//            ->where('users.is_verified', 1)
//            ->where('status', 1)
            ->whereRaw("{$haveClause} <= ?", $distanceRange)
            ->where('booking_type','book_now')
            ->whereBetween('created_at',[Carbon::now()->subMinutes($timeInterval),Carbon::now()])
            ->orderBY('distance', 'asc')
            ->where('ride_status','!=',2)
            ->distinct('passenger_id')
            ->count();

//        dd($totalBookings);

        return $totalBookings;

    }

    public function distanceFormula($lat, $lng)
    {
        $haveClause = '( 6373 * acos( cos( radians(' . $lat . ') )
                                    * cos( radians( bookings.pick_up_latitude ) ) * cos( radians( bookings.pick_up_longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') )
                                    *sin( radians( bookings.pick_up_latitude ) ) ) )';


        return $haveClause;
    }
}
