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

            $response = ['type' => 'success','data'=>$token->id];
            return  $response;
        } catch (\Stripe\Error\InvalidRequest $e) {
            $response = ['type' => 'error','message'=>$e->getMessage()];
            return  $response;
        } catch (\Stripe\Error\Authentication $e) {
            $response = ['type' => 'error','message'=>$e->getMessage()];
            return  $response;
        } catch (\Stripe\Error\ApiConnection $e) {
            $response = ['type' => 'error','message'=>$e->getMessage()];
            return  $response;
        } catch (\Stripe\Exception\CardException $e) {
            $response = ['type' => 'error','message'=>$e->getError()->message];
            return  $response;
        } catch (Exception $e) {
            $response = ['type' => 'error','message'=>$e->getMessage()];
            return  $response;
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


}
