<?php


namespace App\Traits;

use App\Services\API\Driver\AuthService;
use CoreProc\WalletPlus\Models\WalletType;
use App\Models\Setting;
use App\Models\FranchiseWallet;
use App\Models\DriverWalletModel;
use App\Models\PassengersWallet;
use App\Models\CompanyWallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait CreateUserWalletTrait
{

    public function createUserWallet($user, $walletTypeName = "")
    {
        if (isset($user)) {
            $user_type = $user->user_type;
            if ($walletTypeName == "" || $walletTypeName == null) {
                if ($user_type == 1)
                    $walletTypeName = "Passenger-Wallet";
                if ($user_type == 2)
                    $walletTypeName = "Driver-Wallet";
                if ($user_type == 3)
                    $walletTypeName = "Admin-Franchise-Wallet";
            }
        }
        $walletType = WalletType::where('name', $walletTypeName)->first();

        if ($walletType && isset($walletType->id)) {

            $user->wallets()->create(['wallet_type_id' => $walletType->id]);

        } else {

            $walletType = WalletType::create([
                'name' => $walletTypeName,
                'decimals' => 2, // Set how many decimal points your wallet accepts here. Defaults to 0.
            ]);

            $user->wallets()->create(['wallet_type_id' => $walletType->id]);
        }

        return true;
    }


    public function passengerUpdateWalletRideFare($booking)
    {

        $actual_fare = $booking->actual_fare;
        $totalCashPayAmount = $booking->bookingDetail->passenger_total_cash_paid;
        $walletPayAmount = 0;
        $extraPayAmount = 0;


        if ($totalCashPayAmount > 0) {
            if ($totalCashPayAmount >= $actual_fare)
                $totalCashPayAmount = $actual_fare;

            if ($totalCashPayAmount < $actual_fare)
                $totalCashPayAmount = $actual_fare - $totalCashPayAmount;

            $this->passengerWalletUpdate($booking, $totalCashPayAmount, "debit", "cash", "Ride fare amount pay by passenger.");

            $this->passengerWalletUpdate($booking, $totalCashPayAmount, "credit", "cash", "Ride fare amount pay by passenger.");
        }

        if (isset($booking->bookingDetail->passenger_extra_cash_paid)) {
            $extraPayAmount = $booking->bookingDetail->passenger_extra_cash_paid;
            if ($extraPayAmount > 1)
                $this->passengerWalletUpdate($booking, $extraPayAmount, "credit", "cash", "Extra amount pay by passenger.");
        }

        if (isset($booking->bookingDetail->passenger_wallet_paid)) {
            $walletPayAmount = $booking->bookingDetail->passenger_wallet_paid;
            $passengerWalletBalance = $this->passengerWalletBalance($booking->passenger_id);
            if ($walletPayAmount > 0 && $passengerWalletBalance > 0 && $passengerWalletBalance >= $walletPayAmount)
                $this->passengerWalletUpdate($booking, $walletPayAmount, "debit", "wallet", "Ride amount pay from wallet by passenger.");
        }

        $balance = $this->passengerWalletBalance($booking->passenger_id);


        $data = [
            'wallet_balance' => $balance
        ];

        return $data;
    }


    public function driverUpdateWalletRideFare($booking)
    {
        if (isset($booking->driver)) {
            $actual_fare        = 0;
            $totalCashPayAmount = 0;
            $walletPayAmount    = 0;
            $extraPayAmount     = 0;

//            $wallet                 = $booking->driver->wallet('Driver-Wallet');
//            if(!isset($wallet) || $wallet == null)
//                $wallet             = $this->createUserWallet($booking->driver,"Driver-Wallet");

            if (isset($booking->actual_fare))
                $actual_fare        = $booking->actual_fare;

            if (isset($booking->bookingDetail->passenger_total_cash_paid))
                $totalCashPayAmount = $booking->bookingDetail->passenger_total_cash_paid;

            if (isset($booking->bookingDetail->passenger_wallet_paid))
                $walletPayAmount    = $booking->bookingDetail->passenger_wallet_paid;

            if (isset($booking->bookingDetail->passenger_extra_cash_paid))
                $extraPayAmount     = floatval($booking->bookingDetail->passenger_extra_cash_paid);

            $cashFare = $actual_fare;
            if ($walletPayAmount > 0 && $actual_fare > $totalCashPayAmount) {
                $cashFare           = $totalCashPayAmount;
            }
            if ($walletPayAmount > 0 && $totalCashPayAmount == 0 && $actual_fare == $walletPayAmount) {
                $cashFare           = 0;
            }

            if ($walletPayAmount > 0) {
                $driverShareAmount = 0;
                $rideTaxAmount = 0;
                $franchiseShareAmount = 0;
                $companyShareAmount = 0;
                $driverShareAmountAfterTaxDeduct = 0;
                $fareDistributions = $this->rideFareDistributions($walletPayAmount);

                if (isset($fareDistributions)) {
                    $driverShareAmount = $fareDistributions['driverShareAmount'];
                    $rideTaxAmount = $fareDistributions['rideTaxAmount'];
                    $franchiseShareAmount = $fareDistributions['franchiseShareAmount'];
                    $companyShareAmount = $fareDistributions['companyShareAmount'];
                    $driverShareAmountAfterTaxDeduct = $fareDistributions['driverShareAfterTaxDeduction'];
                    $franchisePlusCompanyShareAmount = floatval($rideTaxAmount + $franchiseShareAmount + $companyShareAmount);

                    $this->driverWalletUpdate($booking, $driverShareAmount, $franchisePlusCompanyShareAmount, $rideTaxAmount, $driverShareAmountAfterTaxDeduct, "debit", "wallet", "Wallet amount paid by passenger ");

                    $this->franchiseWalletUpdate($booking, $franchiseShareAmount, $rideTaxAmount, $companyShareAmount, $driverShareAmountAfterTaxDeduct, "credit", "wallet");

                    $this->companyWalletUpdate($booking, $franchiseShareAmount, $rideTaxAmount, $companyShareAmount, "credit", "wallet", "Booking Amount Paid with wallet");

                }

            }


            if ($cashFare > 0) {
                $driverShareAmountAfterTaxDeduct = 0;
                $fareDistributions = $this->rideFareDistributions($cashFare);
                //dd($fareDistributions);
                if (isset($fareDistributions)) {
                    $driverShareAmount                  = $fareDistributions['driverShareAmount'];
                    $rideTaxAmount                      = $fareDistributions['rideTaxAmount'];
                    $franchiseShareAmount               = $fareDistributions['franchiseShareAmount'];
                    $companyShareAmount                 = $fareDistributions['companyShareAmount'];
                    $driverShareAmountAfterTaxDeduct    = $fareDistributions['driverShareAfterTaxDeduction'];
                    $franchisePlusCompanyShareAmount    = floatval($rideTaxAmount + $franchiseShareAmount + $companyShareAmount);
                    $toto_company_amount                = floatval($rideTaxAmount + $companyShareAmount);
                    //Update Driver Wallet
                    $this->driverWalletUpdate($booking, $driverShareAmount, $franchisePlusCompanyShareAmount, 0, $driverShareAmountAfterTaxDeduct, "debit", "cash", "On ride completion cash amount.");
                    $this->driverWalletUpdate($booking, $driverShareAmount, $franchisePlusCompanyShareAmount, $rideTaxAmount, $driverShareAmountAfterTaxDeduct, "credit", "cash", "On ride completion cash amount received by driver.");
                    //Update Franchise Wallet
                    $this->franchiseWalletUpdate($booking, $franchiseShareAmount, $rideTaxAmount, $companyShareAmount, $franchisePlusCompanyShareAmount, "debit", "cash", "On ride completion cash amount received by driver.");
                    // Toto Company Wallet update
                    $this->companyWalletUpdate($booking, $franchiseShareAmount, $rideTaxAmount, $toto_company_amount, "debit", "cash", "On ride completion cash amount received by driver.");
                }
            }

            $data = [
                'passenger_wallet_paid' => $walletPayAmount,
                'passenger_total_cash_paid' => $totalCashPayAmount,
                'passenger_extra_cash_paid' => $extraPayAmount,
                'wallet_balance' => $this->driverWalletBalance($booking->driver_id)
            ];

            return $data;
        }
    }

    // Passenger Wallet
    public function passengerWalletUpdate($data = null, $amount=0, $type="debit", $payment_method="cash", $desc="Ride Fares Paid.")
    {

        $wallet                         = new PassengersWallet;
        if(isset($data)) {
            $wallet->passenger_id       = $data->passenger_id;
            $wallet->booking_id         = $data->id ? $data->id:null;
            $wallet->ride_total_amount  = $data->actual_fare ? $data->actual_fare:null;
        }
        else{
            $wallet->passenger_id       = Auth::user()->id;
        }
        $wallet->payment_method         = $payment_method;
        $wallet->type                   = $type;
        $wallet->amount                 = $amount;
        $wallet->description            = $desc;
        $wallet->save();
    }

// Driver Wallet
    public function driverWalletUpdate($data, $driver_total_amount=0, $franchise_amount=0, $tax_amount=0, $driver_amount=0, $type="debit", $payment_method="cash", $desc="Driver Ride Fare Details")
    {

        //dd($data);
        $wallet                         = new DriverWalletModel;
        if(isset($data) && isset($data->id)) {
            if(isset($data->driver_id))
            $wallet->driver_id          = $data->driver_id;
            if(isset($data->franchise_id))
            $wallet->franchise_id       = $data->franchise_id;
            if(isset($data->id))
            $wallet->booking_id         = $data->id;
            if(isset($data->actual_fare))
            $wallet->ride_total_amount  = $data->actual_fare;
        }
        if(isset($data) && isset($data[0]['driver_id'])){
            $wallet->driver_id          = $data[0]['driver_id'];
        }
        if(isset($data) && isset($data[0]['franchise_id'])){
            $wallet->franchise_id       = $data[0]['franchise_id'];
        }
        $wallet->payment_method         = $payment_method;
        $wallet->type                   = $type;
        $wallet->driver_total_amount    = $driver_total_amount;
        $wallet->franchise_amount       = $franchise_amount;
        $wallet->tax_amount             = $tax_amount;
        $wallet->amount                 = $driver_amount;
        $wallet->description            = $desc;
        $wallet->save();
    }


// Franchise Wallet
    public function franchiseWalletUpdate($data, $franchise_amount=0, $tax_amount=0, $toto_amount=0, $total_amount=0, $type="debit", $payment_method="cash", $desc="Franchise Ride Fare Details")
    {

        $franchiseWallet                    = new FranchiseWallet;
        if(isset($data) && isset($data->id)) {
            if(isset($data->driver_id))
            $franchiseWallet->driver_id     = $data->driver_id;
            if(isset($data->franchise_id))
            $franchiseWallet->franchise_id  = $data->franchise_id;
            if(isset($data->id))
            $franchiseWallet->booking_id    = $data->id;
        }
        if(isset($data) && isset($data[0]['driver_id'])){
            $franchiseWallet->driver_id     = $data[0]['driver_id'];
        }
        if(isset($data) && isset($data[0]['franchise_id'])){
            $franchiseWallet->franchise_id  = $data[0]['franchise_id'];
        }
        $franchiseWallet->payment_method    = $payment_method;
        $franchiseWallet->type              = $type;
        $franchiseWallet->franchise_amount  = $franchise_amount;
        $franchiseWallet->tax_amount        = $tax_amount;
        $franchiseWallet->company_amount    = $toto_amount;
        $franchiseWallet->total_amount      = $total_amount;
        $franchiseWallet->description       = $desc;

        $franchiseWallet->save();
    }


// Toto Company Wallet Update
    public function companyWalletUpdate($data, $franchise_amount=0, $tax_amount=0, $total_amount=0, $type="debit", $payment_method="cash", $desc="Toto Ride Fare Details")
    {

        $franchiseWallet                    = new CompanyWallet;
        if(isset($data) && isset($data->id)) {
            $franchiseWallet->driver_id     = $data->driver_id;
            $franchiseWallet->franchise_id  = $data->franchise_id;
            $franchiseWallet->booking_id    = $data->id;
        }
        if(isset($data) && isset($data[0]['driver_id'])){
            $franchiseWallet->driver_id     = $data[0]['driver_id'];
        }
        if(isset($data) && isset($data[0]['franchise_id'])){
            $franchiseWallet->franchise_id  = $data[0]['franchise_id'];
        }
        $franchiseWallet->payment_method    = $payment_method;
        $franchiseWallet->type              = $type;
        $franchiseWallet->franchise_amount  = $franchise_amount;
        $franchiseWallet->tax_amount        = $tax_amount;
        $franchiseWallet->total_amount      = $total_amount;
        $franchiseWallet->description       = $desc;
        $franchiseWallet->save();

    }

    public function rideFareDistributions($fareAmount, $taxOnTotalAmount = 0)
    {
        $fareDistribution           = Setting::orderBy('id', 'desc')->first();
        //dd($fareDistribution);
        if(isset($fareDistribution) && $fareDistribution != null) {
            $franchiseShareAmount   = (intval($fareDistribution->franchise_share) / 100 ) * floatval($fareAmount);
            $companyShareAmount     = (intval($fareDistribution->company_share) / 100 ) * floatval($fareAmount);
            $driverShareAmount      = (intval($fareDistribution->driver_share) / 100 ) * floatval($fareAmount);
            // Tax on Driver share
            $rideTaxAmount          = (intval($fareDistribution->tax_share) / 100 ) * floatval($driverShareAmount);
            $driverShareAfterTaxDeduction = (abs($driverShareAmount) - abs($rideTaxAmount));

//        if($taxOnTotalAmount == 1)  // Tax on total amount
//            $rideTaxAmount  = (intval($fareDistribution->tax_share) / 100) * * floatval($fareAmount);

            $data = [
                'driverShareAmount'             => floatval($driverShareAmount),
                'driverShareAfterTaxDeduction'  => floatval($driverShareAfterTaxDeduction),
                'rideTaxAmount'                 => floatval($rideTaxAmount),
                'franchiseShareAmount'          => floatval($franchiseShareAmount),
                'companyShareAmount'            => floatval($companyShareAmount),
                'fareAmount'                    => floatval($fareAmount),
            ];

            return $data;
        }
        return false;
    }


    public function passengerWalletBalance($id)
    {
        $totalCreditAmount  = PassengersWallet::where('passenger_id', $id)->where('type', 'credit')->sum('amount');
        $totalDebitAmount   = PassengersWallet::where('passenger_id', $id)->where('type', 'debit')->sum('amount');
        $balance            = $totalCreditAmount - $totalDebitAmount;
        return $balance;
    }

    public function driverWalletBalance($id)
    {
        $totalCreditAmount  = DriverWalletModel::where('driver_id', $id)->where('type', 'credit')->sum('amount');
        $totalDebitAmount   = DriverWalletModel::where('driver_id', $id)->where('type', 'debit')->sum('amount');
        $balance            = $totalCreditAmount - $totalDebitAmount;
        return $balance;
    }

    public function franchiseWalletBalance($id)
    {
        $totalCreditAmount  = FranchiseWallet::where('franchise_id', $id)->where('type', 'credit')->sum('franchise_amount');
        $totalDebitAmount   = FranchiseWallet::where('franchise_id', $id)->where('type', 'debit')->sum('franchise_amount');
        $balance            = $totalCreditAmount - $totalDebitAmount;
        return $balance;
    }
}
