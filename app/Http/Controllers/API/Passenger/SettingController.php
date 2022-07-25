<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Services\API\Passenger\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public $settingService;
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        return  $this->settingService->index();
    }
}
