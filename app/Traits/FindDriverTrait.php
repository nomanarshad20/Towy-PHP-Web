<?php


namespace App\Traits;


use App\Models\DriversCoordinate;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

trait FindDriverTrait
{
    public function fetchDrivers($data, $socket_id = 0)
    {

        $distanceRange = 10;
//        $limit = 10;
//        $vehicle_type_id = 1;
//
//        if (isset($data['vehicle_type_id'])) {
//            $vehicle_type_id = $data['vehicle_type_id'];
//        }


        $lat = $data['pick_up_latitude'];
        $lng = $data['pick_up_longitude'];
//        $franchise_id         = $booking['franchise_id'];
        $driversList = array();


        $setting = Setting::first();
        if ($setting) {
            if ($setting->search_range) {
                $distanceRange = $setting->search_range;
            }
        }


        $haveClause = $this->distanceFormula($lat, $lng);


        $available_drivers = DriversCoordinate::select('users.id', 'users.name', 'users.fcm_token',
            'users.is_verified','drivers_coordinates.latitude','drivers_coordinates.longitude')
            ->selectRaw("{$haveClause} AS distance")
            ->leftJoin('users', 'drivers_coordinates.driver_id', '=', 'users.id')
            ->leftJoin('drivers', 'drivers_coordinates.driver_id', '=', 'drivers.user_id')
//            ->where('drivers.franchise_id', $franchise_id)
            ->where('users.is_verified', 1)
            ->where('status', 1)
            ->whereRaw("{$haveClause} <= ?", $distanceRange)
            ->orderBY('distance', 'asc')
//            ->limit($limit)
            ->get();


        $booking_id = null;
        if (isset($data->id) && $data->id != null)
            $booking_id = $data->id;

        if (isset($available_drivers) && $available_drivers != null) {
            foreach ($available_drivers as $public_driver) {

                $driversList[] = array(
                    "id" => $public_driver->id,
                    "name" => $public_driver->name,
                    "distance" => $public_driver->distance,
                    'booking_id' => $booking_id,
                    'fcm_token' => $public_driver->fcm_token,
                    'latitude' => $public_driver->latitude,
                    'longitude' => $public_driver->longitude
                );
            }

            return $driversList;

            /*
            if (sizeof($driversList) > 0) {
                //$response = ['result' => 'success','data' => $driversList,'message' => 'Available Drivers List'];
            }
            */
        }

        return false;

    }

    public function distanceFormula($lat, $lng)
    {
        $haveClause = '( 6373 * acos( cos( radians(' . $lat . ') )
                                    * cos( radians( drivers_coordinates.latitude ) ) * cos( radians( drivers_coordinates.longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') )
                                    *sin( radians( drivers_coordinates.latitude ) ) ) )';


        return $haveClause;
    }
}
