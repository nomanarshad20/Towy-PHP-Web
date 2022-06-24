<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\FindDistanceRequest;
use App\Services\API\Passenger\RideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RideInitialDistanceController extends Controller
{

    public $rideService;

    public function __construct(RideService $rideService)
    {
        $this->rideService = $rideService;
    }

    public function findDistance(FindDistanceRequest $request)
    {
        try {
            $findingDistance = $this->rideService->getDistance($request->pick_up_latitude, $request->pick_up_longitude,
                $request->drop_off_latitude, $request->drop_off_longitude);



            $distanceInKm = str_replace(',', '', str_replace('km', '', $findingDistance['text']));

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Finding Distance: ' . $e, 500);
        }

        try {

            $gettingVehicleTypeRecords = $this->rideService->gettingVehicleTypeRecords(trim($distanceInKm));

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Calculating Estimated Fare & Getting Vehicle Type Record: ' . $e, 500);
        }

        if(sizeof($gettingVehicleTypeRecords) > 0)
        {
            return makeResponse('success','Distance and Fare Caluclate Successfully',200,$gettingVehicleTypeRecords);
        }
        else{
            return makeResponse('error', 'Record Not Found', 500);

        }


    }
}
