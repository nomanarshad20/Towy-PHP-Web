<?php


namespace App\Traits;


use App\Models\VehicleType;
use GuzzleHttp\Client;

trait FindDistanceTraits
{
    public function getDistance($pickUpLat, $pickupLng, $dropOffLat, $dropOffLng)
    {

        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $pickUpLat . "," . $pickupLng . "&destination=" . $dropOffLat . "," . $dropOffLng . "&sensor=false&mode=driving&key=" . env('GOOGLE_MAP');

        try {
            $client = new Client;

            $makeRequest = $client->request('GET', $url);

            $response = $makeRequest->getBody();

            $responseCode = json_decode($response, true);

            return [
                'value' => ($responseCode['routes'][0]['legs'][0]['distance']['value'] ?? false),
                'text' => ($responseCode['routes'][0]['legs'][0]['distance']['text'] ?? false),
                'text_time' => ($responseCode['routes'][0]['legs'][0]['duration']['text'] ?? false),
                'value_time' => ($responseCode['routes'][0]['legs'][0]['duration']['value'] ?? false)
            ];
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }


    public function gettingVehicleTypeRecords($distance = null, $peakRate = null,$simpleTime = null)
    {

        $findVehicleFares = VehicleType::get();

        $data = array();
        foreach ($findVehicleFares as $findVehicleFare) {
            if ($findVehicleFare) {
                $peak_factor_applied = 0;
                $estimatedFare = 0;

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

                $estimatedFare = ceil ($estimatedFare + $findVehicleFare->min_fare);
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
                    'estimated_fare' => (float)$estimatedFare, 'name' => $findVehicleFare->name];

            }
        }

        if (sizeof($data) > 0) {
            return $data;
        } else {
            return false;
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


}
