<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\CancelRideRequest;
use App\Services\API\Driver\CancelService;
use Illuminate\Http\Request;

class DriverCancelRideController extends Controller
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
