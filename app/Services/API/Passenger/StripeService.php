<?php


namespace App\Services\API\Passenger;


use Illuminate\Support\Facades\Auth;
use Stripe\Customer;
use Stripe\Stripe;

class StripeService
{
    public $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );
        Stripe::setApiKey(env('STRIPE_SECRET'));

    }

    public function createToken($name, $number, $expiry_month, $expiry_year, $cvc)
    {
        try {
            $token = $this->stripe->tokens->create([
                'card' => [
                    'number' => $number,
                    'exp_month' => $expiry_month,
                    'exp_year' => $expiry_year,
                    'cvc' => $cvc,
                ],
            ]);

            $response = ['type' => 'success', 'data' => $token->id];
            return $response;
        } catch (\Stripe\Error\InvalidRequest $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\Authentication $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\ApiConnection $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Exception\CardException $e) {
            $response = ['type' => 'error', 'message' => $e->getError()->message];
            return $response;
        } catch (Exception $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        }

    }

    public function create_customer($stripe_token)
    {
        $stripeEmail = Auth::user()->email;
        $customerId = Customer::create([
            'email' => $stripeEmail,
            'source' => $stripe_token,
        ]);

        return $customerId;
    }

    public function holdAmount($bookingRecord)
    {
        try {
            $amount = $bookingRecord['estimated_fare'];

            $charge = \Stripe\Charge::create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'description' => 'Towy Charge for Booking ID: ' . $bookingRecord['booking_unique_id'],
                'customer' => Auth::user()->stripe_customer_id,
                'capture' => false,
            ]);

            return $charge->id;

        } catch (\Stripe\Error\InvalidRequest $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\Authentication $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\ApiConnection $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Exception\CardException $e) {
            $response = ['type' => 'error', 'message' => $e->getError()->message];
            return $response;
        } catch (Exception $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        }

    }

    public function releasingAmount( $stripe_charge_id)
    {

        $release = $this->stripe->refunds->create([
            'charge' => $stripe_charge_id,

        ]);


        return $release;
    }

    public function captureFund($amount, $stripe_charge_id)
    {
        try {
            $charge = $this->stripe->charges->capture(
                $stripe_charge_id,
                ['amount' => $amount * 100]
            );

            return $charge;


        } catch (\Stripe\Error\InvalidRequest $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\Authentication $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\ApiConnection $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Exception\CardException $e) {
            $response = ['type' => 'error', 'message' => $e->getError()->message];
            return $response;
        } catch (Exception $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        }


    }

    public function charge($bookingRecord,$customerID = null,$amount = null)
    {
        try {

            if(!$customerID)
            {
                $customerID = Auth::user()->stripe_customer_id;
            }

            if(!$amount)
            {
                $amount = $bookingRecord->actual_fare;
            }



            $charge = \Stripe\Charge::create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'description' => 'Towy Charge for Booking ID: ' . $bookingRecord->booking_unique_id,
                'customer' => $customerID,
            ]);

            return $charge->id;

        } catch (\Stripe\Error\InvalidRequest $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\Authentication $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Error\ApiConnection $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        } catch (\Stripe\Exception\CardException $e) {
            $response = ['type' => 'error', 'message' => $e->getError()->message];
            return $response;
        } catch (Exception $e) {
            $response = ['type' => 'error', 'message' => $e->getMessage()];
            return $response;
        }

    }


}
