<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\API\UpdateWalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\DriverPortalTrait;

class FareDistributionsController extends Controller
{
    use DriverPortalTrait;

    public $updateWalletService;

    public function __construct(UpdateWalletService $updateWalletService)
    {
        $this->updateWalletService = $updateWalletService;
    }

    public function bookingFareDistributes()
    {
        $booking = Booking::with('bookingDetail', 'driver', 'passenger', 'franchise')->orderBy('id', 'desc')->first();
        if (isset($booking) && $booking != null) {
            $responseWallet = $this->updateWalletService->updateFareWallets($booking);
            if ($responseWallet && $responseWallet['result'] == "success" && isset($responseWallet['data'])) {
                return makeResponse('success', 'Fare calculate Successfully', 200, $responseWallet['data']);
            } else {
                return makeResponse('error', $responseWallet['message'], 401);
            }

        } else {
            return makeResponse('error', "Booking not found.", 401);
        }

    }


    public function driverWalletPortal(Request $request)
    {
        $ridesSummary       = [];
        $previousAmount     = 0;

        if (isset(auth()->user()->id))
            $userid = auth()->user()->id;

        if (isset($request['fromDate']) && $request['fromDate'] != null)
            $fromDate = $request['fromDate'];
        else if(!isset($request['fromDate']))
            $fromDate           = Carbon::today();

        if (isset($request['tillDate']) && $request['tillDate'] != null)
            $tillDate = $request['tillDate'];
        else if(!isset($request['tillDate'])){
            $tillDate           = Carbon::now();
        }

        $userInfo           = User::where('id', $userid)->first();

        if(isset($userInfo)){
            $firstDate          = Carbon::parse($fromDate);
            $secondDate         = Carbon::parse($userInfo->created_at);

            if ($firstDate->greaterThan($secondDate)) {
                $preFromDate    = $secondDate;
                $preTillDate    = $firstDate;

                $preDriverCalculations = DriverPortalTrait::driverPortalPreviousDetails($userid, $preFromDate, $preTillDate);

                if(isset($preDriverCalculations) && $preDriverCalculations['previous_final_total_amount'] != 0)
                    $previousAmount = floatval($preDriverCalculations['previous_final_total_amount']);
            }

            if ($firstDate->lessThanOrEqualTo($secondDate))
                $fromDate       = $userInfo->created_at;

            $ridesSummary       = DriverPortalTrait::driverPortalDetails($userid, $fromDate, $tillDate, $previousAmount);

            return makeResponse('success', 'Driver Portal Successfully', 200, $ridesSummary);

        }else{
            return makeResponse('error', "Booking not found.", 401);
        }


        /*if (isset($userInfo)) {

            $preDayDate = date('Y-m-d h:i:s', strtotime('-1 day', strtotime($fromDate)));


            if ($tillDate > Carbon::now())
                $tillDate = Carbon::now();

            $driverRidesSummary['previous_total_amount'] = $driverPreviousEarnings;

            if (isset($preDayDate) && $preDayDate != null && $preDayDate != "") {
                $driverPreviousCalculations = $this->driverPortalDetails($userid, $userInfo->created_at, $preDayDate);

                if (isset($driverPreviousCalculations))
                    $driverPreviousEarnings = $driverPreviousCalculations['final_total_amount'];

                $driverRidesSummary['previous_total_amount'] = round($driverPreviousEarnings, 2);
            }

            $driverCalculations = $this->driverPortalDetails($userid, $fromDate, $tillDate);


            $ridesSummary = array_merge($driverCalculations, $driverRidesSummary);

            return makeResponse('success', 'Driver Portal Successfully', 200, $ridesSummary);

        } else {
            return makeResponse('error', "Booking not found.", 401);
        }*/

    }


}
