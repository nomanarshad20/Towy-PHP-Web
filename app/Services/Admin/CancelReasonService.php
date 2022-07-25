<?php


namespace App\Services\Admin;


use App\Models\BookingCancelReason;

class CancelReasonService
{
    public function index()
    {
        $data = BookingCancelReason::all();

        return view('admin.cancel_reason.listing',compact('data'));
    }

    public function create()
    {
        return view('admin.cancel_reason.create');

    }

    public function save( $request)
    {
        try{
            BookingCancelReason::create($request->validated());

            return makeResponse('success','Cancel Reason Created Successfully',200);
        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Creation Cancel Reason: '.$e,500);
        }
    }

    public function edit($id)
    {
        $data = BookingCancelReason::find($id);
        if($data)
        {
            return view('admin.cancel_reason.edit',compact('data'));
        }
        else{
            return redirect()->route('cancelReasonListing')->with('error','Record Not Found');
        }
    }

    public function update($request)
    {
        $data =  BookingCancelReason::find($request->id);

        if($data)
        {
            $data->update($request->validated());

            return makeResponse('success','Record Updated Successfully',200);
        }
        else{
            return makeResponse('error','Record Not Found',500);
        }
    }

    public function delete($request)
    {
        $data =  BookingCancelReason::find($request->id);

        if($data)
        {
            try{
                $data->delete();

                return makeResponse('success','Record Deleted Successfully',200);
            }
            catch (\Exception $e)
            {
                return makeResponse('error','Error in Record Deletion: '.$e,500);

            }

        }
        else{
            return makeResponse('error','Record Not Found',500);
        }
    }
}
