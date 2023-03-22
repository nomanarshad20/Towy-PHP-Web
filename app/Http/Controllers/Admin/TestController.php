<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\API\Driver\ProfileService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Lyal\Checkr\Client as LyalClient;
use Lyal\Checkr\Checkr;

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
                "Authorization: Basic " . base64_encode('1ad8868cb14f4d91483eaa2d037c4246a643ef17:'),
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = "first_name=Test&middle_name=User&last_name=My&email=test@gmail.com&phone=5555555555&zipcode=90401&dob=1970-01-22&ssn=111-11-1234&driver_license_number=F2111655&driver_license_state=CA&work_locations[][country]=US";

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

    public function test1()
    {
        $client = new LyalClient(env('checkr_test_key'));
//        $checkr = new Checkr($client);
        $checkr = $client;

        try {
            $candidate = $checkr->candidates()->create([
                'first_name' => 'Testing',
                'last_name' => 'User',
                'email' => 'test@gmail.com',
                'phone' => '+1 555-555-1212',
                'zipcode' => '90210',
                'dob' => '1990-01-01',
                'ssn' => '123-45-6789',
                'driver_license_number' => 'F1234567',
                'driver_license_state' => 'CA',
            ]);
        } catch (\Exception $e) {
            dd('Error in create candidate: ' . $e);
        }

        try {
            $criminal_check = $checkr->criminal_records()->create([
                'candidate_id' => $candidate->id,
                'report_type' => 'county',
                'county_criminal_search' => [
                    'geos' => ['san_francisco_county'],
                    'include' => ['misdemeanors', 'felonies'],
                    'exclude' => ['traffic'],
                ],
            ]);
        } catch (\Exception $e) {
            dd('Error in checking criminal: ' . $e);
        }

        try {
            $ssn_trace = $checkr->ssn_traces()->create($candidate->id);
            $ssnReport = $ssn_trace;
        } catch (\Exception $e) {
            dd('Error in checking ssn: ' . $e);
        }

        dd($candidate, $ssn_trace->status, $criminal_check->status);


    }

    public function test2()
    {

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode('1ad8868cb14f4d91483eaa2d037c4246a643ef17:')
        ];

        $candidateID = "701da778f9088a44690b6b0c";


//        try {
////            $candidateResponse = '{
////                    "first_name" => "Testing",
////                    "last_name" => "User",
////                    "email" => "test@gmail.com",
////                    "phone" => "1 555-555-1212",
////                    "zipcode" => "90210",
////                    "dob" => "1990-01-01",
////                    "ssn" => "123-45-6789",
////                    "driver_license_number" => "F1234567",
////                    "driver_license_state" => "CA",
////            }';
//
//            $body = '{
//  "first_name": "Testing",
//  "last_name": "User",
//  "email": "test@gmail.com",
//  "middle_name": "Alfred",
//  "no_middle_name": false,
//  "mother_maiden_name": "Jones",
//  "phone": "1 555-555-1212",
//  "zipcode": "90210",
//  "dob": "1970-01-22",
//  "ssn": "111-11-1111",
//  "driver_license_number": "F2111655",
//  "driver_license_state": "CA",
//  "metadata": {},
//  "work_locations": [
//    {
//      "country": "US",
//    }
//  ]
//}';
//
//
//        } catch (\Exception $e) {
//            dd('Error in create candidate: ' . $e);
//        }
//
//
//        $request =  new \GuzzleHttp\Psr7\Request('POST',
//            'https://api.checkr.com/v1/candidates',
//            $headers, $body);
//        $res = $client->sendAsync($request)->wait();
//        $candidate = $res->getBody();
//
//        dump($candidate);

//        $candidate = json_decode($candidateResponse->getBody()->getContents());


