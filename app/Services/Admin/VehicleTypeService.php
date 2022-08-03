<?php


namespace App\Services\Admin;


use App\Helper\ImageUploadHelper;
use App\Models\VehicleType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleTypeService
{


    public function index()
    {
        $data = VehicleType::all();

        return view('admin.vehicle_type.listing', compact('data'));
    }

    public function create()
    {
        return view('admin.vehicle_type.create');
    }

    public function save($request)
    {
        DB::beginTransaction();
        try {
            $saveImage = null;
            if ($request->has('image')) {
                $image = ImageUploadHelper::uploadImage($request->image, 'upload/vehicle-type/');
                $saveImage = $image;
            }

            VehicleType::create($request->except('_token','image')+['created_by'=>Auth::user()->id,'image'=>$saveImage]);
            DB::commit();
            return makeResponse('success', 'Vehicle Type Created Successfully', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Vehicle Type: ' . $e, 500);
        }
    }

    public function edit($id)
    {
        $data = VehicleType::find($id);
        if ($data) {
            return view('admin.vehicle_type.edit', compact('data'));
        } else {
            return redirect()->route('vehicleTypeListing')->with('error', 'Record Not Found');
        }
    }

    public function update($request)
    {
        $data = VehicleType::find($request->id);

        if($data)
        {
            DB::beginTransaction();
            try {
                $saveImage = $data->image;
                if ($request->has('image')) {
                    $image = ImageUploadHelper::uploadImage($request->image, 'upload/vehicle-type/');
                    $saveImage = $image;
                }


                $data->update($request->except('_token','image')+['image'=>$saveImage]);
                DB::commit();
                return makeResponse('success', 'Vehicle Type Updated Successfully', 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Updating Vehicle Type: ' . $e, 500);
            }
        }
        else{
            return makeResponse('error', 'Record Not Found', 404);
        }


    }

    public function delete($request)
    {
        $data = VehicleType::find($request->id);
        if($data)
        {
           $data->delete();

           return makeResponse('success','Vehicle Type Deleted Successfully',200);
        }
        else{
            return makeResponse('error','Record Not Found',404);

        }
    }

    public function changeStatus($request)
    {
        $data = VehicleType::find($request->id);

        if ($data) {
            try {
                if ($data->status == 1) {
                    $data->status = 0;
                } else {
                    $data->status = 1;
                }
                $data->save();

                return makeResponse('success', 'Status Change Successfully', 200);

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Change Status: ' . $e, 500);

            }
        }
        else{
            return makeResponse('error','Record Not Found',404);

        }

    }

    public function deleteImage($request)
    {
        $data = VehicleType::find($request->id);

        if ($data) {
            $data->image = null;

            $data->save();


            return makeResponse('success', 'Image Removed Successfully', 200);

        } else {
            return makeResponse('error', 'Record Not Found', 404);
        }


    }
}
