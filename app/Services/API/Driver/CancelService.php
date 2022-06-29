<?php


namespace App\Services\API\Driver;


use App\Models\Booking;
use App\Models\BookingCancelReason;
use App\Traits\CalculateCancelPercentageAmountTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CancelService
{
    use CalculateCancelPercentageAmountTrait;

    public function cancelService($request)
    {
        $findBooking = Booking::where('id', $request->booking_id)->where('driver_id', Auth::user()->id)
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
                'fine_amount' => $calculateFine
            ]);

            //add amount in driver wallet and send back in response so that on driver app it is updated
            if ($calculateFine > 0) {
                $wallet = $findBooking->driver->wallet('Driver-Wallet');

                $wallet->decrementBalance($calculateFine);
            }

            $data = [
                'fine' => $findBooking->fine_amount,
                'driver_wallet_balance' => $wallet->balance
            ];

            return makeResponse('success', 'Cancel Ride Successfully', 200,$data);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Updating Records: ' . $e, 500);
        }


    }
}
