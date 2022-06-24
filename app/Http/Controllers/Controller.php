<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // wallet store credit/debit transaction
    public function save_to_wallet($wallet_data)
    {
        $user       = auth()->user();
        $amount     = 0;
        $status     = 1;
        $type       = 'credit';
        if(isset($wallet_data['status']))
            $status = $wallet_data['status'];
        if(isset($wallet_data['type']))
            $type   = $wallet_data['type'];
        if(isset($wallet_data['amount']))
            $amount = $wallet_data['amount'];
        $desc       = "Wallet updated with $type amount: $amount";
        if(isset($wallet_data['description']))
            $desc   = $wallet_data['description'];

        if($amount > 0 && isset($user)){
            $user_id    =  $user->id;

            $data       =  [
                            'user_id'     => $user_id,
                            'type'        => $type,
                            'amount'      => $amount,
                            'description' => $desc,
                            'status'      => $status
                        ];
            $wallet     = $user->transactions()->create($data);

            return $wallet;
        }else{
            return false;
        }
    }

    //check available
    public function checkBalance()
    {
        $user = auth()->user();
        return $user->balance();
    }

    //check all transaction
    public function getAllTransactions()
    {
        $user = auth()->user();
        return $user->transactions;
    }
}
