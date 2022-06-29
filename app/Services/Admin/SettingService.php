<?php


namespace App\Services\Admin;


use App\Models\Setting;
use Carbon\Carbon;

class SettingService
{
    public function index()
    {
        $data =  Setting::first();
        return view('admin.setting.create',compact('data'));
    }

    public function save($request)
    {
        $settingArray = [
            'search_range' => $request->search_range,
            'cancel_ride_time' =>$request->cancel_ride_time,
            'passenger_cancel_fine_amount' => $request->passenger_cancel_fine_amount,
            'driver_cancel_fine_amount' => $request->driver_cancel_fine_amount,
            'allowed_waiting_time' =>  $request->allowed_waiting_time
        ];

        if($request->id)
        {
            $data =  Setting::find($request->id);

            if(!$data)
            {
                return makeResponse('error','No Record Found',404);
            }

            $data->update($settingArray);

        }
        else{
            $data =  Setting::create($settingArray);
        }

        return makeResponse('success','Record Inserted Successfully',200);
    }
}
