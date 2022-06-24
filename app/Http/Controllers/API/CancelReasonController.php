<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\API\CancelReasonService;
use Illuminate\Http\Request;

class CancelReasonController extends Controller
{
    public $cancelService;
    public function __construct(CancelReasonService $cancelService)
    {
        $this->cancelService = $cancelService;
    }

    public function index()
    {
        return $this->cancelService->index();
    }
}
