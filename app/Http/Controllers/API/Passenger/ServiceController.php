<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Services\API\Passenger\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public $serviceService;

    public function __construct(ServiceService $service)
    {
        $this->serviceService = $service;
    }

    public function index()
    {
        return $this->serviceService->index();
    }
}
