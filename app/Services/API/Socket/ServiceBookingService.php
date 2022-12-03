<?php


namespace App\Services\API\Socket;


use App\Models\Booking;
use App\Models\BookingPoint;
use App\Models\BookingService;
use App\Models\P2PBookingTracking;
use App\Models\VoucherCodePassenger;
use App\Services\API\Passenger\StripeService;
use App\Services\API\UpdateWalletService;
use App\Traits\BookingResponseTrait;
use App\Traits\CreateUserWalletTrait;
use App\Traits\FindDistanceTraits;
use App\Traits\SendFirebaseNotificationTrait;
use App\Traits\ServiceBookingTrait;
use Carbon\Carbon;

class ServiceBookingService
{
    use SendFirebaseNotificationTrait, ServiceBookingTrait ,BookingResponseTrait, CreateUserWalletTrait, FindDistanceTraits;

    public $walletService;
    public $stripeService;

    public function __construct(UpdateWalletService $walletService, StripeService $stripeService)
    {
        $this->walletService = $walletService;
        $this->stripeService = $stripeService;
    }

    public function reachToPickUp($data, $socket, $io, $user)
    {
//        DB::beginTransaction();
        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)
            ->where('driver_status', 0)
            ->where('ride_status', 1)
            ->first();

        if (!$findBooking) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }


        try {
//            $findBooking->driver_status = $data['driver_status'];
            $calculatePickUpTime = Carbon::parse($findBooking->updated_at);

            $findBooking->update(['driver_status' => $data['driver_status']]);


//            $calculatePickUpTime = Carbon::parse($findBooking->updated_at);
            $currentTime = Carbon::now();
            $findBooking->bookingDetail->update([
                'ride_pickup_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'total_minutes_to_reach_pick_up_point' => $currentTime->diffInMinutes($calculatePickUpTime)
            ]);

            $findBooking->push();


        } catch (\Exception $e) {
//            DB::rollBack();
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }
        $passengerSocketId = $findBooking->passenger_id;
        $passengerFCMToken = $findBooking->passenger->fcm_token;
        $bookingResponse = $this->bookingResponse($findBooking);

//        DB::commit();

        //driver reach location notification
        $notification_type = 2;
        if ($passengerFCMToken) {
//            $fcmToken = ['fcm_token' => $passengerFCMToken];
            $title = 'Service Provider Arrived';
            $message = 'Service Provider Reached to Your PickUp Location';
            $sendFCMNotification = $this->duringRideNotifications($passengerFCMToken, $bookingResponse, $notification_type, $title, $message);
        }



//        dd('here');
//        if ($passengerSocketId) {
        $socket->emit($passengerSocketId . '-driverStatus', [
            'result' => 'success',
            'message' => 'Service Provider Arrived At Your Pick Up Location',
            'data' => (object)$bookingResponse
        ]);

//        }


        return $socket->emit($data['user_id'] . '-driverStatus', [
            'result' => 'success',
            'message' => 'Status Save Successfully',
            'data' => (object)$bookingResponse
        ]);


    }

    public function startRide($data, $socket, $io, $user)
    {

        if (!isset($data['lat'])) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Latitude Field is Required',
                'data' => null
            ]);
        }

        if (!isset($data['lng'])) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Longitude Field is Required',
                'data' => null
            ]);
        }

//        if (!isset($data['waiting_time'])) {
//            $io->to($data['socket_id'])->emit('driverStatus', [
//                'result' => 'error',
//                'message' => 'Waiting Time is a required field',
//                'data' => null
//            ]);
//        }
//        DB::beginTransaction();
        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)->where('driver_status', 1)
            ->where('ride_status', '=', 1)
            ->first();

        if (!$findBooking) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }

        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->save();

        } catch (\Exception $e) {
//            DB::rollBack();
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }

        try {

            $pick_up_time = Carbon::parse($findBooking->bookingDetail->ride_pickup_time);
            $now = Carbon::now();

            $findBooking->bookingDetail->ride_start_time = Carbon::now()->format('Y-m-d H:i:s');
            $findBooking->bookingDetail->driver_waiting_time = $now->diffInMinutes($pick_up_time);

            $findBooking->bookingDetail->save();

            $findBooking->push();
        } catch (\Exception $e) {
//            DB::rollBack();
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error in Saving Driver Waiting Time: ' . $e,
                'data' => null
            ]);
        }

        try {
            BookingPoint::create(['booking_id' => $findBooking->id,
                'lat' => $data['lat'], 'lng' => $data['lng'], 'type' => 1]);
        } catch (\Exception $e) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error in Saving Driver Waiting Time: ' . $e,
                'data' => null
            ]);
        }


        $passengerSocketId = $findBooking->passenger_id;
        $passengerFCMToken = $findBooking->passenger->fcm_token;

        $bookingResponse = $this->bookingResponse($findBooking);

        //ride start notification
        $notification_type = 3;
        if ($passengerFCMToken) {
//            $fcmToken = ['fcm_token' => $passengerFCMToken];
            $title = 'Service Started';
            $message = 'Service Time is started';
            $sendFCMNotification = $this->duringRideNotifications($passengerFCMToken, $bookingResponse, $notification_type, $title, $message);
        }

