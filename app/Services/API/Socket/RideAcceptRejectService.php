<?php


namespace App\Services\API\Socket;


use App\Models\AssignBookingDriver;
use App\Models\Booking;
use App\Models\User;
use App\Models\VoucherCodePassenger;
use App\Traits\BookingResponseTrait;
use App\Traits\SendFirebaseNotificationTrait;

class RideAcceptRejectService
{

    use BookingResponseTrait;
    use SendFirebaseNotificationTrait;

    public function rideAcceptReject($data, $socket, $io)
    {
        $gettingCurrentUser = User::find($data['user_id']);

        if (!$gettingCurrentUser) {
            return $socket->emit($data['user_id'].'-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'User Not Found',
                    'data' => null
                ]
            );
        }

        $findBooking = Booking::find($data['booking_id']);

        if (!$findBooking) {
            return $socket->emit($data['user_id'].'-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Booking Not Found',
                    'data' => null
                ]
            );
        }

        try {
            $findDriverRecord = AssignBookingDriver::where('booking_id', $findBooking->id)
                ->where('driver_id', $gettingCurrentUser->id)
                ->whereNull('status')->first();

            if ($findDriverRecord) {

                // 0 = reject, 1 = accept, 2= ignore
                $findDriverRecord->status = $data['driver_action'];


                $findDriverRecord->save();


            }
        } catch (\Exception $e) {
            return $socket->emit($data['user_id'].'-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => "Unable to Save Driver Status" . $e,
                    'data' => null
                ]
            );
        }

        if ($data['driver_action'] == 1) {
            try {
                $findBooking->vehicle_id = $gettingCurrentUser->driver->vehicle_id;
                $findBooking->driver_id = $gettingCurrentUser->id;
                $findBooking->ride_status = 1;


                if ($findBooking->booking_type == 'book_now') {
                    $gettingCurrentUser->driverCoordinate->update(['status' => 2]);
                    $findBooking->driver_status = 0;
                } else {
                    $gettingCurrentUser->driverCoordinate->update(['status' => 1]);
                }

                // update voucher
                if($findBooking->bookingDetail->is_voucher == 1)
                {
                    $voucher_detail = json_decode($findBooking->bookingDetail->voucher_detail);

                    $voucherId = $voucher_detail->voucher_id;

                    $find =  VoucherCodePassenger::where('passenger_id',$findBooking->passenger_id)
                        ->where('voucher_code_id',$voucherId)->update(['is_used'=>1]);

                }

                $findBooking->save();
            }
            catch (\Exception $e) {
                return $socket->emit($data['user_id'].'-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "Error in Update Booking Record and Driver Status: " . $e,
                        'data' => null
                    ]
                );
            }


//            $passengerSocketID = $findBooking->passenger->socket_id;


//            if ($passengerSocketID) {
                 $socket->emit($findBooking->passenger_id.'-finalRideStatus',
                    [
                        'result' => 'success',
                        'message' => "Driver has Accepted Your Ride Request",
                        'data' => $this->driverBookingResponse($findBooking)
                    ]
                );
//            }




            $socket->emit($data['user_id'].'-finalRideStatus',
                [
                    'result' => 'success',
                    'message' => "You have Accepted Passenger's Ride Request",
                    'data' => $this->driverBookingResponse($findBooking)
                ]
            );

            $passengerFCMToken = $findBooking->passenger->fcm_token;

            if ($passengerFCMToken) {
                $notification_type = 1;

                $sendNotificationToPassenger = $this->rideAcceptNotification($passengerFCMToken, $findBooking, $notification_type);
            }

            try {
                $deleteUnResponsiveDriver = AssignBookingDriver::where('booking_id', $findBooking->id)
                    ->whereNull('status')->delete();
            }
            catch (\Exception $e) {
                return $socket->emit($data['user_id'].'-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "Unable to delete other Driver Records: " . $e,
                        'data' => null
                    ]
                );
            }

        }
        else {


            $gettingCurrentUser->driverCoordinate->update(['status' => 1]);

            $findNextDriver = AssignBookingDriver::where('booking_id', $findBooking->id)
                ->whereNull('status')
                ->where('driver_id', '!=', $gettingCurrentUser->id)
                ->orderBy('id', 'asc')
                ->first();


            if ($findNextDriver) {
                $driverRecord = User::find($findNextDriver->driver_id);

                $driverRecord->driverCoordinate->update(['status' => 3]);

                $booking = $this->driverBookingResponse($findBooking);

                $notification_type = 11;
                if ($driverRecord->fcm_token) {
                    $fcmToken = ['fcm_token' => $driverRecord->fcm_token];
                    $sendFCMNotification = $this->rideRequestNotification($fcmToken, $booking, $notification_type);
                }

                $driverSocketId = $driverRecord->id;

                if ($driverSocketId) {
                     $socket->emit($driverSocketId.'-finalRideStatus',
                        [
                            'result' => 'success',
                            'message' => "New Ride Request",
                            'data' => (object)$booking
                        ]
                    );
                }

                $socket->emit($data['user_id'].'-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "You have Rejected/Ignored the Ride Request",
                        'data' => null
                    ]
                );
            }
            else {
                $passengerID = $findBooking->passenger_id;
                $booking = $this->driverBookingResponse($findBooking);


                if ($data['driver_action'] == 2) {
                     $socket->emit($passengerID.'-finalRideStatus',
                        [
                            'result' => 'error',
                            'message' => "No one has accepted your ride request",
                            'data' => (object)$booking
                        ]
                    );

                    return  $socket->emit($data['user_id'].'-finalRideStatus',
                        [
                            'result' => 'error',
                            'message' => "You have ignored ride request",
                            'data' => null
                        ]
                    );
                }
                elseif ($data['driver_action'] == 0) {
                    $socket->emit($passengerID.'-finalRideStatus',
                        [
                            'result' => 'error',
                            'message' => "No one has accepted your ride request",
                            'data' => (object)$booking
                        ]
                    );


                    return  $socket->emit($data['user_id'].'-finalRideStatus',
                        [
                            'result' => 'error',
                            'message' => "You have rejected ride request",
                            'data' => null
                        ]
                    );
                }



            }
        }


    }
}
