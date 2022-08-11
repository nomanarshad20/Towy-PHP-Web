<?php


namespace App\Services\API\Driver;


use App\Helper\ImageUploadHelper;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function saveProfile($request)
    {
        try {
            if ($request->has('image')) {
                $image = ImageUploadHelper::uploadImage($request->image, 'upload/driver/' . Auth::user()->id . '/');
                Auth::user()->image = $image;
            }

            Auth::user()->first_name = $request->first_name;
            Auth::user()->last_name = $request->last_name;
            Auth::user()->email = $request->email;

            if ($request->password) {
                if(Hash::check($request->old_password,Auth::user()->password))
                {
                    Auth::user()->password = bcrypt($request->password);
                }
                else{
                    $response = ['result'=>'error','message'=>'Old Password is Incorrect','code'=>500];
                    return $response;
                }

            }

            if($request->city)
            {
                Auth::user()->driver->city = $request->city;
                Auth::user()->driver->save();
            }

            Auth::user()->save();


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
