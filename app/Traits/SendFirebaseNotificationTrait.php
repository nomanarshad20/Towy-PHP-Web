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
                    "to" => 'dO3iYmudTKi8_9SFaxhkEY:APA91bHNX0j9NPmjkddfzBOlX1Q2pqAth2XX6JCq0WaUaHngvoMTKO3RArpuTHxABNv7Xmle0CqjX1Qe2TEnD8451NxBQTKNhH-kWPxsKk2802YP2cbIsof0dLGo5iKh4G1rlmhTFgZ4',
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
