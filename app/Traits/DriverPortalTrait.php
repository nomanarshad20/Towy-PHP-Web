<?php

namespace App\Traits;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
trait DriverPortalTrait
{

    public function driverPortalPreviousDetails($id, $fromDate, $tillDate)
    {
        $ridesSummary = [];

        $driverPortal = DB::select('CALL sp_NewDriverPortalData (?,?,?)', array($id, $fromDate, $tillDate));

        if (isset($driverPortal)) {

            $totalDriverShare                                   = $driverPortal[0]->totalDriverCreditAmount + $driverPortal[0]->driverTotalBonus + $driverPortal[0]->totalDriverWalletAmount;

//            if($driverPortal[0]->totalPassengerPaidExtraAmount > 0)
//                $totalDriverShare                               = $totalDriverShare - $driverPortal[0]->totalPassengerPaidExtraAmount;

            $finalAmount                                        = $totalDriverShare - $driverPortal[0]->totalCashCollectedByDriver;

            if($driverPortal[0]->amountReceivedFromDriver > 0)
                $finalAmount                                    = $finalAmount + $driverPortal[0]->amountReceivedFromDriver;

            if($driverPortal[0]->amountPaidToDriver > 0)
                $finalAmount                                    = $finalAmount - $driverPortal[0]->amountPaidToDriver;

            $ridesSummary['previous_final_total_amount']        = $finalAmount;
        } else {
            $ridesSummary['previous_final_total_amount']        = 0;
        }

        return $ridesSummary;
    }

