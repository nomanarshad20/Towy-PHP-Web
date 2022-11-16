<?php


namespace App\Services\Admin;


use App\Helper\ImageUploadHelper;
use App\Models\Service;
use http\Env\Request;

class ServicesService
{
    public function index()
    {
        $data = Service::all();
        return view('admin.service.listing',compact('data'));
    }

    public function create()
    {
        return view('admin.service.create');
    }

    public function save($request,$service)
    {
        try{
            $service->name =  $request->name;
//            $service->initial_distance_rate =  $request->initial_distance_rate;
//            $service->initial_time_rate =  $request->initial_time_rate;
            $service->base_rate =  $request->base_rate;
//            $service->service_time_rate =  $request->service_time_rate;
            $service->description = $request->description;
            if($request->is_quantity_allowed)
            {
                $service->is_quantity_allowed = $request->is_quantity_allowed;
            }

            if($request->has('image'))
            {
                $image = ImageUploadHelper::uploadImage($request->image, 'upload/service/');
                $service->image = $image;
            }
            $service->description = $request->description;

            $service->save();
            return response()->json(['result'=>'success','message'=>'Record Save Successfully']);
        }
        catch (\Exception $e)
        {
            return response()->json(['result'=>'error','message'=>'Error in saving Service Record: '.$e]);
        }
    }

    public function edit($id)
    {
        $data = Service::find($id);
        if($data)
        {
            return view('admin.service.edit',compact('data'));
        }
        else{
            return redirect()->route('serviceListing')->with('error','Record Not Found');
        }
    }

    public function deleteImage($request)
    {
        $data = Service::find($request->id);

        if($data)
        {
            $data->image = null;
            $data->save();
            return response()->json(['result'=>'success','message'=>'Image Deleted Successfully']);
        }
        else{
            return response()->json(['result'=>'error','message'=>'Record Not Found']);
        }
    }

    public function delete($request)
    {
        try{
            $data =  Service::find($request->id);

            if($data)
            {
                $data->delete();

                return response()->json(['result'=>'success','message'=>'Record Deleted Successfully']);
            }
            else{
                return response()->json(['result'=>'error','message'=>'Record Not Found']);
            }

        }
        catch (\Exception $e)
        {
            return response()->json(['result'=>'error','message'=>'Error in Delete Record: '.$e]);
        }
    }
}