//        $ssn_trace = json_decode($ssnResponse->getBody()->getContents());

        try {
            $criminalResponse = '{
    "type": "criminal",
    "candidate_id": "' . $candidateID . '",
    "package": "county",
    "geos": ["US-NY"],
    "county_criminal_search": {
        "county": "New York",
        "state": "NY"
    }
}';
        } catch (\Exception $e) {
            dd('Error in checking criminal: ' . $e);
        }

//        dd($criminalResponse);

        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://api.checkr.com/v1/candidates/701da778f9088a44690b6b0c/screenings', $headers, $criminalResponse);
        $res = $client->sendAsync($request)->wait();
        $criminal_check = $res->getBody();


        try {
            $ssnResponse = "{
                    'type' => 'ssn_trace',
                    'candidate_id' => $candidateID,
                    'ssn' => '123-45-6789'
               }";
        } catch (\Exception $e) {
            dd('Error in checking ssn: ' . $e);
        }

        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://api.checkr.com/v1/screenings', $headers, $ssnResponse);
        $res = $client->sendAsync($request)->wait();
        $ssn_trace = $res->getBody();

//        $criminal_check = json_decode($criminalResponse->getBody()->getContents());

        dd($criminal_check, $ssn_trace);


    }

    public function createReport()
    {
        $client = new Client([
            'base_uri' => 'https://api.checkr.com/v1/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode('1ad8868cb14f4d91483eaa2d037c4246a643ef17:')
            ]
        ]);


        $candidateID = "fd843dd78adfe6ed9367d314";


        $response = $client->post('reports', [
            'json' => [
                'candidate_id' => $candidateID,
                'package' => 'driver_pro',
                'driver_pro' => [
                    'work_locations' => [
                        [
                            'address' => [
                                'street' => '123 Main St',
                                'city' => 'Anytown',
                                'state' => 'CA',
                                'zip' => '12345',
                                'country' => 'US',
                            ],
                            'geocode' => [
                                'accuracy' => 'rooftop',
                                'lat' => 37.7749,
                                'lng' => -122.4194,
                            ],
                            'name' => 'Work Location 1',
                            'reference_id' => 'work_location_1',
                            'time_zone' => 'America/Los_Angeles',
                        ]
                    ]
                ],
            ]

        ]);

//        "geos": ["US-NY"],
//    "county_criminal_search": {
//        "county": "New York",
//        "state": "NY"
//    }
        $reportId = json_decode($response->getBody(), true)['id'];

// Step 3: Retrieve the report status and results
        $response = $client->get('reports/' . $reportId);
        $reportStatus = json_decode($response->getBody(), true)['status'];
        $reportResults = json_decode($response->getBody(), true)['results'];

        echo "Report status: " . $reportStatus . "\n";
        echo "Report results: " . print_r($reportResults, true) . "\n";

    }

    public function listPackage()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.checkr.com/v1/packages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . base64_encode('1ad8868cb14f4d91483eaa2d037c4246a643ef17:'),
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        print_r($response);
        dd($response);
    }

    public function listCandidate()
    {
        // Set your API key and base URL
        $apiKey = '1ad8868cb14f4d91483eaa2d037c4246a643ef17';
        $baseUrl = 'https://api.checkr.com';

// Set up cURL to make API request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/v1/candidates');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode($apiKey),
        ));

// Send the request and get the response
        $response = curl_exec($ch);

// Close cURL handle
        curl_close($ch);

// Process the response
        $candidates = json_decode($response, true);

        dd($candidates);

    }

    public function retrieveReport()
    {

        $reportId = 'fd843dd78adfe6ed9367d314';
        $apiKey = env('checkr_api_key');


        $client = new Client([
            'base_uri' => 'https://api.checkr.com/v1/',
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($apiKey),
                'Content-Type' => 'application/json'
            ]
        ]);

        $response = $client->get('reports/' . $reportId);

        if ($response->getStatusCode() === 200) {
            $report = json_decode($response->getBody()->getContents(), true);
            dd($report);
            // Do something with the report data
        } else {
            dd('error');
            // Handle error response
        }

    }


}
