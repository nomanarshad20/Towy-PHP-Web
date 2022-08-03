<?php


namespace App\Services\API\Driver;


use App\Helper\ImageUploadHelper;
use App\Models\Driver;
use App\Models\DriversCoordinate;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use const http\Client\Curl\AUTH_ANY;

class DriverInformationService
{
    public function save($request, $user = null)
    {
        DB::beginTransaction();

        try {
            $userID = null;
            if ($user) {
                $userID = $user->id;
                Auth::loginUsingId($userID);
            } else {
                $userID = Auth::user()->id;
            }


            if ($request->referrer) {
                $checkForReferrer = User::where('referral_code', $request->referrer)
                    ->where('id', "!=", $userID)
                    ->first();

                if (!$checkForReferrer) {
                    $response = ['result' => 'error', 'message' => 'Referrer Code is invalid', 'code' => 422];

                    return $response;
                }

                Auth::user()->referrer = $request->referrer;

            }


//            $checkForEMail = User::where('id', '!=', $userID)
//                ->where('email', $request->email)->first();

//            if ($checkForEMail) {
//                $response = ['result' => 'error', 'message' => 'Email is already taken', 'code' => 422];
//
//                return $response;
//            }

            Auth::user()->first_name = $request->first_name;
            Auth::user()->last_name = $request->last_name;
//            Auth::user()->email = $request->email;
            Auth::user()->password = bcrypt($request->password);
            Auth::user()->steps = 1;

            Auth::user()->save();
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error In Saving User Information: ' . $e, 'code' => 500];
            return $response;
        }

        try {
            $driver = Driver::create(['user_id' => Auth::user()->id,
                'city' => $request->city]);
            DB::commit();


            $response = ['result' => 'success', 'message' => 'Information Save Successfully'];

            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error In Saving Driver Information: ' . $e, 'code' => 500];
            return $response;
        }
    }

    public function drivers_license($drivers_license)
    {
        if ($drivers_license) {
            $image = ImageUploadHelper::uploadImage($drivers_license, 'upload/driver/' . Auth::user()->id . '/');


            Auth::user()->driver->drivers_license = $image;

//            if (Auth::user()->driver->drivers_license && Auth::user()->driver->vehicle_insurance
//                && Auth::user()->driver->vehicle_inspection
//                && isset(Auth::user()->driver->vehicle) && Auth::user()->driver->vehicle->registration_book
//                && Auth::user()->image) {
//                Auth::user()->steps = 4;
//            }


            Auth::user()->driver->save();
//            Auth::user()->save();


        }


    }

    public function vehicle_insurance($vehicle_insurance)
    {
        if ($vehicle_insurance) {
            $image = ImageUploadHelper::uploadImage($vehicle_insurance, 'upload/driver/' . Auth::user()->id . '/');

            Auth::user()->driver->vehicle_insurance = $image;

//            if (Auth::user()->driver->drivers_license && Auth::user()->driver->vehicle_insurance
//                && Auth::user()->driver->vehicle_inspection
//                && isset(Auth::user()->driver->vehicle) && Auth::user()->driver->vehicle->registration_book
//                && Auth::user()->image) {
//                Auth::user()->steps = 4;
//            }
            Auth::user()->driver->save();
//            Auth::user()->save();
        }
    }

    public function vehicle_inspection($vehicle_inspection)
    {
        if ($vehicle_inspection) {
            $image = ImageUploadHelper::uploadImage($vehicle_inspection, 'upload/driver/' . Auth::user()->id . '/');

            Auth::user()->driver->vehicle_inspection = $image;

//            if (Auth::user()->driver->drivers_license && Auth::user()->driver->vehicle_insurance
//                && Auth::user()->driver->vehicle_inspection
//                && isset(Auth::user()->driver->vehicle) && Auth::user()->driver->vehicle->registration_book
//                && Auth::user()->image) {
//                Auth::user()->steps = 4;
//            }
            Auth::user()->driver->save();
//            Auth::user()->save();
        }
    }

//    public function saveLicenseBackSide($license_back_side)
//    {
//        if ($license_back_side) {
//            $image = ImageUploadHelper::uploadImage($license_back_side, 'upload/driver/' . Auth::user()->id . '/');
//
//            Auth::user()->driver->license_back_side = $image;
//
//            if (Auth::user()->driver->cnic_front_side && Auth::user()->driver->cnic_back_side
//                && Auth::user()->driver->license_front_side && Auth::user()->driver->license_back_side
//                && isset(Auth::user()->driver->vehicle) && Auth::user()->driver->vehicle->registration_book
//                && Auth::user()->image) {
//                Auth::user()->steps = 4;
//            }
//            Auth::user()->driver->save();
//
//            Auth::user()->save();
//        }
//    }

