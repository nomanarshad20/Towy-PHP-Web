<?php


namespace App\Services\API\Passenger;


use App\Models\BannerImage;

class BannerImageService
{
    public function index()
    {
        $data = BannerImage::select('image')->where('status',1)->get();

        if(sizeof($data) > 0)
        {
            return makeResponse('success','Banner Image Created Successfully',200,$data);
        }
        else{
            return makeResponse('error','Record Not Found',500);
        }
    }
}
