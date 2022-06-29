<?php


namespace App\Services\Admin;


use App\Models\PeakFactor;

class PeakService
{
    public function index()
    {
        $data =  PeakFactor::all();

        return view('admin.peak_factor.listing',compact('data'));
    }

    public function create()
    {
        return view('admin.peak_factor.create');
    }

    public function save($request)
    {
        try{
            PeakFactor::create($request->validated());
            return makeResponse('success','Record Save Successfully.',200);

        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Saving Data: '.$e,500);
        }
    }

    public function edit($id)
    {
        $data = PeakFactor::find($id);

        if($data)
        {
            return view('admin.peak_factor.edit',compact('data'));
        }
        else{
            return redirect()->route('peakFactorListing')->with('error','Record Not Found');
        }
    }

    public function update( $request)
    {
        $data = PeakFactor::find($request->id);

        if($data)
        {
            try{
                $data->update($request->validated());
                return makeResponse('success','Record Updated Successfully.',200);

            }
            catch (\Exception $e)
            {
                return makeResponse('error','Error in Updating Data: '.$e,500);
            }
        }
        else{
            return makeResponse('error','Record Not Found',404);
        }

    }

    public function delete($request)
    {
        $data = PeakFactor::find($request->id);

        if($data)
        {
            $data->delete();
            return makeResponse('success','Record Deleted Successfully',200);
        }
        else{
            return makeResponse('error','Record Not Found',404);
        }
    }
}
