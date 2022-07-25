<?php


namespace App\Services\API;


use App\Traits\CreateUserWalletTrait;

class UpdateWalletService
{
    use CreateUserWalletTrait;

    public function updateFareWallets($booking)
    {
        if ($booking) {
            try {
                //Update Passenger Wallet
                $updatePassengerWallet = $this->passengerUpdateWalletRideFare($booking);
                //dd($updatePassengerWallet['wallet_balance']);
                $data[] = array();
                $wallet_balance = 0;
                if (isset($updatePassengerWallet['wallet_balance'])) {
                    $wallet_balance = $updatePassengerWallet['wallet_balance'];
                }
                //Update Driver Wallet
                $updateDriverWallet = $this->driverUpdateWalletRideFare($booking);
                //dd($updateDriverWallet);
                if (isset($updateDriverWallet)) {
                    $data = [
                        "passenger_wallet_paid" => $updateDriverWallet['passenger_wallet_paid'],
                        "passenger_total_cash_paid" => $updateDriverWallet['passenger_total_cash_paid'],
                        "passenger_extra_cash_paid" => $updateDriverWallet['passenger_extra_cash_paid'],
                        "driver_wallet_balance" => $updateDriverWallet['wallet_balance'],
                        'passenger_wallet_balance' => $wallet_balance,
                    ];
                }
                $response = ['result' => 'success', 'data' => $data, 'message' => 'Wallet and booking updated Successfully'];
                return $response;
            } catch (\Exception $e) {
                $response = ['result' => 'error', 'code' => 500, 'message' => 'Error in update passenger wallet: ' . $e];
                return $response;
            }
        } else {
            $response = ['result' => 'error', 'code' => 404, 'message' => 'Record Not Found'];
            return $response;
        }
    }



}
