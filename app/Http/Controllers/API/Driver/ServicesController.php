<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Services\API\Driver\ServicesService;
use Illuminate\Http\Request;

class ServicesController extends Controller
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

    public function save(Request $request)
    {
        return $this->service->save($request);
    }
}
