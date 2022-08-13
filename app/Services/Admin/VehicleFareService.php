<?php


namespace App\Services\Admin;


use App\Models\VehicleFareSetting;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class VehicleFareService
{
    public function create()
    {
//        $data = VehicleFareSetting::first();
        $data =  VehicleType::first();
        return view('admin.vehicle_fare.create',compact('data'));
    }

    public function save($request)
    {

        if($request->id)
        {
//            $data = VehicleFareSetting::find($request->id);
            $data = VehicleType::find($request->id);

        }
        else{
//            $data =  new VehicleFareSetting;
            $data =  new VehicleType;

        }

        $data->min_fare = $request->min_fare;
        $data->per_km_rate =  $request->per_km_rate;
        $data->per_min_rate = $request->per_min_rate;
        $data->waiting_price_per_min =  $request->waiting_price_per_min;
        $data->tax_rate =  $request->tax_rate;
        $data->name =  $request->name;
        $data->created_by =  Auth::user()->id;
        $data->initial_distance_rate =  $request->initial_distance_rate;
        $data->initial_time_rate = $request->initial_time_rate;
//        $data->created_at = Carbon::now()->format('Y-m-d H:i:s');
//        $data->updated_at = Carbon::now()->format('Y-m-d H:i:s');

        try{
            $data->save();

            return makeResponse('success','Record Save Successfully',200);
        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Saving Record: '.$e,200);
        }




    }
}
