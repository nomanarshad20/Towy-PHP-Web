<?php


namespace App\Services\API\Socket;


use App\Models\Booking;
use App\Models\BookingCancelReason;
use App\Traits\CalculateCancelPercentageAmountTrait;
use App\Traits\CreateUserWalletTrait;
use App\Traits\SendFirebaseNotificationTrait;

class DriverCancelService
{
    use CalculateCancelPercentageAmountTrait, CreateUserWalletTrait, SendFirebaseNotificationTrait;

    public function cancelService($data, $socket, $io, $currentUser)
    {
        if(!isset($data['cancel_reason_id']))
        {
            return $socket->emit($currentUser->id . '-driverCancelBooking', [
                'result' => 'error',
                'message' => 'Cancel Reason is a required field',
                'data' => null
            ]);
        }

        $findBooking = Booking::with('bookingDetail')->where('id', $data['booking_id'])
            ->where('driver_id', $currentUser->id)
            ->first();

        if (!$findBooking) {
            return $socket->emit($currentUser->id . '-driverCancelBooking', [
                'result' => 'error',
                'message' => 'Booking Not Found',
                'data' => null
            ]);
        }

        $findReason = BookingCancelReason::where('user_type', 'driver')
            ->where('id', $data['cancel_reason_id'])->first();

        if (!$findReason) {
            return $socket->emit($currentUser->id . '-driverCancelBooking', [
                'result' => 'error',
                'message' => 'Cancel Reason Not Found',
                'data' => null
            ]);
        }

        try {

            $calculateFine = $this->calculatePercentage($findBooking);

            $other_reason = null;

            if ($findReason->reason == "Other" || $findReason->id == 5) {

                if(!isset($data['other_reason']))
                {
                    return $socket->emit($currentUser->id . '-driverCancelBooking', [
                        'result' => 'error',
                        'message' => 'Other Cancel Reason is a required field',
                        'data' => null
                    ]);
                }

                $other_reason = $data['other_reason'];
            }

            $findBooking->driver->driverCoordinate->update(['status' => 1]);

            $findBooking->update([
                'ride_status' => 5,
                'cancel_id' => $data['cancel_reason_id'],
                'other_cancel_reason' => $other_reason,
                'fine_amount' => $calculateFine,
                'chat' => isset($data['chat_messages']) ? json_encode($data['chat_messages']):null
            ]);

            //add amount in driver wallet and send back in response so that on driver app it is updated
            if ($calculateFine > 0) {

                $this->driverWalletUpdate($findBooking, $calculateFine, 0, "debit", "fine", "Driver ride cancel penalty amount.");
                $this->franchiseWalletUpdate($findBooking, 0, 0, $calculateFine, 0, "debit", "fine", "Driver ride cancel penalty amount.");

                $this->companyWalletUpdate($findBooking, 0, 0, $calculateFine, "debit", "fine", "Driver ride cancel penalty amount.");

//                $wallet = $findBooking->driver->wallet('Driver-Wallet');
//                $wallet->decrementBalance($calculateFine);
            }

            //send Notification to Passenger
            $passengerFCM = $findBooking->passenger->fcm_token;

            if ($passengerFCM) {
                $notificationType = 6;
                $title = 'Driver Cancel The Ride';
                $message = 'Driver han Cancelled your ride request';
                $sendNotification = $this->cancelRide($passengerFCM, $notificationType, $title, $message);
            }

            $balance = $this->driverWalletBalance($currentUser->id);

            $data = [
                'fine' => $findBooking->fine_amount,
                'driver_wallet_balance' => $balance
            ];

             $socket->emit($findBooking->passenger_id . '-driverCancelBooking', [
                'result' => 'success',
                'message' => 'Cancel Ride Successfully' ,
                'data' => null
            ]);

            return $socket->emit($currentUser->id . '-driverCancelBooking', [
                'result' => 'success',
                'message' => 'Cancel Ride Successfully' ,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $socket->emit($currentUser->id . '-driverCancelBooking', [
                'result' => 'error',
                'message' => 'Error in Updating Records: ' . $e,
                'data' => null
            ]);

        }


    }
}
