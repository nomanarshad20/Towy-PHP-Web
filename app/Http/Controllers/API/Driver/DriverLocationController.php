<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\SaveDriverLocationRequest;
use App\Services\API\Driver\DriverLocationService;
use Illuminate\Http\Request;

class DriverLocationController extends Controller
{
    public $driverLocationService;

    public function __construct(DriverLocationService $driverLocationService)
    {
        $this->driverLocationService = $driverLocationService;
    }

    public function save(SaveDriverLocationRequest $request)
    {
        return $this->driverLocationService->saveLocation($request);
    }
}
