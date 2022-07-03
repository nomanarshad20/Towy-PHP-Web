<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DriversCoordinate;
use App\Models\P2PBookingTracking;
use App\Models\User;
use App\Services\API\Socket\DriverStatusService;
use App\Services\API\Socket\RideAcceptRejectService;
use App\Traits\FindDistanceTraits;

class SocketController extends Controller
{

    use FindDistanceTraits;

    public $acceptRejectService;
    public $driverService;

    public function __construct(RideAcceptRejectService $acceptRejectService, DriverStatusService $driverStatusService)
    {
        $this->acceptRejectService = $acceptRejectService;
        $this->driverService = $driverStatusService;
    }

    public function updateUser($data, $socket, $io)
    {
        if (!isset($data['socket_id'])) {
            $socket->emit('error', [
                'result' => 'error',
                'message' => 'Socket ID is a Required Field',
                'data' => null
            ]);
        }

        if (!isset($data['user_id'])) {
            $io->to($data['socket_id'])->emit('error',
                [
                    'result' => 'error',
                    'message' => 'User ID is a required field',
                    'data' => null
                ]);
        }

        $currentUser = User::where('id', $data['user_id'])->first();

        if (!$currentUser) {
            $io->to($data['socket_id'])->emit('error', [
                'result' => 'error',
                'message' => 'User Not Found',
                'data' => null
            ]);
        }



        if ($currentUser) {

            $currentUser->socket_id = $data['socket_id'];
            try {
                $currentUser->save();
                $io->to($data['socket_id'])->emit('socketIDUpdate',
                    [
                        'result' => 'success',
                        'message' => 'Socket ID Update Successfully',
                        'data' => [
                            'socket_id' => $data['socket_id'],
                            'user_id' => $data['user_id']
                        ]

                    ]);

            } catch (\Exception $e) {
                $io->to($data['socket_id'])->emit('error', [

                    'result' => 'error',
                    'message' => 'Error in Update: ' . $e,
                    'data' => null

                ]);
            }

        }

    }

    public function p2pTracking($data, $socket, $io)
    {
        try {

            if (!isset($data['socket_id'])) {
                $socket->emit('error', [
                    'result' => 'error',
                    'message' => 'Socket ID is a Required Field',
                    'data' => null
                ]);
            }

            if (!$data['user_id']) {
                $socket->emit('driverCoordinate', [
                        'result' => 'error',
                        'message' => 'User ID is a Required Field',
                        'data' => null
                    ]
                );
            }

            $currentUser = User::where('id', $data['user_id'])->where('socket_id', $data['socket_id'])->first();

            if (!$currentUser) {
                $io->emit('error',
                    [
                        'result' => 'driverCoordinate',
                        'message' => 'User Not Found',
                        'data' => null
                    ]
                );
            }


            $driver = DriversCoordinate::where("driver_id", $data['user_id'])
                ->first();
            if (!$driver) {

                $io->to($currentUser->socket_id)->emit('driverCoordinate', [
                    'result' => 'error',
                    'message' => 'Record Not Found',
                    'data' => null
                ]);

            }


            if($data['booking_id'])
            {

                $checkForBooking = Booking::where('driver_id', $data['user_id'])
                    ->where('id', $data['booking_id'])
                    ->where('ride_status', 1)->first();

                if ($checkForBooking) {

                    $calculateDistance = $this->getDistance($data->latitude, $data->longitude,
                        $driver->latitude,
                        $driver->longitude);

                    $distanceInKm = str_replace(',', '', str_replace('km', '', $calculateDistance['text']));

                    P2PBookingTracking::create(['booking_id' => $checkForBooking->id,
                        'driver_id' => $checkForBooking->driver_id,
                        'latitude' => $data->latitude, 'longitude' => $data->longitude,
                        'distance' => trim($distanceInKm),
                        'status' => $checkForBooking->driver_status]);

                    //saving driver current lat and lng
                    $driver->latitude = $data['latitude'];
                    $driver->latitude = $data['longitude'];
                    $driver->area_name = $data['area_name'];
                    $driver->city = $data['city'];
                    $driver->bearing = $data['bearing'];
                    $driver->status = 2;
                    $driver->save();

                    $passengerSocketId = $checkForBooking->passenger->socket_id;

                    if ($passengerSocketId) {
                        $io->to($passengerSocketId)->emit('driverCoordinate', [
                            'result' => 'success',
                            'message' => 'Driver Coordinate Save Successfully',
                            'data' => [
                                "latitude" => $driver->latitude,
                                "longitude" => $driver->longitude,
                                "city" => $driver->city,
                                "area_name" => $driver->area_name,
                                "bearing" => $driver->bearing
                            ],
                        ]);
                    }

                }
            }
            else {
                //saving driver current lat and lng
                $driver->latitude = $data['latitude'];
                $driver->latitude = $data['longitude'];
                $driver->area_name = $data['area_name'];
                $driver->city = $data['city'];
                $driver->bearing = $data['bearing'];
                $driver->save();
            }

            $io->to($currentUser->socket_id)->emit('driverCoordinate',
                [
                    'result' => 'success',
                    'message' => 'Driver Coordinate Save Successfully',
                    'data' => [
                        "latitude" => $driver->latitude,
                        "longitude" => $driver->longitude,
                        "city" => $driver->city,
                        "area_name" => $driver->area_name,
                        "bearing" => $driver->bearing
                    ],

                ]);

        } catch (\Exception $e) {

            $io->to($currentUser->socket_id)->emit('driverCoordinate',
                [
                    'result' => 'error',
                    'message' => 'Error in Saving Coordinate: ' . $e,
                    'data' => null

                ]);
        }
    }

