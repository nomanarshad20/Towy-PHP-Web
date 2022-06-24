<?php


namespace App\Services\API\Driver;


use App\Models\DriverCoordinate;
use App\Models\DriversCoordinate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverLocationService
{
    public function saveLocation($request)
    {
        DB::beginTransaction();
        try{
            if(!Auth::user()->driverCoordinate)
            {
                $coordinate = new DriversCoordinate;
            }
            else{
                $coordinate =  Auth::user()->driverCoordinate;
            }

            $coordinate->driver_id = Auth::user()->id;
            $coordinate->latitude = $request->latitude;
            $coordinate->longitude =  $request->longitude;
            $coordinate->bearing = $request->bearing;
            $coordinate->city = $request->city;
            $coordinate->area_name = $request->area_name;

            $coordinate->save();

            DB::commit();

            $data = [
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'bearing' => $request->bearing,
                'city' => $request->city,
                'area_name' => $request->area_name
            ];

            return makeResponse('success','Driver Coordinate Update Successfully',200,$data);

        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return makeResponse('error','Error in Saving Driver Coordindate: '.$e,500);
        }
    }
}
