<?php


namespace App\Services\API\Socket;


use App\Models\Booking;
use App\Models\P2PBookingTracking;
use App\Models\User;
use App\Traits\BookingResponseTrait;
use App\Traits\SendFirebaseNotificationTrait;

class DriverStatusService
{
    use SendFirebaseNotificationTrait, BookingResponseTrait;

    public function reachToPickUp($data, $socket, $io, $user)
    {
        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)->where('driver_status', 0)
            ->where('ride_status', '=', 1)
            ->first();

        if (!$findBooking) {
            $socket->emit('error', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }


        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->save();

        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('error', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }


        $passengerSocketId = $findBooking->passenger->socket_id;
        $passengerFCMToken = $findBooking->passenger->fcm_token;

        $bookingResponse = $this->driverBookingResponse($findBooking);

        //driver reach location notification
        $notification_type = 2;
        if ($user->fcm_token) {
            $fcmToken = ['fcm_token' => $passengerFCMToken];
            $sendFCMNotification = $this->duringRideNotifications($fcmToken, $bookingResponse, $notification_type);
        }

        $io->to($passengerFCMToken)->emit('arrivedAtPickUp', [
            'result' => 'success',
            'message' => 'Driver Arrived At Your Pick Location',
            'data' => $bookingResponse
        ]);

        $io->to($data['socket_id'])->emit('arrivedAtPickUp', [
            'result' => 'success',
            'message' => 'Status Save Successfully',
            'data' => $bookingResponse
        ]);


    }

    public function startRide($data, $socket, $io, $user)
    {

        if (!isset($data['waiting_time'])) {
            $io->to($data['socket_id'])->emit('error', [
                'result' => 'error',
                'message' => 'Waiting Time is a required field',
                'data' => null
            ]);
        }

        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)->where('driver_status', 1)
            ->where('ride_status', '=', 1)
            ->first();

        if (!$findBooking) {
            $socket->emit('error', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }

        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->save();
        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('error', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }

        try {
            $findBooking->bookingDetail->driver_waiting_time = $data['waiting_time'];
            $findBooking->bookingDetail->save();
        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('error', [
                'result' => 'error',
                'message' => 'Error in Saving Driver Waiting Time: ' . $e,
                'data' => null
            ]);
        }


        $passengerSocketId = $findBooking->passenger->socket_id;
        $passengerFCMToken = $findBooking->passenger->fcm_token;

        $bookingResponse = $this->driverBookingResponse($findBooking);

        //ride start notification
        $notification_type = 3;
        if ($user->fcm_token) {
            $fcmToken = ['fcm_token' => $passengerFCMToken];
            $sendFCMNotification = $this->duringRideNotifications($fcmToken, $bookingResponse, $notification_type);
        }

        $io->to($passengerFCMToken)->emit('arrivedAtPickUp', [
            'result' => 'success',
            'message' => 'Driver Arrived At Your Pick Location',
            'data' => $bookingResponse
        ]);

        $io->to($data['socket_id'])->emit('arrivedAtPickUp', [
            'result' => 'success',
            'message' => 'Status Save Successfully',
            'data' => $bookingResponse
        ]);

    }

    public function completeRide($data, $socket, $io, $user)
    {
        if (!isset($data['total_distance'])) {
            $io->to($data['socket_id'])->emit('error', [
                'result' => 'error',
                'message' => 'Total Distance is a required field',
                'data' => null
            ]);
        }

        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)->where('driver_status', 2)
            ->where('ride_status', '=', 1)
            ->first();

        if (!$findBooking) {
            $socket->emit('error', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }


        $totalDistance = 0;

        $mobileDistance = $data['total_distance'];

        $p2pDistance = P2PBookingTracking::where('booking_id', $findBooking->id)->sum('distance');

        if ($p2pDistance < $mobileDistance) {
            $totalDistance = $mobileDistance;
        } else {
            $totalDistance = $p2pDistance;
        }


        $fare = $this->calculateFare($findBooking);

        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->total_distance = $totalDistance;
            $findBooking->actual_fare = $fare['totalFare'];
            $findBooking->save();
        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('error', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }


    }


    public function calculateFare($findBooking)
    {
        $calculateWaitingTime = $totalTime = 0;

        $driverWaitingTime = $findBooking->bookingDetail->driver_waiting_time;
        $allowedTime = $findBooking->bookingDetail->allowed_waiting_time;

        if ($allowedTime > $driverWaitingTime) {
            $totalTime = $allowedTime - $driverWaitingTime;
        } else {
            $totalTime = $driverWaitingTime - $allowedTime;
        }

        $waitingTimePrice = $findBooking->bookingDetail->waiting_price_per_min;

        $calculateWaitingTime = (float)($waitingTimePrice * $totalTime);

        $calculateDistanceFare = $findBooking->total_distance * $findBooking->bookingDetail->vehicle_per_km_rate;

        $totalFare = (float)($calculateWaitingTime + $calculateDistanceFare);

        //peak factor calculation here

        $fare = [
            'total_fare' => $totalFare,
            'waiting_time_fare' => $calculateWaitingTime,
            'distance_fare' => $calculateDistanceFare
        ];

        return $fare;

    }


}