//        DB::commit();

//        if ($passengerSocketId) {
        $socket->emit($passengerSocketId . '-driverStatus', [
            'result' => 'success',
            'message' => 'Service Time is started and Service Provider is fixing issue in your automobile',
            'data' => $bookingResponse
        ]);
//        }


        return $socket->emit($data['user_id'] . '-driverStatus', [
            'result' => 'success',
            'message' => 'Status Save Successfully',
            'data' => $bookingResponse
        ]);

    }

    public function completeRide($data, $socket, $io, $user)
    {
//        DB::beginTransaction();

//        if (!isset($data['lat'])) {
//            return $socket->emit($data['user_id'] . '-driverStatus', [
//                'result' => 'error',
//                'message' => 'Latitude is a required field',
//                'data' => null
//            ]);
//        }
//
//        if (!isset($data['lng'])) {
//            return $socket->emit($data['user_id'] . '-driverStatus', [
//                'result' => 'error',
//                'message' => 'Longitude is a required field',
//                'data' => null
//            ]);
//        }

//        if (!isset($data['total_distance'])) {
//            return $socket->emit($data['user_id'] . '-driverStatus', [
//                'result' => 'error',
//                'message' => 'Total Distance is a required field',
//                'data' => null
//            ]);
//        }
//
//        if (!isset($data['mobile_final_distance'])) {
//            return $socket->emit($data['user_id'] . '-driverStatus', [
//                'result' => 'error',
//                'message' => 'Mobile Final Distance is a required field',
//                'data' => null
//            ]);
//        }
//
//        if (!isset($data['mobile_initial_distance'])) {
//            return $socket->emit($data['user_id'] . '-driverStatus', [
//                'result' => 'error',
//                'message' => 'Mobile Initial Distance is a required field',
//                'data' => null
//            ]);
//        }

        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)->where('driver_status', 2)
            ->where('ride_status', 1)
            ->first();

        if (!$findBooking) {

            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }

//        try {
//            BookingPoint::create(['booking_id' => $findBooking->id,
//                'lat' => $data['lat'], 'lng' => $data['lng'], 'type' => 2]);
//        } catch (\Exception $e) {
//            return $socket->emit($data['user_id'] . '-driverStatus', [
//                'result' => 'error',
//                'message' => 'Error in Saving Driver Waiting Time: ' . $e,
//                'data' => null
//            ]);
//        }


        $totalDistance = $initialDistance = $finalDistance = 0;
        $fareType = 'mobile';
        $mobileDistance = 0;


        $p2pDistance = P2PBookingTracking::where('booking_id', $findBooking->id)->sum('distance');
        $p2pRideDistance = P2PBookingTracking::where('booking_id', $findBooking->id)->where('driver_status', '!=', 0)->sum('distance');
        $p2pInitialDistance = P2PBookingTracking::where('booking_id', $findBooking->id)->where('driver_status', 0)->sum('distance');

        $p2pDistance = round($p2pDistance, 2);
        $p2pRideDistance = round($p2pRideDistance, 2);
        $p2pInitialDistance = round($p2pInitialDistance, 2);


        //calculating Distance From Google Location API
        $initialPoints = $endPoints = $startPoints = array();
        $pickUpDistance = $rideDistance = 0;
        $bookingPoints = BookingPoint::where('booking_id', $findBooking->id)->get();
        if (sizeof($bookingPoints) > 0) {
            foreach ($bookingPoints as $bookingPoint) {
                if ($bookingPoint->type == 0) {
                    $initialPoints = ['lat' => $bookingPoint->lat, 'lng' => $bookingPoint->lng];
                } elseif ($bookingPoint->type == 1) {
                    $startPoints = ['lat' => $bookingPoint->lat, 'lng' => $bookingPoint->lng];
                } elseif ($bookingPoint->type == 2) {
                    $endPoints = ['lat' => $bookingPoint->lat, 'lng' => $bookingPoint->lng];
                }

            }


            if(sizeof($startPoints) > 0 && sizeof($initialPoints) > 0)
            {
                $pickUpDistanceCalculate = $this->getDistance($initialPoints['lat'], $initialPoints['lng'],
                    $startPoints['lat'], $startPoints['lng']);


                $pickUpDistance = $this->gettingDistanceInKm($pickUpDistanceCalculate);
            }




//            $rideDistanceCalculate = $this->getDistance($startPoints['lat'], $startPoints['lng'],
//                $endPoints['lat'], $endPoints['lng']);

//            $rideDistance = $this->gettingDistanceInKm($rideDistanceCalculate);

        }



