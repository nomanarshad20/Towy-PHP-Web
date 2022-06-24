<?php


namespace App\Services\Admin;


use App\Models\Setting;

class SettingService
{
    public function index()
    {
        $data =  Setting::first();
        return view('admin.setting.create',compact('data'));
    }

    public function save($request)
    {
        $settingArray = $request->all();

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
