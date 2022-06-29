<?php


namespace App\Helper;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Image;

class ImageUploadHelper
{
    public static function saveImage($image, $fileNameUpload, $path,$drive)
    {

        Image::make($image)->save($path . $fileNameUpload);

        return $drive . $fileNameUpload;
    }

    public static function uploadImage($uploadImage, $path)
    {
        $image = $uploadImage;
        $ext = $image->getClientOriginalExtension();
        $randomString = mt_rand(1000, 9999);
        $fileName = $image->getClientOriginalName();
        $fileNameUpload = time() . "-".$randomString.'.' . $ext;
        $drive = $path;
        $path = public_path($drive);
        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true);
        }

        $imageSave = ImageUploadHelper::saveImage($image, $fileNameUpload, $path,$drive);

        return $imageSave;
    }


}