    public function savePhoto($image)
    {
        if ($image) {
            $image = ImageUploadHelper::uploadImage($image, 'upload/driver/' . Auth::user()->id . '/');

            Auth::user()->image = $image;

//            if (Auth::user()->driver->drivers_license && Auth::user()->driver->vehicle_insurance
//                && Auth::user()->driver->vehicle_inspection
//                && isset(Auth::user()->driver->vehicle) && Auth::user()->driver->vehicle->registration_book
//                && Auth::user()->image) {
//                Auth::user()->steps = 4;
//            }
//
            Auth::user()->save();
        }
    }

    public function saveVehicleInformation($request)
    {
        try {

            $vehicle = Vehicle::create(['name' => $request->name, 'model' => $request->model,
                'registration_number' => $request->registration_number,
                'vehicle_type_id' => 1,
                'model_year' => $request->model_year]);

            if ($request->has('registration_book')) {
                $image = ImageUploadHelper::uploadImage($request->registration_book, 'upload/vehicle/' . $vehicle->id . '/');
                $vehicle->registration_book = $image;

                $vehicle->save();
            }

            Auth::user()->driver->vehicle_id = $vehicle->id;

            Auth::user()->driver->save();


//            if (Auth::user()->driver->drivers_license && Auth::user()->driver->vehicle_insurance
//                && Auth::user()->driver->vehicle_inspection
//                && isset(Auth::user()->driver->vehicle) && Auth::user()->driver->vehicle->registration_book
//                && Auth::user()->image) {
//                Auth::user()->steps = 4;
//            }

//            Auth::user()->save();

            $response = ['result' => 'success', 'message' => 'Vehicle Information Save Successfully.'];

            return $response;


        } catch (\Exception $e) {
            $response = ['result' => 'error', 'message' => 'Error in Saving Vehicle: ' . $e];

            return $response;
        }
    }

    public function documentComplete()
    {
        try {

            if (Auth::user()->driver->drivers_license && Auth::user()->driver->vehicle_insurance
                && Auth::user()->driver->vehicle_inspection
                && isset(Auth::user()->driver->vehicle) && Auth::user()->driver->vehicle->registration_book
                && Auth::user()->image) {
                Auth::user()->steps = 4;
            }
            Auth::user()->save();


            DriversCoordinate::create(['status' => 0, 'driver_id' => Auth::user()->id]);

            $response = ['result' => 'success', 'message' => 'All Documents are saved successfully.', 'code' => 200];

            return $response;

        } catch (\Exception $e) {
            $response = ['result' => 'error', 'message' => 'Error in Saving Step: ' . $e, 'code' => 500];

            return $response;
        }
    }

    public function getVehicleType()
    {
        $data = VehicleType::select('id', 'name', 'image','description')->where('status', 1)->get();
//        $vehicleTypeArray =  array();
//        foreach($data as $vehicleType)
//        {
//            $vehicleTypeArray[] = ['id'=>$vehicleType->id,'name'=>$vehicleType->name];
//        }

        if (sizeof($data) > 0) {
            $response = ['result' => 'success', 'message' => 'Vehicle Type Found Successfully', 'code' => 200, 'data' => $data];
        } else {
            $response = ['result' => 'error', 'message' => 'Vehicle Type Not Found', 'code' => 404, 'data' => null];
        }
        return $response;
    }

    public function saveVehicleType($request)
    {
        DB::beginTransaction();
        try {
            Auth::user()->driver->vehicle_type_id = $request->vehicle_type_id;
            Auth::user()->driver->save();
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error in Saving Vehicle Type Record: ' . $e, 'code' => 500];
            return $response;
        }

        try {
            Auth::user()->steps = 3;
            Auth::user()->save();
            DB::commit();
            $response = ['result' => 'success', 'message' => 'Vehicle Type Save Successfully', 'code' => 200];
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error in Saving Vehicle Type Record: ' . $e, 'code' => 500];
            return $response;
        }
    }

    public function saveSocialSecurityNumber($request)
    {
        DB::beginTransaction();
        try {
            Auth::user()->driver->ssn = $request->ssn;
            Auth::user()->driver->save();

            DB::commit();
            $response = ['result' => 'success', 'message' => 'Social Security Number Save Successfully', 'code' => 200];

        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error in Saving Vehicle Type Record: ' . $e, 'code' => 500];

        }
        return $response;

    }


}
