<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateVehcileRequest;
use App\Services\Admin\VehicleService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public $vehicleService;

    public function __construct(VehicleService $vehicleService) {
        $this->vehicleService = $vehicleService;
    }

    public function index()
    {
        return $this->vehicleService->index();
    }

    public function create()
    {
        return $this->vehicleService->create();
    }

    public function save(CreateVehcileRequest $request)
    {
        return $this->vehicleService->save($request);
    }

    public function edit($id)
    {
        return $this->vehicleService->edit($id);
    }

    public function update(CreateVehcileRequest $request)
    {
        return $this->vehicleService->update($request);
    }

    public function delete(Request $request)
    {
        return $this->vehicleService->delete($request);
    }

    public function deleteImage(Request $request)
    {
        return $this->vehicleService->deleteImage($request);
    }

//    public function changeStatus(Request $request)
//    {
//        return $this->vehicleService->changeStatus($request);
//    }
}
