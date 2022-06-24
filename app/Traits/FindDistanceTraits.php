<?php


namespace App\Traits;


use App\Models\VehicleType;
use GuzzleHttp\Client;

trait FindDistanceTraits
{
    public function getDistance($pickUpLat, $pickupLng, $dropOffLat, $dropOffLng)
    {

        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $pickUpLat . "," . $pickupLng . "&destination=" . $dropOffLat . "," . $dropOffLng . "&sensor=false&mode=driving&key=" . env('GOOGLE_MAP');

        $client =  new Client;

        $makeRequest = $client->request('GET', $url);

        $response = $makeRequest->getBody();
        $responseCode = json_decode($response,true);



        return [
            'value' => ($responseCode['routes'][0]['legs'][0]['distance']['value'] ?? false),
            'text' => ($responseCode['routes'][0]['legs'][0]['distance']['text'] ?? false),
            'text_time' => ($responseCode['routes'][0]['legs'][0]['duration']['text'] ?? false),
            'value_time' => ($responseCode['routes'][0]['legs'][0]['duration']['value'] ?? false)
        ];
    }


    public function gettingVehicleTypeRecords($distance = null)
    {

        $findVehicleFares = VehicleType::get();

        $data = array();

        foreach($findVehicleFares as $findVehicleFare)
        {
            if ($findVehicleFare) {

                $estimatedFare = 0;
                if ($distance) {
                    $estimatedFare = $findVehicleFare->per_km_rate * $distance;
                }


                $data[] = ['min_fare' => (float)$findVehicleFare->min_fare, 'per_km_rate' => (float)$findVehicleFare->per_km_rate,
                    'per_min_rate' => (float)$findVehicleFare->per_min_rate, 'tax_rate' => (float)$findVehicleFare->tax_rate,
                    'waiting_price_per_min' => (float)$findVehicleFare->waiting_price_per_min,
                    'vehicle_type_id' => $findVehicleFare->id,
                    'total_distance' => (float)$distance,
                    'estimated_fare' => (float)$estimatedFare,'name'=>$findVehicleFare->name];

            }
        }

        if(sizeof($data) > 0)
        {
            return $data;
        }
        else{
            return false;
        }



    }


}
