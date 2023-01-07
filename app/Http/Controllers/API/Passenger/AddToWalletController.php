<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Passenger\AddToWalletRequest;
use App\Services\API\Passenger\AddToWalletService;
use App\Services\API\Passenger\AuthService;
use App\Services\API\Passenger\StripeService;
use App\Traits\CreateUserWalletTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddToWalletController extends Controller
{
    use CreateUserWalletTrait;

    public $addToWallet;
    public $stripeService;
    public $authService;

    public function __construct(AddToWalletService $service, StripeService $stripeService,AuthService $authService)
    {
        $this->addToWallet = $service;
        $this->stripeService = $stripeService;
        $this->authService = $authService;
    }

    public function save(AddToWalletRequest $request)
    {
        $createToken = $this->stripeService->createToken(Auth::user()->name, $request->card_number,
            $request->expiry_month, $request->expiry_year, $request->cvc);

        if ($createToken['type'] == 'error') {
            return makeResponse($createToken['type'], $createToken['message'], 500);
        }

        $createCharge = $this->stripeService->directCharge($createToken['data'], $request->amount);

        if ($createToken['type'] == 'error') {
            return makeResponse($createCharge['type'], $createCharge['message'], 500);
        }


        $saveAmount = $this->passengerWalletUpdate(null, $request->amount, "credit", "stripe", "Add To Wallet Amount by Passenger.");

        $balance = $this->authService->getUserData(Auth::user());

        return makeResponse('success','Add to Wallet Amount Successfully',200,$balance);
    }
}
