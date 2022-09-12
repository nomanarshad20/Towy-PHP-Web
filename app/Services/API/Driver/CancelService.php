<?php


namespace App\Services\API\Driver;


use App\Models\Booking;
use App\Models\BookingCancelReason;
use App\Traits\CalculateCancelPercentageAmountTrait;
use App\Traits\CreateUserWalletTrait;
use App\Traits\SendFirebaseNotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CancelService
{
    use CalculateCancelPercentageAmountTrait;
    use CreateUserWalletTrait;
    use SendFirebaseNotificationTrait;

    public function cancelService($request)
    {
        $findBooking = Booking::with('bookingDetail')->where('id', $request->booking_id)
            ->where('driver_id', Auth::user()->id)
            ->first();



        if (!$findBooking) {
            return makeResponse('error', 'Booking Not Found', 404);
        }

        $findReason = BookingCancelReason::where('user_type', 'driver')
            ->where('id', $request->cancel_reason_id)->first();

        if (!$findReason) {
            return makeResponse('error', 'Cancel Reason Not Found', 404);
        }

        try {

            $calculateFine = $this->calculatePercentage($findBooking);

            $other_reason = null;

            if ($findReason->reason == "Other" || $findReason->id == 5) {
                $other_reason = $request->other_reason;
            }

            $findBooking->driver->driverCoordinate->update(['status' => 1]);

            $findBooking->update([
                'ride_status' => 5,
                'cancel_id' => $request->cancel_reason_id,
                'other_cancel_reason' => $other_reason,
                'fine_amount' => $calculateFine,
                'chat' => json_encode($request->chat_messages)
            ]);

            //add amount in driver wallet and send back in response so that on driver app it is updated
            if ($calculateFine > 0) {

                $this->driverWalletUpdate($findBooking, $calculateFine, 0, "debit", "fine", "Driver ride cancel penalty amount.");
                $this->franchiseWalletUpdate($findBooking, 0, 0, $calculateFine, 0, "debit", "fine","Driver ride cancel penalty amount.");

                $this->companyWalletUpdate($findBooking, 0, 0, $calculateFine, "debit", "fine", "Driver ride cancel penalty amount.");

//                $wallet = $findBooking->driver->wallet('Driver-Wallet');
//                $wallet->decrementBalance($calculateFine);
            }

            //send Notification to Passenger
            $passengerFCM = $findBooking->passenger->fcm_token;

            if($passengerFCM)
            {
                $notificationType = 6;
                $title =  'Driver Cancel The Ride';
                $message = 'Driver han Cancelled your ride request';
                $sendNotification = $this->cancelRide($passengerFCM,$notificationType,$title,$message);
            }

            $balance    =   CreateUserWalletTrait::driverWalletBalance(Auth::user()->id);

            $data = [
                'fine' => $findBooking->fine_amount,
                'driver_wallet_balance' => $balance
            ];



            return makeResponse('success', 'Cancel Ride Successfully', 200,$data);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Updating Records: ' . $e, 500);
        }


    }
}
