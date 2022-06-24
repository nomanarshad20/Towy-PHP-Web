<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateVehicleType;
use App\Services\Admin\VehicleTypeService;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{

    public $vehicleTypeService;

    public function __construct(VehicleTypeService $vehicleTypeService) {
        $this->vehicleTypeService = $vehicleTypeService;
    }


    public function index()
    {
        return $this->vehicleTypeService->index();
    }

    public function create()
    {
        return $this->vehicleTypeService->create();
    }

    public function save(CreateVehicleType $request)
    {
        return $this->vehicleTypeService->save($request);
    }

    public function edit($id)
    {
        return $this->vehicleTypeService->edit($id);
    }

    public function update(CreateVehicleType $request)
    {
        return $this->vehicleTypeService->update($request);
    }

    public function delete(Request $request)
    {
        return $this->vehicleTypeService->delete($request);
    }

    public function changeStatus(Request  $request)
    {
        return $this->vehicleTypeService->changeStatus($request);
    }
}
