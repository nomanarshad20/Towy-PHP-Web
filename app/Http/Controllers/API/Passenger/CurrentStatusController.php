<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Services\API\Passenger\CurrentStatusService;
use Illuminate\Http\Request;

class CurrentStatusController extends Controller
{

    public $currentStatusService;
    public function __construct(CurrentStatusService $currentStatusService)
    {
        $this->currentStatusService = $currentStatusService;
    }

    //
    public function index()
    {
        return $this->currentStatusService->index();
    }
}
