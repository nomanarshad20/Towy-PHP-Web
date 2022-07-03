<?php


namespace App\Services\API\Socket;


use App\Models\Booking;
use App\Models\P2PBookingTracking;
use App\Models\User;
use App\Traits\BookingResponseTrait;
use App\Traits\SendFirebaseNotificationTrait;
use Carbon\Carbon;

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
            $socket->emit('driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }


        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->save();
            $calculatePickUpTime = Carbon::parse($findBooking->updated_at);
            $currentTime = Carbon::now();
            $findBooking->bookingDetail->update([
                'ride_pickup_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'total_minutes_to_reach_pick_up_point' => $currentTime->diffInMinutes($calculatePickUpTime)
            ]);


        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('driverStatus', [
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
            $title = 'Driver Arrived';
            $message = 'Driver Reached to Your PickUp Location';
            $sendFCMNotification = $this->duringRideNotifications($fcmToken, $bookingResponse, $notification_type, $title, $message);
        }

        $io->to($passengerSocketId)->emit('driverStatus', [
            'result' => 'success',
            'message' => 'Driver Arrived At Your Pick Location',
            'data' => $bookingResponse
        ]);

        $io->to($data['socket_id'])->emit('driverStatus', [
            'result' => 'success',
            'message' => 'Status Save Successfully',
            'data' => $bookingResponse
        ]);


    }

    public function startRide($data, $socket, $io, $user)
    {

//        if (!isset($data['waiting_time'])) {
//            $io->to($data['socket_id'])->emit('driverStatus', [
//                'result' => 'error',
//                'message' => 'Waiting Time is a required field',
//                'data' => null
//            ]);
//        }

        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)->where('driver_status', 1)
            ->where('ride_status', '=', 1)
            ->first();

        if (!$findBooking) {
            $socket->emit('driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }

        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->save();

        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('driverStatus', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }

        try {

            $pick_up_time = Carbon::parse($findBooking->bookingDetail->ride_pickup_time);
            $now = Carbon::now();

            $findBooking->bookingDetail->ride_start_time = Carbon::now()->parse('Y-m-d H:i:s');
            $findBooking->bookingDetail->driver_waiting_time = $now->diffInMinutes($pick_up_time);

            $findBooking->bookingDetail->save();
        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('driverStatus', [
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
            $title = 'Ride Started';
            $message = 'Fasten Your Seat Belt Ride Is Started';
            $sendFCMNotification = $this->duringRideNotifications($fcmToken, $bookingResponse, $notification_type, $title, $message);
        }

        $io->to($passengerFCMToken)->emit('driverStatus', [
            'result' => 'success',
            'message' => 'Driver Arrived At Your Pick Location',
            'data' => $bookingResponse
        ]);

        $io->to($data['socket_id'])->emit('driverStatus', [
            'result' => 'success',
            'message' => 'Status Save Successfully',
            'data' => $bookingResponse
        ]);

    }

    public function completeRide($data, $socket, $io, $user)
    {
        if (!isset($data['total_distance'])) {
            $io->to($data['socket_id'])->emit('driverStatus', [
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
            $socket->emit('driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }


        $totalDistance = 0;

        $mobileDistance = $data['total_distance'];

        $p2pDistance = P2PBookingTracking::where('booking_id', $findBooking->id)->sum('distance');
        $p2pRideDistance = P2PBookingTracking::where('booking_id', $findBooking->id)->where('driver_status', '!=', 0)->sum('distance');
        $p2pInitialDistance = P2PBookingTracking::where('booking_id', $findBooking->id)->where('driver_status', 0)->sum('distance');


        if ($mobileDistance > $p2pDistance) {

            $diffInDistance = $mobileDistance - $p2pDistance;

            if ($diffInDistance >= 10) {
                $totalDistance = $mobileDistance;
            }
            else{
                $googleDistance = $totalDistance + $p2pInitialDistance;

                $differenceDistance =  $mobileDistance -  $googleDistance;

                if($differenceDistance > 0)
                {
                    $totalDistance = $mobileDistance;
                }
                else{
                    $totalDistance = $googleDistance;
                }

            }

        }

        $fare = $this->calculateFare($findBooking,$p2pDistance,$p2pInitialDistance,$p2pRideDistance,$totalDistance);

        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->total_calculated_distance = $totalDistance;
//            $findBooking->actual_fare = $fare['totalFare'];
            $findBooking->save();
        } catch (\Exception $e) {
            $io->to($user->socket_id)->emit('driverStatus', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }

        $passengerSocketId = $findBooking->passenger->socket_id;
        $passengerFCMToken = $findBooking->passenger->fcm_token;

        $bookingResponse = $this->driverBookingResponse($findBooking);

        //ride start notification
        $notification_type = 4;
        if ($user->fcm_token) {
            $fcmToken = ['fcm_token' => $passengerFCMToken];
            $title = 'Ride is Completed';
            $message = 'Congratulations! You have Reached Your Destination.Hope you have enjoyed our services';
            $sendFCMNotification = $this->duringRideNotifications($fcmToken, $bookingResponse, $notification_type, $title, $message);
        }

        $io->to($passengerFCMToken)->emit('driverStatus', [
            'result' => 'success',
            'message' => 'Driver Arrived At Your Pick Location',
            'data' => $bookingResponse
        ]);

        $io->to($data['socket_id'])->emit('driverStatus', [
            'result' => 'success',
            'message' => 'Status Save Successfully',
            'data' => $bookingResponse
        ]);


    }


    public function calculateFare($findBooking,$p2pDistance,$p2pInitialDistance,$p2pRideDistance,$totalDistance)
    {
        $calculateWaitingTime = $totalTime = 0;

        //calculate Waiting Time Fare
        $driverWaitingTime = $findBooking->bookingDetail->driver_waiting_time;
        $allowedTime = $findBooking->bookingDetail->allowed_waiting_time;

        if ($allowedTime > $driverWaitingTime) {
            $totalTime = $allowedTime - $driverWaitingTime;
        } else {
            $totalTime = $driverWaitingTime - $allowedTime;
        }

        $waitingTimePrice = $findBooking->bookingDetail->waiting_price_per_min;
        $calculateWaitingTime = (float)($waitingTimePrice * $totalTime);

        //calculate Pick Up Fare
        $pickUpTimeCalculation = $findBooking->bookingDetail->total_minutes_to_reach_pick_up_point * $findBooking->bookingDetail->initial_time_rate;
        $pickUpDistanceCalculation = $findBooking->bookingDetail->p2pInitialDistance * $findBooking->bookingDetail->initial_distance_rate;
        $totalInitialFare = $pickUpTimeCalculation + $pickUpDistanceCalculation;

        //calculate Ride Fare
        $totalRideTime =  $findBooking->bookingDetail->ride_start_time + $findBooking->bookingDetail->ride_end_time;
        $totalRideTimeFare =  $totalRideTime * $findBooking->bookingDetail->vehicle_per_min_rate;
        $totalDistanceFare = $totalDistance * $findBooking->bookingDetail->vehicle_per_km_rate;


        $totalFare = (float) ($totalRideTimeFare + $totalDistanceFare + $totalInitialFare + $pickUpDistanceCalculation + $calculateWaitingTime + $findBooking->bookingDetail->vehicle_tax);

        //peak factor calculation here

        $checkPassengerWallet = $findBooking->passenger->wallet('Passenger-Wallet');
        $wallet_balance = $checkPassengerWallet->balance;

        if ($findBooking->bookingDetail->peak_factor_applied == 1) {
            $totalFare = $totalFare * $findBooking->bookingDetail->peak_factor_rate;
        }

        if ($wallet_balance != 0 && $wallet_balance < 0) {
            $totalFare = $totalFare + $wallet_balance;
        }

        $fare = [
            'total_fare' => $totalFare,
            'waiting_time_fare' => $calculateWaitingTime,
            'distance_fare' => $totalDistanceFare + $totalRideTimeFare
        ];

        return $fare;

    }


}
