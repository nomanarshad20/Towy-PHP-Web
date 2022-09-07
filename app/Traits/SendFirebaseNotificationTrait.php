<?php


namespace App\Traits;


trait SendFirebaseNotificationTrait
{

    public function rideRequestNotification($driver,$booking,$notification_type)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = 'New Ride Request';
        $data['body'] = 'New Ride Request Received';
        $data['data'] = (object)$booking;

        $this->sendPushNotification($driver['fcm_token'],$data);

        return true;
    }

    public function rideAcceptNotification($passengerFCM,$booking,$notification_type)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = 'Ride Accepted By Driver';
        $data['body'] = 'Your Ride Has Been Accepted By Driver. Hang on Tight';
        $data['data'] = (object)$booking;

        $this->sendPushNotification($passengerFCM,$data);

        return true;
    }

    public function duringRideNotifications($passengerFCM,$booking,$notification_type,$title,$message)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = (object)$booking;

        $this->sendPushNotification($passengerFCM,$data);

        return true;
    }

    public function voucherNotificaiton($fcm,$notification_type,$title,$message,$voucher)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = (object)$voucher;

        $this->sendPushNotification($fcm,$data);

        return true;
    }

    public function driverRideAcceptRejectNotification($driverFCM,$booking,$notification_type,$title,$message)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data['title'] = $title;
        $data['body'] = $message;
        //$data['data'] = (object)$booking;
        $data['data'] = $booking;

        $this->sendPushNotification($driverFCM,$data);

        return true;
    }

    public function cancelRide($fcm,$notification_type,$title,$message)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = null;

        $this->sendPushNotification($fcm,$data);

        return true;
    }

    public function bookingEndNotification($passengerFCM,$booking,$notification_type)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data['title'] = 'No Driver is Currently Free at the Moment';
        $data['body'] = 'No Driver is Currently Free at the Moment. Request Again after sometime. If the issue persist contact with admin for that.';
        $data['data'] = (object)$booking;

        $this->sendPushNotification($passengerFCM['fcm_token'],$data);

        return true;
    }

    public function sendPushNotification($fcm, $dataBody)
    {

        $client = new \GuzzleHttp\Client(['verify' => false]);
        $API_SERVER_KEY = env('FCM_SERVER_KEY');



        $request = $client->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'headers' => [
                    'Authorization' => 'key=' . $API_SERVER_KEY,
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    "to" => $fcm,
                    "priority" => "high",
                    "content_available" => true,
                    "mutable_content" => true,
                    "time_to_live" => 35,
//                    "notification" => $dataNoti,
                    "data" => $dataBody
                ])
            ]
        );

        $response = $request->getBody();
        $response = json_decode($response);


        if ($response->success > 0) {
            return true;
        } else {
            return false;
        }


    }
}
