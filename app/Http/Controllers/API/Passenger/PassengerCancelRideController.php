<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CancelRideRequest;
use App\Services\API\Passenger\CancelService;
use Illuminate\Http\Request;

class PassengerCancelRideController extends Controller
{
    public $cancelService;
    public function __construct(CancelService $cancelService)
    {
        $this->cancelService = $cancelService;
    }

    public function cancelRide(CancelRideRequest $request)
    {
        return $this->cancelService->cancelService($request);
    }
}
