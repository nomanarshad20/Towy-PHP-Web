<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Services\API\Driver\CalculateFareService;
use Illuminate\Http\Request;

class CalculateFareController extends Controller
{

    public $calculateFare;

    public function __construct(CalculateFareService $calculateFareService)
    {
        $this->calculateFare = $calculateFareService;
    }

    public function calculateFare($request)
    {

    }
}
