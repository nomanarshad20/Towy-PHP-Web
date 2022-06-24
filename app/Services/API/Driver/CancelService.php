<?php


namespace App\Services\API\Driver;


use App\Models\Booking;
use App\Models\BookingCancelReason;
use Illuminate\Support\Facades\Auth;

class CancelService
{
    public function cancelService($request)
    {
        $findBooking = Booking::where('id',$request->booking_id)->where('driver_id',Auth::user()->id)
        ->first();

        if(!$findBooking)
        {
            return makeResponse('error','Booking Not Found',404);
        }

        $findReason = BookingCancelReason::where('user_type','driver')
            ->where('id',$request->cancel_reason_id)->first();

        if(!$findReason)
        {
            return makeResponse('error','Cancel Reason Not Found',404);
        }

        if



    }
}
