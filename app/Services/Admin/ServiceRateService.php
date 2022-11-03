<?php


namespace App\Services\Admin;


use App\Models\ServiceRate;

class ServiceRateService
{
    public function index()
    {
        $data = ServiceRate::first();
        return view('admin.service_rate.create',compact('data'));
    }

    public function save($request)
    {
        try {
            if ($request->id) {
                $data = ServiceRate::find($request->id);
            } else {
                $data = new ServiceRate;
            }

            $data->initial_distance_rate = $request->initial_distance_rate;
            $data->initial_time_rate = $request->initial_time_rate;
            $data->service_time_rate = $request->service_time_rate;

            $data->save();

            return response()->json(['result' => 'success', 'message' => 'Record Save Successfully']);
        }
        catch (\Exception $e)
        {
            return response()->json(['result' => 'error', 'message' => 'Error in Saving Record: '.$e]);
        }
    }
}

