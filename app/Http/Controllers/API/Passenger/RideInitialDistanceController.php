<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\FindDistanceRequest;
use App\Models\PeakFactor;
use App\Services\API\Passenger\RideService;
use App\Services\API\PeakService;
use App\Traits\FindDistanceTraits;
use App\Traits\FindDriverTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RideInitialDistanceController extends Controller
{

    use FindDriverTrait;

    public $rideService, $peakService;

    public function __construct(RideService $rideService, PeakService $peakService)
    {
        $this->rideService = $rideService;
        $this->peakService = $peakService;
    }

    public function findDistance(FindDistanceRequest $request)
    {
        //find distance between two point
        try {
            $findingDistance = $this->rideService->getDistance($request->pick_up_latitude, $request->pick_up_longitude,
                $request->drop_off_latitude, $request->drop_off_longitude);



            $time = explode(' ',$findingDistance['text_time']);



            if(isset($time) && isset($time[1]))
            {
                if($time[1] == 'hours' || $time[1] == 'hour' )
                {
                    $simpleTime = $time[0] * 60;
                }
                elseif($time[1] == 'mins' || $time[1] == 'min')
                {
                    $simpleTime = $time[0];
                }


                if(isset($time[2]))
                {
                    $simpleTime = $simpleTime + $time[2];
                }

            }
            else{
                $simpleTime = 1;
            }


            $distance = explode(' ',$findingDistance['text']);


            if(isset($distance) && isset($distance[1]))
            {
                if($distance[1] == 'm')
                {
                    $distanceInKm = (float)$distance[0]/1000;
                }
                elseif($distance[1] == 'km')
                {
                    $distanceInKm = (float)($findingDistance['value']/1000);
                }
                else{
                    $distanceInKm = 1;
                }
            }
            else{
                $distanceInKm = 1;
            }



        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Finding Distance: ' . $e, 500);
        }

        //check for peak rate
        try {
            $totalBooking = $this->peakService->calculateTotalBooking($request->pick_up_latitude, $request->pick_up_longitude);
            $data = ['pick_up_latitude' => $request->pick_up_latitude, 'pick_up_longitude' => $request->pick_up_longitude];

            $findDriver = $this->fetchDrivers($data);
            $totalDriver = count($findDriver);
            $peakRate = null;


            if($totalBooking > $totalDriver && $totalDriver > 0)
            {
                $peakFactor = PeakFactor::where('start_point','<=',$totalBooking)->where('end_point','>=',$totalBooking)->first();

                $peakRate = $peakFactor->factor_rate;
            }

        }
        catch (\Exception $e) {
            return makeResponse('error', 'Error in Checking Peak Rate: ' . $e, 500);
        }


        try {

            $gettingVehicleTypeRecords = $this->rideService->gettingVehicleTypeRecords(trim($distanceInKm),$peakRate,trim($simpleTime),$request->pick_up_latitude, $request->pick_up_longitude);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Calculating Estimated Fare & Getting Vehicle Type Record: ' . $e, 500);
        }

        if (sizeof($gettingVehicleTypeRecords) > 0) {
            return makeResponse('success', 'Distance and Fare Calculate Successfully', 200, $gettingVehicleTypeRecords);
        } else {
            return makeResponse('error', 'Record Not Found', 500);

        }


    }
}
