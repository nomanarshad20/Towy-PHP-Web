<?php


namespace App\Services\Admin;


use App\Helper\ImageUploadHelper;
use App\Models\Driver;
use App\Models\Franchise;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Support\Facades\DB;

class DriverService
{


    public function index()
    {
        $data = User::where('user_type', 2)->get();
        return view('admin.driver.listing', compact('data'));
    }

    public function create()
    {
        $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
//        $vehicleTypes =  VehicleType::where('status',1)->get();

        return view('admin.driver.create', compact('franchises'));
    }

    public function save($request)
    {
        DB::beginTransaction();

        try {
            $user = User::create(['name' => $request->name, 'email' => $request->email,
                'password' => bcrypt($request->password),
                'mobile_no' => $request->mobile_no, 'user_type' => 2,


            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating User: ' . $e, 500);
        }


        try {
            $vehicle = Vehicle::create(['name' => $request->vehicle_name,
                'model' => $request->model,'vehicle_type_id'=>1,
                'model_year' => $request->model_year,
                'registration_number' => $request->registration_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Vehicle: ' . $e, 500);
        }

        try {

            $driver = Driver::create(['city' => $request->city,
                'franchise_id' => $request->franchise_id,
                'vehicle_type_id'=>1,
                'vehicle_id' => $vehicle->id, 'user_id' => $user->id]);


            $user->referral_code = "partner-00" . $user->id;

            $user->save();

//            $vehicleTypes =  VehicleType::where('status',1)->get();

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Driver: ' . $e, 500);
        }


        DB::commit();
        return makeResponse('success', 'Driver Information Save Successfully', 200);
    }

    public function edit($id)
    {
        $data = User::find($id);

        if ($data) {
            $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
//            $vehicleTypes =  VehicleType::where('status',1)->get();


            return view('admin.driver.edit', compact('data', 'franchises'));
        } else {
            return redirect()->route('driverListing')->with('error', 'Record Not Found');
        }

    }

    public function update($request)
    {
        DB::beginTransaction();

        $checkForEmail = User::where('email', $request->email)->where('id', '!=', $request->id)
            ->first();

        if ($checkForEmail) {
            return makeResponse('error', 'Email Already Exist', 500);
        }

        $checkForMobilePhone = User::where('mobile_no', $request->mobile_no)->where('id', '!=', $request->id)
            ->first();

        if ($checkForMobilePhone) {
            return makeResponse('error', 'Mobile No Already Exist', 500);
        }

        $data = User::find($request->id);

        if ($data && $data->user_type == 2) {
            try {
                $data->update(['name' => $request->name, 'email' => $request->email,
                    'mobile_no' => $request->mobile_no, 'user_type' => 2,
                ]);

                if ($request->password) {
                    $data->update(['password' => bcrypt($request->password)]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating User: ' . $e, 500);
            }


            try {
                $data->driver->vehicle->update(['name' => $request->vehicle_name,
                    'model' => $request->model,'vehicle_type_id'=>$request->vehicle_type_id,
                    'model_year' => $request->model_year,
                    'registration_number' => $request->registration_number]);

            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating Vehicle: ' . $e, 500);
            }

            try {

                $driver = $data->driver->update(['city' => $request->city,
                    'franchise_id' => $request->franchise_id,
                    'vehicle_type_id'=>$request->vehicle_type_id,
                    'vehicle_id' => $data->driver->vehicle->id, 'user_id' => $request->id]);

                $data->save();

            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating Driver: ' . $e, 500);
            }

            try {
                if ($request->has('cnic_front_image')) {
                    $image = ImageUploadHelper::uploadImage($request->cnic_front_image, 'upload/driver/' . $request->id . '/');
                    $data->driver->cnic_front_side = $image;

                }

                if ($request->has('cnic_back_image')) {
                    $image = ImageUploadHelper::uploadImage($request->cnic_back_image, 'upload/driver/' . $request->id . '/');
                    $data->driver->cnic_back_side = $image;
                }

                if ($request->has('license_front_image')) {
                    $image = ImageUploadHelper::uploadImage($request->license_front_image, 'upload/driver/' . $request->id . '/');
                    $data->driver->license_front_side = $image;
                }

                if ($request->has('license_back_image')) {
                    $image = ImageUploadHelper::uploadImage($request->license_back_image, 'upload/driver/' . $request->id . '/');
                    $data->driver->license_back_side = $image;
                }

                if ($request->has('registration_book')) {
                    $image = ImageUploadHelper::uploadImage($request->registration_book, 'upload/vehicle/' . $data->driver->vehicle->id . '/');
                    $data->driver->vehicle->registration_book = $image;
                }

                if ($request->has('profile_image')) {
                    $profile_image = ImageUploadHelper::uploadImage($request->profile_image, 'upload/driver/' . $request->id . '/');
                    $data->image = $profile_image;
                    $vehicleTypes =  VehicleType::where('status',1)->get();

                    $data->save();


//                    $data->update(['image'=>$profile_image]);
                }

                $data->driver->save();
                $data->driver->vehicle->save();

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Saving Documents: ' . $e, 500);
            }

            DB::commit();
            return makeResponse('success', 'Driver Information Save Successfully', 200,);

        } else {
            return makeResponse('error', 'Record Not Found', 500);

        }


    }


    public function changeStatus($request)
    {
        $data = User::find($request->id);

        if ($data) {
            try {
                if ($data->is_verified == 1) {
                    $data->is_verified = 0;
                } else {
                    $data->is_verified = 1;
                }
                $data->save();

                return makeResponse('success', 'Status Change Successfully', 200);

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Change Status: ' . $e, 500);

            }
        } else {
            return makeResponse('error', 'Record Not Found', 404);
        }
    }

    public function delete($request)
    {
        $data = User::find($request->id);

        if ($data) {
            try {

                $data->delete();

                return makeResponse('success', 'Driver Deleted Successfully', 200);

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Deleting Driver: ' . $e, 500);
            }
        } else {
            return makeResponse('error', 'Record Not Found', 404);
        }
    }

    public function deleteImage($request)
    {
        $data = User::find($request->id);

        if ($data) {
            if ($request->image == 'profile_image') {
                $data->image = null;

                $data->save();
            } elseif ($request->image == 'registration_book') {
                $data->driver->vehicle->registration_book = null;

                $data->driver->vehilce->save();
            } else {
                $data->driver->update(['' .$request->image => null]);
            }

            return makeResponse('success', 'Image Removed Successfully', 200);

        } else {
            return makeResponse('error', 'Record Not Found', 404);
        }


    }


}
