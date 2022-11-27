<?php


namespace App\Services\API\Socket;


use App\Http\Controllers\API\Driver\DriverInformationController;
use App\Models\AssignBookingDriver;
use App\Models\Booking;
use App\Models\BookingPoint;
use App\Models\DriversCoordinate;
use App\Models\User;
use App\Models\VoucherCodePassenger;
use App\Traits\BookingResponseTrait;
use App\Traits\SendFirebaseNotificationTrait;
use App\Traits\ServiceBookingTrait;
use Carbon\Carbon;
use Psy\Input\CodeArgument;

class AcceptRejectServiceRide
{

    use ServiceBookingTrait;
    use SendFirebaseNotificationTrait;

    public function rideAcceptReject($data, $socket, $io)
    {
        $gettingCurrentUser = User::find($data['user_id']);

        if (!$gettingCurrentUser) {
            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'User Not Found',
                    'data' => null
                ]
            );
        }

        $findBooking = Booking::find($data['booking_id']);

        if (!$findBooking) {
            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => 'Booking Not Found',
                    'data' => null
                ]
            );
        }

        if ($findBooking->ride_status == 2) {

//            $findBooking->driver->driverCoordinate->update(['status' => 1]);
            //send Notification to Driver
            $driverFCM = $gettingCurrentUser->fcm_token;

            $driver = DriversCoordinate::where('driver_id', $gettingCurrentUser->id)
                ->update(['status' => 1]);

            $booking = $this->bookingResponse($findBooking);

            if ($driverFCM) {

                $notificationType = 15;
                $title = 'Passenger Cancel The Ride';
                $message = 'Passenger has Cancelled his ride';

                $notificationsData = [
                    'result' => 'error',
                    'message' => "Passenger has Cancelled his ride",
                    'data' => null
                ];

                $sendNotification = $this->cancelRide($driverFCM, $notificationType, $title, $message, $notificationsData);
            }


            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => "Ride is Cancel By Passenger",
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
            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'error',
                    'message' => "Unable to Save Driver Status" . $e,
                    'data' => null
                ]
            );
        }

        //accept scenario
        if ($data['driver_action'] == 1) {
            try {
                $findBooking->vehicle_id = $gettingCurrentUser->driver->vehicle_id;
                $findBooking->driver_id = $gettingCurrentUser->id;
                if (isset($gettingCurrentUser->franchise_id))
                    $findBooking->franchise_id = $gettingCurrentUser->franchise_id;
                $findBooking->ride_status = 1;

                $gettingCurrentUser->driverCoordinate->update(['status' => 2]);
                $findBooking->driver_status = 0;


                // update voucher
                if ($findBooking->bookingDetail->is_voucher == 1) {
                    $voucher_detail = json_decode($findBooking->bookingDetail->voucher_detail);

                    $voucherId = $voucher_detail->voucher_id;

                    $find = VoucherCodePassenger::where('passenger_id', $findBooking->passenger_id)
                        ->where('voucher_code_id', $voucherId)->update(['is_used' => 1]);

                }

                $findBooking->save();
            } catch (\Exception $e) {
                return $socket->emit($data['user_id'] . '-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "Error in Update Booking Record and Driver Status: " . $e,
                        'data' => null
                    ]
                );
            }

            //save drive coordinate from here he accepts ride
            try {
                $getCoordinate = DriversCoordinate::where('driver_id', $data['user_id'])->first();

                if ($getCoordinate) {
                    BookingPoint::create([
                        'booking_id' => $findBooking->id,
                        'lat' => $getCoordinate->latitude,
                        'lng' => $getCoordinate->longitude,
                        'type' => 0
                    ]);
                }

            } catch (\Exception $e) {
                return $socket->emit($data['user_id'] . '-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "Error in saving Driver Starting Point: " . $e,
                        'data' => null
                    ]
                );
            }

            $passengerFCMToken = $findBooking->passenger->fcm_token;

            if ($passengerFCMToken) {
                $notification_type = 1;
                $bookingFinalObject = $this->bookingResponse($findBooking);

                $sendNotificationToPassenger = $this->rideAcceptNotification($passengerFCMToken, $bookingFinalObject, $notification_type);
            }

            $driverFCMToken = $findBooking->driver->fcm_token;

            if ($driverFCMToken) {
                $notification_type = 15;
                $title = 'Accept Ride';
                $message = 'You have accept the ride request by passenger';
                $bookingFinalObject = $this->bookingResponse($findBooking);

                $sendNotificationToDriver = $this->driverRideAcceptRejectNotification($driverFCMToken, $bookingFinalObject, $notification_type, $title, $message);
            }

            //delete other drivers where request not receive
            try {
                $deleteUnResponsiveDriver = AssignBookingDriver::where('booking_id', $findBooking->id)
                    ->whereNull('status')->delete();
            } catch (\Exception $e) {
                return $socket->emit($data['user_id'] . '-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "Unable to delete other Driver Records: " . $e,
                        'data' => null
                    ]
                );
            }


            $socket->emit($findBooking->passenger_id . '-finalRideStatus',
                [
                    'result' => 'success',
                    'message' => "Driver has Accepted Your Ride Request",
                    'data' => $this->bookingResponse($findBooking)
                ]
            );


            return $socket->emit($data['user_id'] . '-finalRideStatus',
                [
                    'result' => 'success',
                    'message' => "You have Accepted Passenger's Ride Request",
                    'data' => $this->bookingResponse($findBooking)
                ]
            );


        } else {

            $gettingCurrentUser->driverCoordinate->update(['status' => 1]);

//            $findNextDriver = AssignBookingDriver::where('booking_id', $findBooking->id)
//                ->whereNull('status')
//                ->where('driver_id', '!=', $gettingCurrentUser->id)
//                ->orderBy('id', 'asc')
//                ->first();

            $passengerID = $findBooking->passenger_id;

            $findBooking->ride_status = 6;
            $findBooking->save();

            $booking = $this->bookingResponse($findBooking);


            if ($data['driver_action'] == 2) {

                $driverFCMToken = $gettingCurrentUser->fcm_token;

                if ($driverFCMToken) {
                    $notification_type = 15;
                    $title = 'Ignore Ride Request';
                    $message = 'You have ignored the ride request by passenger';

                    $sendNotificationToDriver = $this->driverRideAcceptRejectNotification($driverFCMToken, $booking, $notification_type, $title, $message);
                }

                $socket->emit($passengerID . '-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "No one has accepted your ride request",
                        'data' => (object)$booking
                    ]
                );

                return $socket->emit($data['user_id'] . '-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "You have ignored ride request",
                        'data' => null
                    ]
                );
            } elseif ($data['driver_action'] == 0) {

                $driverFCMToken = $gettingCurrentUser->fcm_token;

                if ($driverFCMToken) {
                    $notification_type = 15;
                    $title = 'Reject Ride Request';
                    $message = 'You have reject the ride request by passenger';

                    $sendNotificationToDriver = $this->driverRideAcceptRejectNotification($driverFCMToken, $booking, $notification_type, $title, $message);
                }


                $socket->emit($passengerID . '-finalRideStatus',
                    [
                        'result' => 'error',
                        'message' => "No one has accepted your ride request",
                        'data' => (object)$booking
                    ]
                );


                return $socket->emit($data['user_id'] . '-finalRideStatus',
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
