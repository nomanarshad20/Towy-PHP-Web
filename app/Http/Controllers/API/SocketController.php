<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DriversCoordinate;
use App\Models\P2PBookingTracking;
use App\Models\User;
use App\Services\API\Socket\AcceptRejectServiceRide;
use App\Services\API\Socket\DriverCancelService;
use App\Services\API\Socket\DriverStatusService;
use App\Services\API\Socket\RideAcceptRejectService;
use App\Traits\BookingResponseTrait;
use App\Traits\CreateUserWalletTrait;
use App\Traits\FindDistanceTraits;
use App\Traits\FindDriverTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SocketController extends Controller
{

    use FindDistanceTraits, BookingResponseTrait, CreateUserWalletTrait, FindDriverTrait;

    public $acceptRejectService;
    public $driverService;
    public $driverCancelService;
    public $acceptRejectServiceRide;

    public function __construct(RideAcceptRejectService $acceptRejectService
        , DriverStatusService $driverStatusService, DriverCancelService $cancelService
        , AcceptRejectServiceRide $acceptRejectServiceRide)
    {
        $this->acceptRejectService = $acceptRejectService;
        $this->driverService = $driverStatusService;
        $this->driverCancelService = $cancelService;
        $this->acceptRejectServiceRide = $acceptRejectServiceRide;
    }

    public function updateUser($data, $socket, $io)
    {
        if (!isset($data['socket_id'])) {
            $socket->emit('socketIDUpdate', [
                'result' => 'error',
                'message' => 'Socket ID is a Required Field',
                'data' => null
            ]);
        }

        if (!isset($data['user_id'])) {
            $io->to($data['socket_id'])->emit('socketIDUpdate',
                [
                    'result' => 'error',
                    'message' => 'User ID is a required field',
                    'data' => null
                ]);
        }

        $currentUser = User::where('id', $data['user_id'])->first();

        if (!$currentUser) {
            $io->to($data['socket_id'])->emit('socketIDUpdate', [
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

            if (!isset($data['user_id'])) {
                return $socket->emit('driverCoordinate', [
                        'result' => 'error',
                        'message' => 'User ID is a Required Field',
                        'data' => null
                    ]
                );


            }

            $currentUser = User::where('id', $data['user_id'])->first();

            if (!$currentUser) {
                return $socket->emit($data['user_id'] . '-driverCoordinate',
                    [
                        'result' => 'error',
                        'message' => 'User Not Found',
                        'data' => null
                    ]
                );
            }


            $driver = DriversCoordinate::where("driver_id", $data['user_id'])
                ->first();

            $distance = 21;


            if (isset($data['booking_id']) && $data['booking_id'] != 0) {

                $checkForBooking = Booking::where('driver_id', $data['user_id'])
                    ->where('id', $data['booking_id'])
                    ->where('ride_status', 1)->first();


                if ($checkForBooking) {

                    if ($data['latitude'] == $driver->latitude && $data['longitude'] == $driver->longitude) {
                        return $socket->emit($data['user_id'] . '-driverCoordinate',
                            [
                                'result' => 'success',
                                'message' => 'Driver Coordinate Same So Not Save',
                                'data' => [
                                    "latitude" => $driver->latitude,
                                    "longitude" => $driver->longitude,
                                    "city" => $driver->city,
                                    "area_name" => $driver->area_name,
                                    "bearing" => $driver->bearing
                                ],

                            ]);
                    }

                    $haveClause = $this->findDistanceFormula($data['latitude'], $data['longitude'], $driver->latitude, $driver->longitude);

                    $distance = $haveClause;

                    if ($distance > 20) {

                        P2PBookingTracking::create(['booking_id' => $checkForBooking->id,
                            'driver_id' => $checkForBooking->driver_id,
                            'latitude' => $data['latitude'], 'longitude' => $data['longitude'],
                            'distance' => trim($distance),
                            'driver_status' => $checkForBooking->driver_status
                        ]);

                        //saving driver current lat and lng
                        $driver->latitude = $data['latitude'];
                        $driver->longitude = $data['longitude'];
                        $driver->area_name = $data['area_name'];
                        $driver->city = $data['city'];
                        $driver->bearing = $data['bearing'];
                        $driver->status = 2;
                        $driver->save();


//                    if ($passengerSocketId) {
                        $socket->emit($checkForBooking->passenger_id . '-driverCoordinate', [
                            'result' => 'success',
                            'message' => 'Driver Coordinate Send Successfully',
                            'data' => [
                                "latitude" => $driver->latitude,
                                "longitude" => $driver->longitude,
                                "city" => $driver->city,
                                "area_name" => $driver->area_name,
                                "bearing" => $driver->bearing,
                                'distance' => $distance

                            ],
                        ]);


                        return $socket->emit($data['user_id'] . '-driverCoordinate',
                            [
                                'result' => 'success',
                                'message' => 'Driver Coordinate Save Successfully',
                                'data' => [
                                    "latitude" => $driver->latitude,
                                    "longitude" => $driver->longitude,
                                    "city" => $driver->city,
                                    "area_name" => $driver->area_name,
                                    "bearing" => $driver->bearing,
                                    'distance' => $distance

                                ],

                            ]);
                    } else {
                        $socket->emit($checkForBooking->passenger_id . '-driverCoordinate', [
                            'result' => 'success',
                            'message' => 'Driver Coordinate is less than 20m',
                            'data' => [
                                "latitude" => $driver->latitude,
                                "longitude" => $driver->longitude,
                                "city" => $driver->city,
                                "area_name" => $driver->area_name,
                                "bearing" => $driver->bearing,
                                'distance' => $distance

                            ],
                        ]);


                        return $socket->emit($data['user_id'] . '-driverCoordinate',
                            [
                                'result' => 'success',
                                'message' => 'Driver Coordinate is less than 20m',
                                'data' => [
                                    "latitude" => $driver->latitude,
                                    "longitude" => $driver->longitude,
                                    "city" => $driver->city,
                                    "area_name" => $driver->area_name,
                                    "bearing" => $driver->bearing,
                                    'distance' => $distance

                                ],

                            ]);
                    }

                }


            } else {
                $distance = 21;
                if (!isset($driver)) {
                    $driver = new DriversCoordinate;
                    $driver->driver_id = $data['user_id'];

                } else {
                    $distance = 0;
                    if ($data['latitude'] == $driver->latitude && $data['longitude'] == $driver->longitude) {
                        return $socket->emit($data['user_id'] . '-driverCoordinate',
                            [
                                'result' => 'success',
                                'message' => 'Driver Coordinate Same So Not Save',
                                'data' => [
                                    "latitude" => $driver->latitude,
                                    "longitude" => $driver->longitude,
                                    "city" => $driver->city,
                                    "area_name" => $driver->area_name,
                                    "bearing" => $driver->bearing,
                                    'distance' => $distance

                                ],

                            ]);
                    }

                    $haveClause = $this->findDistanceFormula($data['latitude'], $data['longitude'], $driver->latitude, $driver->longitude);
                    $distance = $haveClause;
                }


                if ($distance > 20) {
                    //saving driver current lat and lng
                    $driver->latitude = $data['latitude'];
                    $driver->longitude = $data['longitude'];
                    $driver->area_name = $data['area_name'];
                    $driver->city = $data['city'];
                    $driver->bearing = $data['bearing'];
                    $driver->save();
                } else {
                    return $socket->emit($data['user_id'] . '-driverCoordinate',
                        [
                            'result' => 'success',
                            'message' => 'Driver Coordinate Not Save because distance is less than 20 meter',
                            'data' => [
                                "latitude" => $driver->latitude,
                                "longitude" => $driver->longitude,
                                "city" => $driver->city,
                                "area_name" => $driver->area_name,
                                "bearing" => $driver->bearing,
                                'distance' => $distance
                            ],

                        ]);
                }

            }


            return $socket->emit($data['user_id'] . '-driverCoordinate',
                [
                    'result' => 'success',
                    'message' => 'Driver Coordinate Save Successfully',
                    'data' => [
                        "latitude" => $driver->latitude,
                        "longitude" => $driver->longitude,
                        "city" => $driver->city,
                        "area_name" => $driver->area_name,
                        "bearing" => $driver->bearing,
                        'distance' => $distance
                    ],

                ]);

        } catch (\Exception $e) {

            return $socket->emit($data['user_id'] . '-driverCoordinate',
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
            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Booking ID is a Required Field',
                    'data' => null
                ]
            );
        }

        if (!isset($data['driver_action'])) {
            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Driver Action is a Required Field',
                    'data' => null
                ]
            );
        }

        return $this->acceptRejectService->rideAcceptReject($data, $socket, $io);

    }

    public function changeBookingDriverStatus($data, $socket, $io)
    {

        if (!$data['user_id']) {
            return $socket->emit('driverStatus', [
                    'result' => 'error',
                    'message' => 'User ID is a Required Field',
                    'data' => null
                ]
            );
        }


        $currentUser = User::where('id', $data['user_id'])
            ->first();

        if (!$currentUser) {
            return $socket->emit($data['user_id'] . '-driverStatus',
                [
                    'result' => 'error',
                    'message' => 'User Not Found',
                    'data' => null
                ]
            );
        }


        if (!isset($data['booking_id'])) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Booking ID is a Required Field',
                'data' => null
            ]);
        }

        if (!isset($data['driver_status'])) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
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
        } elseif ($data['driver_status'] == 4) {
            return $this->driverService->collectFare($data, $socket, $io, $currentUser);
        }
    }

    public function walletPayment($data, $io, $socket)
    {
        DB::beginTransaction();


        if (!isset($data['user_id'])) {
            return $socket->emit('walletPaymentResponse', [
                    'result' => 'error',
                    'message' => 'User ID is a Required Field',
                    'data' => null
                ]
            );
        }

        if (!isset($data['payment_type'])) {
            return $socket->emit($data['user_id'] . '-walletPaymentResponse', [
                    'result' => 'error',
                    'message' => 'Payment Type is a Required Field',
                    'data' => null
                ]
            );
        }

        $currentUser = User::where('id', $data['user_id'])
            ->first();

        if (!$currentUser) {
            return $socket->emit($data['user_id'] . '-walletPaymentResponse',
                [
                    'result' => 'error',
                    'message' => 'User Not Found',
                    'data' => null
                ]
            );
        }

        if (!isset($data['booking_id'])) {
            return $socket->emit($data['user_id'] . '-walletPaymentResponse', [
                'result' => 'error',
                'message' => 'Booking ID is a Required Field',
                'data' => null
            ]);
        }

        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $currentUser->id)->where('driver_status', 3)
            ->where('ride_status', '=', 1)
            ->first();

        if (!$findBooking) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }


        if ($findBooking->payment_type == 'cash') {
            return $socket->emit($data['user_id'] . '-walletPaymentResponse', [
                'result' => 'error',
                'message' => 'Your ride payment type is cash you cannot pay from wallet',
                'data' => null
            ]);
        }


        if ($data['payment_type'] == 'cash') {
            $findBooking->payment_type = 'cash_wallet';
            $findBooking->save();
        } elseif ($data['payment_type'] == 'wallet') {

            $wallet_balance = $this->passengerWalletBalance($findBooking->passenger_id);

            $totalFare = $findBooking->actual_fare;

            $totalPaidFare = $findBooking->bookingDetail->passenger_wallet_paid;

            $unPaidFare = $totalFare - $findBooking->bookingDetail->passenger_wallet_paid;

            $fare = 0;
            $leftBalance = 0;
            if ($unPaidFare > 0) {
                if ($unPaidFare > $wallet_balance) {
                    $fare = $unPaidFare - $wallet_balance;
                } elseif ($wallet_balance > $unPaidFare) {
                    $fare = $wallet_balance - $unPaidFare;
                }

            }


            $findBooking->bookingDetail->update(['passenger_wallet_paid' => $fare + $unPaidFare]);
        }

        $bookingResponse = $this->driverBookingResponse($findBooking);

        $driverSocketId = $findBooking->driver_id;

