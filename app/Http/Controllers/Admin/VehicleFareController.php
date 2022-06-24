<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveVehicleFareRequest;
use App\Services\Admin\VehicleFareService;
use App\Services\Admin\VehicleTypeService;
use Illuminate\Http\Request;

class VehicleFareController extends Controller
{
    public $vehicleFareService;
    public function __construct(VehicleFareService $vehicleFareService)
    {
        $this->vehicleFareService = $vehicleFareService;
    }

    public function create()
    {
        return $this->vehicleFareService->create();
    }

    public function save(SaveVehicleFareRequest $request)
    {
        return $this->vehicleFareService->save($request);
    }
}
