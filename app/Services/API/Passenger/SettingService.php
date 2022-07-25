<?php


namespace App\Services\API\Passenger;


use App\Models\Setting;

class SettingService
{
    public function index()
    {
        $data = Setting::first();

        if($data)
        {
            $help = array();
            if(isset($data->help))
            {
                if($data->help)
                {
                    $help = ['help'=>$data->help];
                }
            }

            if(sizeof($help) > 0)
            {
                return makeResponse('success','Record Found',200,$help);
            }
            else{
                return makeResponse('error','Record Not Found',404);
            }
        }
        else{
            return makeResponse('error','Record Not Found',500);
        }
    }
}