//        if ($driverSocketId) {
        $socket->emit($driverSocketId . '-walletPaymentResponse', [
            'result' => 'success',
            'message' => 'Fare Updated',
            'data' => (object)$bookingResponse
        ]);
//        }
        //passenger socket id
        return $socket->emit($data['user_id'] . '-walletPaymentResponse', [
            'result' => 'success',
            'message' => 'Fare Updated',
            'data' => (object)$bookingResponse
        ]);


    }


    public function getDriverLastLocation($data, $io, $socket)
    {

        if (!isset($data['user_id'])) {
            return $socket->emit('driverLastLocation', [
                    'result' => 'error',
                    'message' => 'User ID is a Required Field',
                    'data' => null
                ]
            );


        }

        $currentUser = User::where('id', $data['user_id'])->first();

        if (!$currentUser) {
            return $socket->emit($data['user_id'] . '-driverLastLocation',
                [
                    'result' => 'error',
                    'message' => 'User Not Found',
                    'data' => null
                ]
            );
        }

        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('passenger_id', $currentUser->id)
            ->whereNotNull('driver_id')->first();

        if (!$findBooking) {
            return $socket->emit($data['user_id'] . '-driverLastLocation',
                [
                    'result' => 'error',
                    'message' => 'Booking Not Found',
                    'data' => null
                ]
            );
        }

        $getCoordinate = DriversCoordinate::where('driver_id', $findBooking->driver_id)
            ->first();

        if (!$getCoordinate) {
            return $socket->emit($data['user_id'] . '-driverLastLocation',
                [
                    'result' => 'error',
                    'message' => 'Driver Location Not Found',
                    'data' => null
                ]
            );
        }

        return $socket->emit($data['user_id'] . '-driverLastLocation',
            [
                'result' => 'success',
                'message' => 'Driver Last Coordinate',
                'data' => [
                    "latitude" => $getCoordinate->latitude,
                    "longitude" => $getCoordinate->longitude,
                    "city" => $getCoordinate->city,
                    "area_name" => $getCoordinate->area_name,
                    "bearing" => $getCoordinate->bearing
                ],

            ]);

    }

    public function driverCancelBooking($data, $io, $socket)
    {
        if (!isset($data['user_id'])) {
            return $socket->emit('driverLastLocation', [
                    'result' => 'error',
                    'message' => 'User ID is a Required Field',
                    'data' => null
                ]
            );
        }

        $currentUser = User::where('id', $data['user_id'])->first();

        if (!$currentUser) {
            return $socket->emit($data['user_id'] . '-driverLastLocation',
                [
                    'result' => 'error',
                    'message' => 'User Not Found',
                    'data' => null
                ]
            );
        }

        return $this->driverCancelService->cancelService($data, $socket, $io, $currentUser);

    }

    public function acceptRejectServiceRide($data, $socket, $io)
    {
        if (!isset($data['booking_id'])) {
            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Booking ID is a Required Field',
                    'data' => null
                ]
            );
        }

        if (!isset($data['driver_action'])) {
            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Driver Action is a Required Field',
                    'data' => null
                ]
            );
        }

        return $this->acceptRejectServiceRide->rideAcceptReject($data, $socket, $io);

    }

}
