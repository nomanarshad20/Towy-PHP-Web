<?php


namespace App\Services\API;


use App\Models\BookingCancelReason;
use Illuminate\Support\Facades\Auth;

class CancelReasonService
{
    public function index()
    {
        $cancelReasons =  BookingCancelReason::select('id','reason','user_type');
        if(Auth::user()->user_type == 1)
        {
            $cancelReasons =  $cancelReasons->where('user_type','passenger');
        }
        elseif(Auth::user()->user_type == 2){
            $cancelReasons =  $cancelReasons->where('user_type','driver');
        }

        $cancelReasons = $cancelReasons->get();

        if(sizeof($cancelReasons) > 0)
        {
            return makeResponse('success','Cancel Reason Found Successfully',200,$cancelReasons);
        }
        else{
            return makeResponse('error','Record Not Found',404);
        }

    }
}