//        $totalDistance = $mobileDistance;
//        $initialDistance = $data['mobile_initial_distance'];
//        $finalDistance = $data['mobile_final_distance'];
        $totalDistance = $rideDistance + $pickUpDistance;
        $initialDistance = $pickUpDistance;
        $finalDistance = $rideDistance;
        $fareType = 'mobile';

//        if ($mobileDistance > $p2pDistance) {
//            $diffInDistance = $mobileDistance - $p2pDistance;
//            if ($diffInDistance >= 10) {
//                $totalDistance = $mobileDistance;
//                $initialDistance = $data['mobile_initial_distance'];
//                $finalDistance = $data['mobile_final_distance'];
//                $fareType = 'mobile';
//            }
//            else {
//                $googleDistance = $p2pInitialDistance + $findBooking->total_distance;
//
//                $differenceDistance = $mobileDistance - $googleDistance;
//
//                if ($differenceDistance > 0) {
//                    $totalDistance = $mobileDistance;
//                    $initialDistance = $data['mobile_initial_distance'];
//                    $finalDistance = $data['mobile_final_distance'];
//                    $fareType = 'mobile';
//                }
//                else {
//                    $totalDistance = $googleDistance;
//                    $initialDistance = $p2pInitialDistance;
//                    $finalDistance = $findBooking->total_distance;
//                    $fareType = 'google';
//                }
//
//            }
//        }
//        else{
//            $totalDistance = $mobileDistance;
//            $initialDistance = $data['mobile_initial_distance'];
//            $finalDistance = $data['mobile_final_distance'];
//            $fareType = 'mobile';
//        }

        $fare = $this->calculateFare($findBooking, $p2pDistance, $p2pInitialDistance,
            $p2pRideDistance, $totalDistance,
            $initialDistance, $finalDistance, $fareType);


        try {

            //get passenger wallet

//            $wallet_balance = $this->passengerWalletBalance($findBooking->passenger_id);
//
//            if ($findBooking->payment_type == 'wallet') {
//                if ($wallet_balance > 0) {
//                    $findBooking->bookingDetail->update([
//                        'passenger_wallet_paid' => $wallet_balance
//                    ]);
//                }
//
//            }


            $findBooking->driver_status = $data['driver_status'];
            $findBooking->total_calculated_distance = $totalDistance;
            $findBooking->actual_fare = $fare['total_fare'];
//            $findBooking->fine_amount = $fare['fine'];
            $findBooking->save();

            $findBooking->bookingDetail->update([
                'ride_end_time' => $fare['ride_end_time']->format('Y-m-d H:i:s'),
                'total_ride_minutes' => $fare['totalRideTime'],
                'p2p_before_pick_up_distance' => $p2pInitialDistance,
                'p2p_after_pick_up_distance' => $p2pRideDistance,
                'mobile_final_distance' => $rideDistance,
                'mobile_initial_distance' => $pickUpDistance,
                'min_vehicle_fare' => $fare['serviceBaseFare']
            ]);


        }
        catch (\Exception $e) {
//            DB::rollBack();
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error in Updating Driver Status: ' . $e,
                'data' => null
            ]);
        }

        //change voucher status
        try {
            if ($findBooking->bookingDetail->is_voucher == 1) {
                $voucher_detail = json_decode($findBooking->bookingDetail->voucher_detail);

                $voucherId = $voucher_detail->voucher_id;

                $find = VoucherCodePassenger::where('passenger_id', $findBooking->passenger_id)
                    ->where('voucher_code_id', $voucherId)->update(['is_used' => 1]);
            }

        }
        catch (\Exception $e) {
//            DB::rollBack();
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error in Updating Voucher Status: ' . $e,
                'data' => null
            ]);
        }

        $findBooking->push();
        $passengerSocketId = $findBooking->passenger_id;
        $passengerFCMToken = $findBooking->passenger->fcm_token;

        $bookingResponse = $this->bookingResponse($findBooking);

        //end ride notification
        $notification_type = 4;
        if ($passengerFCMToken) {
//            $fcmToken = ['fcm_token' => $passengerFCMToken];
            $title = 'Service is Completed';
            $message = 'Congratulations! Service Provider has fixed your problem with automobile.Hope you have enjoyed our services';
            $sendFCMNotification = $this->duringRideNotifications($passengerFCMToken, $bookingResponse, $notification_type, $title, $message);
        }

