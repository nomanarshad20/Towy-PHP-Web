<?php


namespace App\Services\API\Passenger;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function index()
    {
        $data = Auth::user()->notifications;

        $notifications = array();
        foreach ($data as $notification) {


            $notifications[] = [
                'id' => $notification->id,
                'title' => 'Voucher Code',
                'read_at' => $notification->read_at,
                'message' => $notification->data['message'],
                'voucher_code' => $notification->data['voucher_code'],
                'discount_value' => $notification->data['discount_value'],
                'expiry_date' => $notification->data['expiry_date'],
                'discount_type' => $notification->data['discount_type'],
                'created_at' => Carbon::parse($notification->created_at)->format('d M')
            ];


        }


        if (sizeof($notifications) > 0) {
            return makeResponse('success', 'Notification Fetch Successfully', 200, $notifications);
        } else {
            return makeResponse('error', 'Record Not Found', 404);
        }

    }
}
