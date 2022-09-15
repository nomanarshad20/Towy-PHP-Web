<?php


namespace App\Traits;


trait SendFirebaseNotificationTrait
{

    public function rideRequestNotification($driver,$booking,$notification_type)
    {
        $title  = 'New Ride Request';
        $message = 'New Ride Request Received';

        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = (object)$booking;

        $notification = array();
        $notification['notification_type']  = $notification_type;
        $notification ['title'] = $title;
        $notification['body'] = $message;
        $notification['data'] = (object)$booking;

        $this->sendPushNotification($driver['fcm_token'],$data,$notification);

        return true;
    }

    public function rideAcceptNotification($passengerFCM,$booking,$notification_type)
    {
        $title  = 'Ride Accepted By Driver';
        $message =  'Your Ride Has Been Accepted By Driver. Hang on Tight';



        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = (object)$booking;

        $notification = array();
        $notification['notification_type']  = $notification_type;
        $notification ['title'] = $title;
        $notification['body'] = $message;
        $notification['data'] = (object)$booking;

        $this->sendPushNotification($passengerFCM,$data,$notification);

        return true;
    }

    public function duringRideNotifications($passengerFCM,$booking,$notification_type,$title,$message)
    {
        $title  = 'Ride Accepted By Driver';
        $message =  'Your Ride Has Been Accepted By Driver. Hang on Tight';



        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = (object)$booking;


        $notification = array();
        $notification['notification_type']  = $notification_type;
        $notification ['title'] = $title;
        $notification['body'] = $message;
        $notification['data'] = (object)$booking;

        $this->sendPushNotification($passengerFCM,$data,$notification);

        return true;
    }

    public function voucherNotificaiton($fcm,$notification_type,$title,$message,$voucher)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = (object)$voucher;

        $notification = array();
        $notification['notification_type']  = $notification_type;
        $notification ['title'] = $title;
        $notification['body'] = $message;
        $notification['data'] = (object)$voucher;

        $this->sendPushNotification($fcm,$data,$notification);

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

        $notification = array();
        $notification['notification_type']  = $notification_type;
        $notification ['title'] = $title;
        $notification['body'] = $message;
        $notification['data'] = $booking;

        $this->sendPushNotification($driverFCM,$data,$notification);

        return true;
    }

    public function cancelRide($fcm,$notification_type,$title,$message)
    {
        $data = array();
        $data['notification_type']  = $notification_type;
        $data ['title'] = $title;
        $data['body'] = $message;
        $data['data'] = null;


        $notification = array();
        $notification['notification_type']  = $notification_type;
        $notification ['title'] = $title;
        $notification['body'] = $message;
        $notification['data'] = null;

        $this->sendPushNotification($fcm,$data,$notification);

        return true;
    }

    public function bookingEndNotification($passengerFCM,$booking,$notification_type)
    {

        $title  = 'No Driver is Currently Free at the Moment';
        $message =  'No Driver is Currently Free at the Moment. Request Again after sometime. If the issue persist contact with admin for that.';


        $data = array();
        $data['notification_type']  = $notification_type;
        $data['title'] = $title;
        $data['body'] = $message;
        $data['data'] = (object)$booking;

        $notification = array();
        $notification['notification_type']  = $notification_type;
        $notification ['title'] = $title;
        $notification['body'] = $message;
        $notification['data'] =  (object)$booking;

        $this->sendPushNotification($passengerFCM['fcm_token'],$data,$notification);

        return true;
    }

    public function sendPushNotification($fcm, $dataBody,$notificationBody)
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
                    "time_to_live" => 10,
                    "notification" => $notificationBody,
                    "data" => $dataBody,
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
