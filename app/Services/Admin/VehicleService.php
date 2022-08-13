<?php


namespace App\Services\Admin;


use App\Helper\ImageUploadHelper;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Support\Facades\DB;

class VehicleService
{
    public function index()
    {
        $data = Vehicle::all();

        return view('admin.vehicle.listing', compact('data'));
    }

    public function create()
    {
        $drivers = Driver::whereNull('vehicle_id')->get();
//        $vehicleTypes =  VehicleType::where('status',1)->get();
        return view('admin.vehicle.create', compact('drivers'));
    }

    public function save($request)
    {
        DB::beginTransaction();

        try {


            $vehicle = Vehicle::create($request->except('_token', 'driver_id', 'registration_book')+['vehicle_type_id' => 1]);

            if ($request->has('registration_book')) {
                $image = ImageUploadHelper::uploadImage($request->registration_book, 'upload/vehicle/' . $vehicle->id . '/');
                $vehicle->update(['registration_book' => $image]);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Vehicle: ' . $e, 200);
        }

        if ($request->driver_id) {
            try {
                $checkDriver = Driver::where('user_id', $request->driver_id)->whereNotNull('vehicle_id')->first();
                if ($checkDriver) {
                    DB::rollBack();
                    return makeResponse('error', 'Vehicle already assigned to that Driver', 200);
                }
                $saveDriver = Driver::where('user_id', $request->driver_id)->update(['vehicle_id' => $vehicle->id]);
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Attaching Vehicle With Driver: ' . $e, 200);
            }
        }

        DB::commit();
        return makeResponse('success', 'Vehicle Successfully Created', 200);


    }


    public function edit($id)
    {
        $data = Vehicle::find($id);

        if ($data) {
            $drivers = Driver::whereNull('vehicle_id')->orWhere('vehicle_id', $data->id)->get();
//            $vehicleTypes =  VehicleType::where('status',1)->get();


            return view('admin.vehicle.edit', compact('data', 'drivers'));
        } else {
            return redirect()->route('vehicleListing')->with('error', 'Record Not Found');
        }

    }

    public function update($request)
    {
        $data = Vehicle::find($request->id);
        DB::beginTransaction();

        try {

            $saveImage = $data->registration_book;
            if ($request->has('registration_book')) {
                $image = ImageUploadHelper::uploadImage($request->registration_book, 'upload/vehicle/' . $data->id . '/');
                $saveImage = $image;
            }

            $data->update([
                'name' => $request->name,
                'model' => $request->model,
                'model_year' => $request->model_year,
                'registration_number' => $request->registration_number,
                'registration_book' => $saveImage
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Updating Vehicle: ' . $e, 200);
        }

        if ($request->driver_id) {
            try {
                $checkDriver = Driver::where('user_id', $request->driver_id)
                    ->where('vehicle_id', '!=', $data->id)
                    ->first();
                if ($checkDriver && $checkDriver->vehicle_id != null) {
                    DB::rollBack();
                    return makeResponse('error', 'Vehicle already assigned to that Driver', 200);
                }
                $saveDriver = Driver::where('user_id', $request->driver_id)->update(['vehicle_id' => $data->id]);
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Attaching Vehicle With Driver: ' . $e, 200);
            }
        }

        DB::commit();
        return makeResponse('success', 'Vehicle Updated Successfully', 200);


    }

    public function delete($request)
    {
        $data = Vehicle::find($request->id);

        if ($data) {
            try {

                $data->delete();

                return makeResponse('success', 'Vehicle Deleted Successfully', 200);

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Deleting Vehicle: ' . $e, 200);
            }
        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }
    }

    public function deleteImage($request)
    {
        $data = Vehicle::find($request->id);

        if ($data) {
           if ($request->image == 'registration_book') {
                $data->registration_book = null;

                $data->save();
            }

            return makeResponse('success', 'Image Removed Successfully', 200);

        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }


    }

//    public function changeStatus($request)
//    {
//        $data = Vehicle::find($request->id);
//
//        if ($data) {
//            try {
//                if ($data->is_verified == 1) {
//                    $data->is_verified = 0;
//                } else {
//                    $data->is_verified = 1;
//                }
//                $data->save();
//
//                return makeResponse('success', 'Status Change Successfully', 200);
//
//            } catch (\Exception $e) {
//                return makeResponse('error', 'Error in Change Status: ' . $e, 500);
//
//            }
//        } else {
//            return makeResponse('error', 'Record Not Found', 404);
//        }
//    }
}
