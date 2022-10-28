<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateServiceRequest;
use App\Models\Service;
use App\Services\Admin\ServicesService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public $service;
    public function __construct(ServicesService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->index();
    }

    public function create()
    {
        return $this->service->create();
    }

    public function save(CreateServiceRequest $request)
    {
        $service = new Service;
        return $this->service->save($request,$service);
    }

    public function edit($id)
    {
        return $this->service->edit($id);
    }

    public function update(CreateServiceRequest $request)
    {
        $service = Service::find($request->id);
        return $this->service->save($request,$service);
    }

    public function delete(Request $request)
    {
        return $this->service->delete($request);
    }

    public function deleteImage(Request $request)
    {
        return $this->service->deleteImage($request);
    }


}