    public function driverPortalDetails($id, $fromDate, $tillDate,$previousAmount=0)
    {
        $ridesSummary = [];

        $driverPortal = DB::select('CALL sp_NewDriverPortalData (?,?,?)', array($id, $fromDate, $tillDate));

        if (isset($driverPortal)) {

            if (isset($driverPortal[0]->totalRideActualAmount))
                $ridesSummary['totalRideActualAmount']          = $driverPortal[0]->totalRideActualAmount;
            if (isset($driverPortal[0]->totalCashCollectedByDriver))
                $ridesSummary['totalCashCollectedByDriver']     = $driverPortal[0]->totalCashCollectedByDriver;

            if (isset($driverPortal[0]->driverTotalBonus))
                $ridesSummary['driverTotalBonus']               = $driverPortal[0]->driverTotalBonus;

            if (isset($driverPortal[0]->totalDriverCreditAmount))
                $ridesSummary['totalDriverCreditAmount']        = $driverPortal[0]->totalDriverCreditAmount;

            if (isset($driverPortal[0]->totalTaxAmount))
                $ridesSummary['totalTaxAmount']                 = $driverPortal[0]->totalTaxAmount;

            if (isset($driverPortal[0]->totalDriverWalletAmount))
                $ridesSummary['totalDriverWalletAmount']        = $driverPortal[0]->totalDriverWalletAmount;

            if (isset($driverPortal[0]->totalDriverCancelPenalty))
                $ridesSummary['totalDriverCancelPenalty']       = $driverPortal[0]->totalDriverCancelPenalty;

            if (isset($driverPortal[0]->amountPaidToDriver))
                $ridesSummary['amountPaidToDriver']             = $driverPortal[0]->amountPaidToDriver;

            if (isset($driverPortal[0]->amountReceivedFromDriver))
                $ridesSummary['amountReceivedFromDriver']       = $driverPortal[0]->amountReceivedFromDriver;

            $ridesSummary['totalPassengerCancelPenalty']        = 0;
            $ridesSummary['totalDriverCashEarnings']            = $driverPortal[0]->totalDriverCreditAmount + $ridesSummary['totalTaxAmount'];

            $totalDriverShare                                   = $driverPortal[0]->totalDriverCreditAmount + $driverPortal[0]->driverTotalBonus + $driverPortal[0]->totalDriverWalletAmount;

            if (isset($driverPortal[0]->totalPassengerPaidExtraAmount))
                $ridesSummary['totalPassengerPaidExtraAmount']  = $driverPortal[0]->totalPassengerPaidExtraAmount;

//            if($driverPortal[0]->totalPassengerPaidExtraAmount > 0)
//                $totalDriverShare                               = $totalDriverShare - $driverPortal[0]->totalPassengerPaidExtraAmount;

            $ridesSummary['newSum']                             = $totalDriverShare;

            $finalAmount                                        = $totalDriverShare - $driverPortal[0]->totalCashCollectedByDriver;

            $ridesSummary['remainings']                         = $finalAmount;

            $ridesSummary['previous_total_amount']              = $previousAmount;

            if($previousAmount != 0)
                $finalAmount                                    = $finalAmount + $previousAmount;

            if($driverPortal[0]->amountReceivedFromDriver > 0)
                $finalAmount                                    = $finalAmount + $driverPortal[0]->amountReceivedFromDriver;

            if($driverPortal[0]->amountPaidToDriver > 0)
                $finalAmount                                    = $finalAmount - $driverPortal[0]->amountPaidToDriver;

            $ridesSummary['final_total_amount']                 = $finalAmount;
            // Rides Count
            if (isset($driverPortal[0]->ratingsAvg))
                $ridesSummary['ratingsAvg']                     = $driverPortal[0]->ratingsAvg;
            if (isset($driverPortal[0]->totalCompletedRides))
                $ridesSummary['totalCompletedRides']            = $driverPortal[0]->totalCompletedRides;
            if (isset($driverPortal[0]->totalReceivedRides))
                $ridesSummary['totalReceivedRides']             = $driverPortal[0]->totalReceivedRides;
            if (isset($driverPortal[0]->totalAcceptRides))
                $ridesSummary['totalAcceptRides']               = $driverPortal[0]->totalAcceptRides;
            if (isset($driverPortal[0]->totalIgnoreRides))
                $ridesSummary['totalIgnoreRides']               = $driverPortal[0]->totalIgnoreRides;
            if (isset($driverPortal[0]->totalRejectRides))
                $ridesSummary['totalRejectRides']               = $driverPortal[0]->totalRejectRides;
            if (isset($driverPortal[0]->totalDriverCancelRides))
                $ridesSummary['totalDriverCancelRides']         = $driverPortal[0]->totalDriverCancelRides;
            if (isset($driverPortal[0]->totalPassengerCancelRides))
                $ridesSummary['totalPassengerCancelRides']      = $driverPortal[0]->totalPassengerCancelRides;
            if($driverPortal[0]->totalCompletedRides > 0 && $driverPortal[0]->totalReceivedRides > 0)
            $ridesSummary['completeRidesPercent']               = ($driverPortal[0]->totalCompletedRides / $driverPortal[0]->totalReceivedRides) * 100;
            else
            $ridesSummary['completeRidesPercent']               = 0;

            if($driverPortal[0]->totalAcceptRides > 0 && $driverPortal[0]->totalReceivedRides > 0)
            $ridesSummary['acceptRidesPercent']                 = ($driverPortal[0]->totalAcceptRides / $driverPortal[0]->totalReceivedRides) * 100;
            else
            $ridesSummary['acceptRidesPercent']                 = 0;

        } else {

//            $ridesSummary['totalLoginHours']                = 0;

            $ridesSummary['totalRideActualAmount']          = 0;
            $ridesSummary['totalDriverDebitAmount']         = 0;
            $ridesSummary['totalPassengerPaidExtraAmount']  = 0;
            $ridesSummary['totalDriverCancelPenalty']       = 0;
            $ridesSummary['totalPassengerCancelPenalty']    = 0;
            $ridesSummary['totalTaxAmount']                 = 0;
            $ridesSummary['totalDriverCreditAmount']        = 0;
            $ridesSummary['amountPaidToDriver']             = 0;
            $ridesSummary['amountReceivedFromDriver']       = 0;
            $ridesSummary['driverCurrentBalance']           = 0;
            $ridesSummary['driverTotalBonus']               = 0;
            $ridesSummary['newSum']                         = 0;
            $ridesSummary['previous_total_amount']          = 0;
            $ridesSummary['final_total_amount']             = 0;
            // Ride Count
            $ridesSummary['totalDriverCashEarning']         = 0;
            $ridesSummary['totalReceivedRides']             = 0;
            $ridesSummary['totalAcceptRides']               = 0;
            $ridesSummary['totalIgnoreRides']               = 0;
            $ridesSummary['totalRejectRides']               = 0;
            $ridesSummary['totalCompletedRides']            = 0;
            $ridesSummary['totalDriverCancelRides']         = 0;
            $ridesSummary['totalPassengerCancelRides']      = 0;
            $ridesSummary['completeRidesPercent']           = 0;
            $ridesSummary['acceptRidesPercent']             = 0;
            $ridesSummary['ratingsAvg']                     = 0;

        }

        return $ridesSummary;
    }


}


