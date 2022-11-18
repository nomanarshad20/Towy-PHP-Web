<?php

namespace App\Traits;

use App\Models\DriversCoordinate;
use App\Models\DriverService;
use App\Models\Setting;

trait FindServiceDriverTrait
{
    public function fetchDrivers($data, $services)
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


        $available_drivers = DriversCoordinate::select('drivers_coordinates.driver_id',
            'users.first_name', 'users.last_name', 'users.fcm_token', 'users.is_verified',
            'drivers_coordinates.latitude', 'drivers_coordinates.longitude')
            ->selectRaw("{$haveClause} AS distance")
            ->leftJoin('users', 'drivers_coordinates.driver_id', '=', 'users.id')
//            ->leftJoin('drivers', 'drivers_coordinates.driver_id', '=', 'drivers.user_id')
//            ->where('drivers.franchise_id', $franchise_id)
//            ->where('users.is_verified', 1)
            ->where('status', 1)
            ->where('users.user_type', 4)
            ->whereRaw("{$haveClause} <= ?  ", $distanceRange)
//            ->where('driver_id',48)
            ->orderBY('distance', 'asc')
//            ->limit($limit)
            ->get();


        $booking_id = null;
        if (isset($data->id) && $data->id != null) {
            $booking_id = $data->id;
        }

        $servicesArray = [];
        foreach($services as $service)
        {
            $servicesArray = [$service['id']];
        }


        if (isset($available_drivers) && $available_drivers != null) {
            foreach ($available_drivers as $public_driver) {

                $getDriverService = DriverService::where('user_id', $public_driver->driver_id)
//                    ->get();
                    ->pluck('service_id')->toArray();

                foreach($getDriverService as $key => $singleService)
                {
                    if(in_array($singleService,$servicesArray))
                    {
                        break;
                    }
                    else{
                        if($key+1 >= sizeof($getDriverService) )
                        {
                            break 2;
                        }

                    }
                }



                $driversList[] = array(
                    "id" => $public_driver->driver_id,
                    "first_name" => $public_driver->first_name,
                    "last_name" => $public_driver->last_name,
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

    public function findDistanceFormula($currentLat, $currentLng, $previousLat, $previousLng)
    {
        $theta = $previousLng - $currentLng;
        $dist = sin(deg2rad($previousLat)) * sin(deg2rad($currentLat)) + cos(deg2rad($previousLat)) * cos(deg2rad($currentLat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344) * 1000; //meter return
    }
}
