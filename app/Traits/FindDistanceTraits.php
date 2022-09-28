<?php


namespace App\Traits;


use App\Models\DriversCoordinate;
use App\Models\Setting;
use App\Models\VehicleType;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

trait FindDistanceTraits
{
    public function getDistance($pickUpLat, $pickupLng, $dropOffLat, $dropOffLng)
    {

        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $pickUpLat . "," . $pickupLng . "&destination=" . $dropOffLat . "," . $dropOffLng . "&sensor=false&mode=driving&key=" . env('GOOGLE_MAP');

        try {
            $client = new Client;

            $makeRequest = $client->request('GET', $url);

//            $makeRequest =  Http::get($url);

            $response = $makeRequest->getBody();

//            dd($makeRequest->body());

            $responseCode = json_decode($response, true);

            dd($responseCode,$response);

            return [
                'value' => ($responseCode['routes'][0]['legs'][0]['distance']['value'] ?? false),
                'text' => ($responseCode['routes'][0]['legs'][0]['distance']['text'] ?? false),
                'text_time' => ($responseCode['routes'][0]['legs'][0]['duration']['text'] ?? false),
                'value_time' => ($responseCode['routes'][0]['legs'][0]['duration']['value'] ?? false)
            ];
        }
        catch (\Exception $e)
        {
            dd($e->getMessage());
        }
    }


    public function gettingVehicleTypeRecords($distance = null, $peakRate = null,$simpleTime = null,$pickupLat = null,$pickupLng = null)
    {
        $findVehicleFares = VehicleType::get();

        $distanceRange = 10;
        $setting = Setting::first();
        if ($setting) {
            if ($setting->search_range) {
                $distanceRange = $setting->search_range;
            }
        }

        $data = array();
        foreach ($findVehicleFares as $findVehicleFare) {
            if ($findVehicleFare) {
                $peak_factor_applied = 0;
                $estimatedFare = 0;

                $haveClause = $this->distanceFormulaForDriverFind($pickupLat, $pickupLng);

                $available_driver = DriversCoordinate::select('drivers_coordinates.driver_id',
                    'users.first_name', 'users.last_name', 'users.fcm_token', 'users.is_verified',
                    'drivers_coordinates.latitude', 'drivers_coordinates.longitude')
                    ->selectRaw("{$haveClause} AS distance")
                    ->leftJoin('drivers','drivers_coordinates.driver_id', '=', 'drivers.user_id')
                    ->leftJoin('users', 'drivers_coordinates.driver_id', '=', 'users.id')
                    ->where('status', 1)
                    ->where('drivers.vehicle_type_id',$findVehicleFare->id)
                    ->whereRaw("{$haveClause} <= ?", $distanceRange)
                    ->orderBY('distance', 'asc')
                    ->first();

//                dd($available_driver->latitude,$available_driver->longitude,$pickupLat, $pickupLng);

                if($available_driver) {

                    $calculateDriverTimeToReach = $this->getDistance($available_driver->latitude,$available_driver->longitude,$pickupLat, $pickupLng);

                    dd($calculateDriverTimeToReach);

                    $time = explode(' ',$calculateDriverTimeToReach['text_time']);

                    if(isset($time) && isset($time[1]))
                    {
                        if($time[1] == 'hours' || $time[1] == 'hour' )
                        {
                            $driverTime = $time[0] * 60;
                        }
                        elseif($time[1] == 'mins' || $time[1] == 'min')
                        {
                            $driverTime = $time[0];
                        }


                        if(isset($time[2]))
                        {
                            $driverTime = $driverTime + $time[2];
                        }

                    }
                    else{
                        $driverTime = 1;
                    }


                    if ($distance) {
                        $estimatedFare = $findVehicleFare->per_km_rate * $distance;
                        if ($peakRate) {
                            $estimatedFare = $estimatedFare * $peakRate;
                            $peak_factor_applied = 1;
                        }

                    }

                    if ($simpleTime) {

                        $timeFare = $findVehicleFare->per_min_rate * $simpleTime;
                        $estimatedFare = $estimatedFare + $timeFare;

                    }

                    $estimatedFare = ceil($estimatedFare + $findVehicleFare->min_fare);
                    $gst = 0;


                    $data[] = ['min_fare' => (float)$findVehicleFare->min_fare,
                        'per_km_rate' => (float)$findVehicleFare->per_km_rate,
                        'per_min_rate' => (float)$findVehicleFare->per_min_rate,
                        'tax_rate' => (float)$findVehicleFare->tax_rate,
                        'waiting_price_per_min' => (float)$findVehicleFare->waiting_price_per_min,
                        'vehicle_type_id' => $findVehicleFare->id,
                        'total_distance' => (float)$distance,
                        'peak_factor_applied' => $peak_factor_applied,
                        'peak_factor_rate' => $peakRate,
                        'driver_reach_time' => $driverTime,
                        'driver_id' => $available_driver->driver_id,
                        'estimated_fare' => (float)$estimatedFare, 'name' => $findVehicleFare->name];
                }
            }
        }

        if (sizeof($data) > 0) {
            return $data;
        } else {
            return $data;
        }


    }

    public function gettingDistanceInKm($calculatedistance)
    {
        $distance = explode(' ',$calculatedistance['text']);

        $distanceInKm = 0;

        if(isset($distance) && isset($distance[1]))
        {
            if($distance[1] == 'm')
            {
                $distanceInKm = (float)$distance[0]/1000;
            }
            elseif($distance[1] == 'km')
            {
                $distanceInKm = (float)($calculatedistance['value']/1000);
            }
            else{
                $distanceInKm = 1;
            }
        }
        else{
            $distanceInKm = 1;
        }

        return $distanceInKm;
    }


    public function distanceFormulaForDriverFind($lat, $lng)
    {
        $haveClause = '( 6373 * acos( cos( radians(' . $lat . ') )
                                    * cos( radians( drivers_coordinates.latitude ) ) * cos( radians( drivers_coordinates.longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat . ') )
                                    *sin( radians( drivers_coordinates.latitude ) ) ) )';


        return $haveClause;
    }


}