//        DB::commit();

//        if ($passengerSocketId) {
        $socket->emit($passengerSocketId . '-driverStatus', [
            'result' => 'success',
            'message' => 'Congratulations! You have Reached Your Destination.Hope you have enjoyed our services',
            'data' => $bookingResponse
        ]);
//        }


        return $socket->emit($data['user_id'] . '-driverStatus', [
            'result' => 'success',
            'message' => 'Status Save Successfully.Please View The Receipt and Act Accordingly',
            'data' => $bookingResponse
        ]);


    }

    public function collectFare($data, $socket, $io, $user)
    {
//        DB::beginTransaction();
        $findBooking = Booking::where('id', $data['booking_id'])
            ->where('driver_id', $user->id)->where('driver_status', 3)
            ->where('ride_status', '=', 1)
            ->first();

        if (!$findBooking) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Booking Record Not Found',
                'data' => null
            ]);
        }

//        if (!isset($data['payment_type'])) {
//            return $socket->emit($data['user_id'] . '-driverStatus', [
//                'result' => 'error',
//                'message' => 'Payment Type is a required field',
//                'data' => null
//            ]);
//        }


//        $cashPaid = 0;
//        $extraCashPaid = 0;

//        if ($findBooking->payment_type == 'cash' || $data['payment_type'] == 'cash' || $data['payment_type'] == 'cash_wallet') {
//
//
//            if (!isset($data['total_cash_paid'])) {
//                return $socket->emit($data['user_id'] . '-driverStatus', [
//                    'result' => 'error',
//                    'message' => 'Total Cash Paid is a required field',
//                    'data' => null
//                ]);
//            }
//
//            if (!isset($data['extra_cash_paid'])) {
//                return $socket->emit($data['user_id'] . '-driverStatus', [
//                    'result' => 'error',
//                    'message' => 'Extra Cash Paid is a required field',
//                    'data' => null
//                ]);
//            }
//
//            $cashPaid = $data['total_cash_paid'];
//            $extraCashPaid = $data['extra_cash_paid'];
//
//        }

        try {
            $findBooking->driver_status = $data['driver_status'];
            $findBooking->ride_status = 4;
//            if ($findBooking->payment_type == 'cash_wallet' && $data['payment_type'] == 'cash_wallet') {
//                $findBooking->payment_type = 'cash_wallet';
//            }
            $findBooking->chat = isset($data['chat_message']) ? json_encode($data['chat_messages']) : null;

            $findBooking->save();

            $findBooking->bookingDetail->update([
//                'passenger_total_cash_paid' => $cashPaid,
//                'passenger_extra_cash_paid' => $extraCashPaid,
                'route_json' => isset($data['route_json']) ? $data['route_json'] : null
            ]);
        } catch (\Exception $e) {
//            DB::rollBack();
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error in Updating Record: ' . $e,
                'data' => null
            ]);
        }

        //deduct payment
        try {

            $calculateDiff = $findBooking->actual_fare - $findBooking->estimated_fare;

            $fareAmount = $findBooking->estimated_fare;
            if ($calculateDiff < 0) {
                $fareAmount = $findBooking->actual_fare;
            }


            $funds = $this->stripeService->captureFund($fareAmount, $findBooking->stripe_charge_id);

            if ($funds['type'] == 'error') {
                return $socket->emit($data['user_id'] . '-driverStatus', [
                    'result' => 'error',
                    'message' => $funds['message'],
                    'data' => null
                ]);
            }


        } catch (\Exception $e) {
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => 'error',
                'message' => 'Error during Payment from Stripe: ' . $e,
                'data' => null
            ]);
        }


        if ($calculateDiff > 0) {
            if ($findBooking->actual_fare != $findBooking->estimated_fare) {
                //if we have new fare then charge user directly
                try {

                    $passengerCustomerID = $findBooking->passenger->stripe_customer_id;

                    $charge = $this->stripeService->charge($findBooking, $passengerCustomerID, $calculateDiff);

                    if (isset($charge['type']) && $charge['type'] == 'error') {
                        return makeResponse('error', $charge['message'], 500);
                    }

                    $findBooking->stripe_charge_id = $charge;
                    $findBooking->save();

                } catch (\Exception $e) {
                    return $socket->emit($data['user_id'] . '-driverStatus', [
                        'result' => 'error',
                        'message' => 'Error during Payment from Stripe direct Charge: ' . $e,
                        'data' => null
                    ]);
                }
            }
        }

        //updateWallet
        $walletUpdate = $this->walletService->updateFareWallets($findBooking);

        if ($walletUpdate['result'] == 'error') {
//            DB::rollBack();
            return $socket->emit($data['user_id'] . '-driverStatus', [
                'result' => $walletUpdate['result'],
                'message' => $walletUpdate['message'],
                'data' => null
            ]);
        }

        $findBooking->push();

