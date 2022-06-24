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

            //DB::beginTransaction();
            /*$data = [
                'vehicle_type_id'   => $request->vehicle_type_id,
                'pick_up_latitude'  => $request->latitude,
                'pick_up_longitude' => $request->longitude
            ];*/

            $gettingDrivers             = $this->fetchDrivers($request);


            if (sizeof($gettingDrivers) > 0) {
                $response = ['result' => 'success', 'data' => $gettingDrivers, 'message' => 'Nearest Drivers Find Successfully'];
            } else {
                $response = ['result' => 'error', 'data' => $request->all(), 'message' => 'Driver Not Found. Try Again Later', 'code' => 404];
            }

            //return $response;
            return makeResponse($response['result'], $response['message'], $response['code'], $response['data']);

        } catch (\Exception $e) {
            //            DB::rollBack();
            $response = ['result' => 'error', 'data' => $data, 'message' => 'Error in find nearest drivers : ' . $e, 'code' => 500];
            return $response;
            return makeResponse('error', $response['message'], $response['code'], $response['data']);
        }
    }
}
