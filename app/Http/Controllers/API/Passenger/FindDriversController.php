<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\FindDriverTrait;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FindDriversController extends Controller
{
    use FindDriverTrait;

    public function findDriversByVehicle(Request $request)
    {
        try {

            $gettingDrivers = $this->fetchDrivers($request);

            if (sizeof($gettingDrivers) > 0) {
                $response = ['result' => 'success', 'data' => $gettingDrivers, 'message' => 'Nearest Drivers Find Successfully','code' => 200];
            } else {
                $response = ['result' => 'error', 'data' => null, 'message' => null, 'code' => 404];
                // hide message as per mobile developer request.
                // $response = ['result' => 'error', 'data' => null, 'message' => 'Driver Not Found. Try Again Later', 'code' => 404];
            }
            return makeResponse($response['result'], $response['message'], $response['code'], $response['data']);

        } catch (\Exception $e) {
            $response = ['result' => 'error', 'message' => 'Error in find nearest drivers : ' . $e];
            return makeResponse($response['result'], $response['message'], 500);
        }
    }
}