//        DB::commit();


        $passengerSocketId = $findBooking->passenger_id;
        $passengerFCMToken = $findBooking->passenger->fcm_token;

        $bookingResponse = $this->bookingResponse($findBooking);


        //collect fare notification
        $notification_type = 5;
        if ($passengerFCMToken) {
//            $fcmToken = ['fcm_token' => $passengerFCMToken];
            $title = 'Rating:';
            $message = 'Give your value able feedback to our service provider.';
            $sendFCMNotification = $this->duringRideNotifications($passengerFCMToken, $bookingResponse, $notification_type, $title, $message);
        }

//        if ($passengerSocketId) {
        $socket->emit($passengerSocketId . '-driverStatus', [
            'result' => 'success',
            'message' => 'Give Your Feedback to our driver',
            'data' => (object)$bookingResponse
        ]);
//        }


        return $socket->emit($data['user_id'] . '-driverStatus', [
            'result' => 'success',
            'message' => 'Ride is Complete. Provide Your Feedback to Passenger',
            'data' => (object)$bookingResponse
        ]);


    }


    public function calculateFare($findBooking, $p2pDistance, $p2pInitialDistance, $p2pRideDistance, $totalDistance, $mobile_initial_distance, $mobile_final_distance, $fareType)
    {
        $calculateWaitingTime = $totalTime = 0;

        //calculate Waiting Time Fare
        $driverWaitingTime = $findBooking->bookingDetail->driver_waiting_time;
        $allowedTime = $findBooking->bookingDetail->allowed_waiting_time;

        if ($allowedTime) {
            if ($allowedTime < $driverWaitingTime) {
                $totalTime = $driverWaitingTime;
            }
            $waitingTimePrice = $findBooking->bookingDetail->waiting_price_per_min;
            $calculateWaitingTime = (float)($waitingTimePrice * $totalTime);


        }
        else {

            $waitingTimePrice = $findBooking->bookingDetail->waiting_price_per_min;
            $calculateWaitingTime = (float)($waitingTimePrice * $driverWaitingTime);
        }


        //calculate Pick Up Fare
        $pickUpTimeCalculation = $findBooking->bookingDetail->total_minutes_to_reach_pick_up_point * $findBooking->bookingDetail->initial_time_rate;
        if ($fareType == 'google') {
            $pickUpDistanceCalculation = $p2pInitialDistance * $findBooking->bookingDetail->initial_distance_rate;
        } else {
            $pickUpDistanceCalculation = $mobile_initial_distance * $findBooking->bookingDetail->initial_distance_rate;
        }

        $totalInitialFare = $pickUpTimeCalculation + $pickUpDistanceCalculation;


        //calculate service fare
        if ($findBooking->bookingDetail->ride_end_time) {
            $endTime = Carbon::parse($findBooking->bookingDetail->ride_end_time);
        } else {
            $endTime = Carbon::now();
        }

        $startTime = Carbon::parse($findBooking->bookingDetail->ride_start_time);

        //calculate Ride Fare
        $totalRideTime = $endTime->diffInMinutes($startTime);
        $totalRideTimeFare = $totalRideTime * $findBooking->bookingDetail->vehicle_per_min_rate;
        //mobile final distance will be final distance pickup to dropoff its just a name
        $totalDistanceFare = $mobile_final_distance * $findBooking->bookingDetail->vehicle_per_km_rate;

        $serviceBaseFare = 0;
        $getServices = BookingService::where('booking_id',$findBooking->id)->get();

        foreach($getServices as $service)
        {
            if($service->quantity == 0)
            {
                $serviceBaseFare = $serviceBaseFare + $service->base_fare;
            }
            else{
                $serviceBaseFare = $serviceBaseFare + ($service->base_fare * $service->quantity);
            }
        }


//        $totalFare = (float)($totalRideTimeFare + $totalDistanceFare + $totalInitialFare
//            + $pickUpDistanceCalculation + $calculateWaitingTime);

        $totalFare = (float)($totalRideTimeFare + $totalDistanceFare + $totalInitialFare
            + $calculateWaitingTime + $serviceBaseFare );


        //get passenger wallet


        $wallet_balance = $this->passengerWalletBalance($findBooking->passenger_id);


        //peak factor calculation here

        if ($findBooking->bookingDetail->peak_factor_applied == 1) {
            $totalFare = $totalFare * $findBooking->bookingDetail->peak_factor_rate;
        }

        if ($wallet_balance != 0 && $wallet_balance < 0) {
            $totalFare = $totalFare + $wallet_balance;
        }

        if ($findBooking->bookingDetail->is_voucher == 1) {
            $discountAmount = 0;
            $discountType = 'percentage';
            $totalDiscountedPrice = 0;
            $voucher_detail = json_decode($findBooking->bookingDetail->voucher_detail);
            if (isset($voucher_detail->expiry_date)) {
                $voucherExpiryDate = Carbon::parse($voucher_detail->expiry_date);
                $currentDate = Carbon::now();
                if ($voucherExpiryDate < $currentDate) {
                    $discountAmount = 0;
                } else {
                    if (isset($voucher_detail->discount_value)) {
                        $discountAmount = $voucher_detail->discount_value;
                    }

                }

                if (isset($voucher_detail->discount_type)) {
                    $discountType = $voucher_detail->discount_type;
                }
            }

            if ($discountAmount == 0) {
                $totalDiscountedPrice = 0;
            } else {
                if ($discountType == 'percentage') {
                    $totalDiscountedPrice = ($totalFare * $discountAmount) / 100;
                }

            }


            $totalFare = $totalFare - $totalDiscountedPrice;

        }

        $totalFare = $totalFare + $findBooking->bookingDetail->min_vehicle_fare;

        //find Tax

        if (isset($findBooking->bookingDetail->vehicle_tax)) {
            $tax = ($totalFare * $findBooking->bookingDetail->vehicle_tax) / 100;
        } else {
            $tax = 0;
        }

        //add Tax

        $totalFare = $totalFare + $tax;


        $totalCollectFare = 0;

//        if ($wallet_balance > 0 && $findBooking->payment_type == 'wallet') {
//            if ($wallet_balance > $totalFare) {
//                $totalCollectFare = $wallet_balance - $totalFare;
//            } else {
//                $totalCollectFare = $totalFare - $wallet_balance;
//            }
//
//        } else {
        $totalCollectFare = $totalFare;
//        }

        $fare = [
            'total_fare' => ceil($totalFare),
            'totalCollectFare' => $totalCollectFare,
            'waiting_time_fare' => $calculateWaitingTime,
            'distance_fare' => $totalDistanceFare + $totalRideTimeFare,
            'totalRideTime' => $totalRideTime,
            'ride_end_time' => $endTime,
            'serviceBaseFare' => $serviceBaseFare
//            'fine' => $wallet_balance
        ];

        return $fare;

    }
}