    public function acceptRejectRide($data, $socket, $io)
    {
        if (!isset($data['booking_id'])) {
            $io->to($data['socket_id'])->emit('finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Booking ID is a Required Field',
                    'data' => null
                ]
            );
        }

        if (!isset($data['driver_action'])) {
            $io->to($data['socket_id'])->emit('finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Driver Action is a Required Field',
                    'data' => null
                ]
            );
        }

        if (!isset($data['socket_id'])) {
            $io->emit('finalRideStatus', [
                'result' => 'error',
                'message' => 'Socket ID is a Required Field',
                'data' => null
            ]);
        }

        return $this->acceptRejectService->rideAcceptReject($data, $socket, $io);

    }

    public function changeBookingDriverStatus($data, $socket, $io)
    {
        if (!isset($data['socket_id'])) {
            $socket->emit('error', [
                'result' => 'error',
                'message' => 'Socket ID is a Required Field',
                'data' => null
            ]);
        }

        if (!$data['user_id']) {
            $io->to($data['socket_id'])->emit('error', [
                    'result' => 'error',
                    'message' => 'User ID is a Required Field',
                    'data' => null
                ]
            );
        }


        $currentUser = User::where('id', $data['user_id'])
            ->where('socket_id', $data['socket_id'])
            ->first();

        if (!$currentUser) {
            $socket->emit('error',
                [
                    'result' => 'error',
                    'message' => 'User Not Found',
                    'data' => null
                ]
            );
        }


        if (!isset($data['booking_id'])) {
            $io->to($currentUser->socket_id)->emit('error', [
                'result' => 'error',
                'message' => 'Booking ID is a Required Field',
                'data' => null
            ]);
        }

        if (!isset($data['driver_status'])) {
            $io->to($currentUser->socket_id)->emit('error', [
                'result' => 'error',
                'message' => 'Driver Status is a Required Field',
                'data' => null
            ]);
        }

        if ($data['driver_status'] == 1) {
            return $this->driverService->reachToPickUp($data, $socket, $io, $currentUser);
        } elseif ($data['driver_status'] == 2) {
            return $this->driverService->startRide($data, $socket, $io, $currentUser);
        } elseif ($data['driver_status'] == 3) {
            return $this->driverService->completeRide($data, $socket, $io, $currentUser);
        }elseif($data['driver_status'] == 4){
            return $this->driverService->collectFare($data, $socket, $io, $currentUser);
        }


    }
}
