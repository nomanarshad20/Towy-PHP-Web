<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveServiceRate;
use App\Services\Admin\ServiceRateService;
use Illuminate\Http\Request;

class ServiceRateController extends Controller
{

    public $serviceRate;
    public function __construct(ServiceRateService $service)
    {
        $this->serviceRate = $service;
    }

    public function index()
    {
        return $this->serviceRate->index();
    }

    public function save(SaveServiceRate $request)
    {
        return $this->serviceRate->save($request);
    }
}
