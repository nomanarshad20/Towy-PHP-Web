<?php


namespace App\Services\Admin;


use App\Helper\ImageUploadHelper;
use App\Models\BannerImage;

class BannerImageService
{
    public function index()
    {
        $data = BannerImage::all();

        return view('admin.banner_image.listing',compact('data'));
    }

    public function create()
    {
        return view('admin.banner_image.create');
    }

    public function save($request)
    {
        try{
            $image = ImageUploadHelper::uploadImage($request->image,'upload/banner-image/');
            BannerImage::create(['image'=>$image,'status'=>1]);

            return makeResponse('success','Image Upload Successfully',200);
        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Image Upload: '.$e,200);
        }

    }

    public function delete($request)
    {
        $data = BannerImage::find($request->id);

        if($data)
        {
            $data->delete();
            return makeResponse('success','Image Deleted Successfully',200);
        }
        else{
            return makeResponse('error','Record Not Found',200);
        }
    }

    public function changeStatus($request)
    {
        $data = BannerImage::find($request->id);

        if($data)
        {
            if($data->status == 1)
            {
                $data->status = 0;
            }
            else{
                $data->status = 1;
            }

            $data->save();
            return makeResponse('success','Status Change Successfully',200);
        }
        else{
            return makeResponse('error','Record Not Found',200);
        }
    }

}
