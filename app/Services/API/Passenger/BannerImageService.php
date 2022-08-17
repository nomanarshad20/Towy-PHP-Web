<?php


namespace App\Services\API\Passenger;


use App\Models\BannerImage;
use App\Models\VehicleType;

class BannerImageService
{
    public function index()
    {
        $bannerImages = BannerImage::select('image')->where('status',1)->get();

        $vehicleTypes = VehicleType::select('name','image')->where('status',1)->get();

        $data =  [
            'banner_image' => $bannerImages,
            'vehicle_types' => $vehicleTypes
        ];


        return makeResponse('success','Record Fetch Successfully',200,$data);

    }
}
