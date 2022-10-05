<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test()
    {


        try {
            $url = "https://api.checkr.com/v1/candidates";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Basic ".base64_encode('1ad8868cb14f4d91483eaa2d037c4246a643ef17:'),
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = "first_name=John&middle_name=Alfred&last_name=Smith&email=john.smith@gmail.com&phone=5555555555&zipcode=90401&dob=1970-01-22&ssn=111-11-1111&driver_license_number=F2111655&driver_license_state=CA&work_locations[][country]=US";

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);
            dd($resp);



//            $client = new \GuzzleHttp\Client(['verify' => false]);
//
//            $makeRequest = $client->post(
//                'https://api.checkr-staging.com/v1/candidates',
//                [
//                    'headers' => [
//                        'Content-Type' => 'application/json',
//                        'Authorization' => '1ad8868cb14f4d91483eaa2d037c4246a643ef17'
//                    ],
//                    'body' => json_encode([
//                        'first_name' => 'John',
//                        'middle_name' => 'Alfred',
//                        'last_name' => 'Smith',
//                        'email' => 'john.smith@gmail.com',
//                        'phone' => '5555555555',
//                        'zipcode' => '90401',
//                        'dob' => '1970-01-22',
//                        'ssn' => '847-43-4645',
//                        'driver_license_number' => 'F2111655',
//                        'driver_license_state' => 'CA',
//                    ])
//                ]
//            );
//
//            $response = $makeRequest->getBody();
//            $response = json_decode($response);
//
//            dd($response);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
