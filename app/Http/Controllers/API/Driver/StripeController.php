<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Passenger\StripeRequest;
use App\Services\API\Driver\AuthService;
use App\Services\API\Passenger\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Exception;

class StripeController extends Controller
{
    public $stripeService;

    public function __construct(StripeService $service)
    {
        $this->stripeService = $service;
    }

    public function createConnectAccount(Request $request)
    {
        $account = $this->stripeService->createConnectAccount($request);

        if ($account['type'] == 'error') {
            return makeResponse('error', $account['message'], 500);
        }

        Auth::user()->stripe_customer_id = $account['data'];
        Auth::user()->save();

        $accountLink = $this->stripeService->createConnectAccountLink();

        if ($accountLink['type'] == 'error') {
            return makeResponse("error", $accountLink['message'], 500);

        }


        return makeResponse("success", 'User account created successfully.', 200, $accountLink['data']);
    }

    public function refreshConnectAccountLink(Request $request)
    {
        $accountLink = $this->stripeService->createConnectAccountLink($request);

        if ($accountLink['type'] == 'error') {
            return makeResponse("error", $accountLink['message'], 500);

        }


        return redirect($accountLink['data']['url']);
    }


}
