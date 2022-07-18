<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DriversCoordinate;
use App\Models\P2PBookingTracking;
use App\Models\User;
use App\Services\API\Socket\DriverStatusService;
use App\Services\API\Socket\RideAcceptRejectService;
use App\Traits\BookingResponseTrait;
use App\Traits\CreateUserWalletTrait;
use App\Traits\FindDistanceTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SocketController extends Controller
{

    use FindDistanceTraits, BookingResponseTrait;

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


            if (isset($data['booking_id']) && $data['booking_id'] != 0) {

                $checkForBooking = Booking::where('driver_id', $data['user_id'])
                    ->where('id', $data['booking_id'])
                    ->where('ride_status', 1)->first();


                if ($checkForBooking) {

                    $calculateDistance = $this->getDistance($data['latitude'], $data['longitude'],
                        $driver->latitude,
                        $driver->longitude);

                    $distanceInKm = str_replace(',', '', str_replace('km', '', $calculateDistance['text']));
                    $distanceInKm = str_replace(',', '', str_replace('m', '', $distanceInKm));


                    P2PBookingTracking::create(['booking_id' => $checkForBooking->id,
                        'driver_id' => $checkForBooking->driver_id,
                        'latitude' => $data['latitude'], 'longitude' => $data['longitude'],
                        'distance' => trim($distanceInKm),
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
                    $socket->emit($data['user_id'] . '-driverCoordinate', [
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


                    $socket->emit($data['user_id'] . '-driverCoordinate',
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
                }

            }
            else {
                if (!isset($driver)) {
                    $driver = new DriversCoordinate;
                }
                //saving driver current lat and lng
                $driver->latitude = $data['latitude'];
                $driver->longitude = $data['longitude'];
                $driver->area_name = $data['area_name'];
                $driver->city = $data['city'];
                $driver->bearing = $data['bearing'];
                $driver->save();
            }


            $socket->emit($data['user_id'] . '-driverCoordinate',
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

            return $socket->to($data['socket_id'])->emit('driverCoordinate',
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


        if($data['payment_type'] == 'cash')
        {
            $findBooking->payment_type = 'cash_wallet';
            $findBooking->save();
        }
        elseif($data['payment_type'] == 'wallet') {

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
}
