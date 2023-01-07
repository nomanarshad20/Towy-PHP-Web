<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Passenger\StripeRequest;
use App\Services\API\Passenger\AuthService;
use App\Services\API\Passenger\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Exception;
use Stripe\Customer;

class StripeController extends Controller
{
    public $stripeService;
    public $authService;

    public function __construct(StripeService $stripeService,AuthService  $authService)
    {
        $this->stripeService = $stripeService;
        $this->authService = $authService;
    }

    public function createCustomer(StripeRequest $request)
    {

        try {
            $token = $this->stripeService->createToken($request->name, $request->number, $request->expiry_month,
                $request->expiry_year, $request->cvc);
        } catch (Exception $e) {
            return makeResponse('error', 'Error in Creating Stripe Token: ' . $e, 500);
        }

        if ($token['type'] == 'error') {
            return makeResponse('error', $token['message'], $token['code']);
        }

        try {
            $customer = $this->stripeService->create_customer($token['data']);
        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Creating Stripe Customer: ' . $e, 500);
        }


        Auth::user()->stripe_customer_id = $customer->id;
        Auth::user()->card_last_4_digit = 'xxxx-xxxx-xxxx-'.$token['card_last_4'];
        Auth::user()->save();

        $data = $this->authService->getUserData(Auth::user());

        return makeResponse('success', 'Your information saved successfully', 200,$data);


    }
}
