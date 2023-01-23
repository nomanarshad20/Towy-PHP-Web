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

    public function __construct(StripeService  $service)
    {
        $this->stripeService = $service;
    }

    public function createConnectAccountLink(Request  $request)
    {
        $this->stripeService->createConnectAccountLink($request);
    }
}
