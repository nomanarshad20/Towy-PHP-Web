<?php


namespace App\Traits;

use CoreProc\WalletPlus\Models\WalletType;
trait CreateUserWalletTrait
{

    Public function createUserWallet($user,$walletTypeName="")
    {
        if(isset($user)) {
            $user_type = $user->user_type;
            if($walletTypeName == "" || $walletTypeName == null) {
                if ($user_type == 1)
                    $walletTypeName = "Passenger-Wallet";
                if ($user_type == 2)
                    $walletTypeName = "Driver-Wallet";
                if ($user_type == 3)
                    $walletTypeName = "Admin-Franchise-Wallet";
            }
        }
        $walletType = WalletType::where('name', $walletTypeName)->first();

        if ($walletType && isset($walletType->id)){

            $user->wallets()->create(['wallet_type_id' => $walletType->id]);

        }else {

            $walletType = WalletType::create([
                'name' => $walletTypeName,
                'decimals' => 2, // Set how many decimal points your wallet accepts here. Defaults to 0.
            ]);

            $user->wallets()->create(['wallet_type_id' => $walletType->id]);
        }

        return true;
    }


    Public function passengerUpdatWalletRideFare($booking)
    {
//        $actual_fare            = $booking->actual_fare;
//        $totalCashPayAmount     = $booking->bookingDetail->passenger_total_cash_paid;
        $walletPayAmount        = $booking->bookingDetail->passenger_wallet_paid;
        $extraPayAmount         = $booking->bookingDetail->passenger_extra_cash_paid;
        if (isset($booking->passenger)) {
            $passengerWallet    = $booking->passenger->wallet('Passenger-Wallet');
            if($walletPayAmount > 0)
                $passengerWallet->decrementBalance($walletPayAmount);

            if($extraPayAmount > 1)
                $passengerWallet->incrementBalance($walletPayAmount);

            $data = [
                'passenger_wallet_paid'     => $walletPayAmount,
                'passenger_extra_cash_paid' => $extraPayAmount,
                'wallet_balance'            => $passengerWallet->balance
            ];
        }
    }


    Public function driverUpdatWalletRideFare($booking)
    {
//        $actual_fare            = $booking->actual_fare;
//        $totalCashPayAmount     = $booking->bookingDetail->passenger_total_cash_paid;
        $walletPayAmount        = $booking->bookingDetail->passenger_wallet_paid;
        $extraPayAmount         = $booking->bookingDetail->passenger_extra_cash_paid;
        if (isset($booking->driver)) {
            $passengerWallet    = $booking->driver->wallet('Driver-Wallet');
            if($walletPayAmount > 0)
                $passengerWallet->decrementBalance($walletPayAmount);

            if($extraPayAmount > 1)
                $passengerWallet->incrementBalance($walletPayAmount);

            $data = [
                'passenger_wallet_paid'     => $walletPayAmount,
                'passenger_extra_cash_paid' => $extraPayAmount,
                'wallet_balance'            => $passengerWallet->balance
            ];
        }
    }

    Public function franchiseUpdatWalletRideFare($booking)
    {
        $actual_fare            = $booking->actual_fare;
        $totalCashPayAmount     = $booking->bookingDetail->passenger_total_cash_paid;
        $walletPayAmount        = $booking->bookingDetail->passenger_wallet_paid;
        $extraPayAmount         = $booking->bookingDetail->passenger_extra_cash_paid;
        if (isset($booking->frachise)) {
            $passengerWallet    = $booking->franchise->wallet('Passenger-Wallet');
            if($walletPayAmount > 0)
                $passengerWallet->decrementBalance($walletPayAmount);

            if($extraPayAmount > 1)
                $passengerWallet->incrementBalance($walletPayAmount);

            $data = [
                'passenger_wallet_paid'     => $walletPayAmount,
                'passenger_extra_cash_paid' => $extraPayAmount,
                'wallet_balance'            => $passengerWallet->balance
            ];
        }
    }
}
