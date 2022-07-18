<?php


namespace App\Services\API\Driver;


use App\Helper\ImageUploadHelper;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    public function saveProfile($request)
    {
        try {
            if ($request->has('image')) {
                $image = ImageUploadHelper::uploadImage($request->image, 'upload/driver/' . Auth::user()->id . '/');
                Auth::user()->image = $image;
            }

            Auth::user()->name = $request->name;
            Auth::user()->email = $request->email;
            Auth::user()->driver->city = $request->city;
            if ($request->password) {
                Auth::user()->password = bcrypt($request->password);
            }

            Auth::user()->driver->save();
            Auth::user()->save();

//            $data = [
//                'image' => Auth::user()->image,
//                'name' => Auth::user()->name,
//                'email' => Auth::user()->email,
//                'city' => Auth::user()->driver->city
//            ];

            $response = ['result'=>'success','message'=>'Profile Update Successfully','code'=>200];
            return $response;
//            return makeResponse('success', 'Profile Updated Successfully', 200,$data);
        } catch (\Exception $e) {
            $response = ['result'=>'error','message'=>'Error in Updating Profile: ' . $e,'code'=>500];
            return $response;
//            return makeResponse('error', 'Error in Updating Profile: ' . $e, 500);
        }
    }
}
