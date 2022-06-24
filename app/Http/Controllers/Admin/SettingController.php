<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public $settingService;
    public function __construct(SettingService $service)
    {
        $this->settingService = $service;
    }

    public function index()
    {
        return $this->settingService->index();
    }

    public function save(Request $request)
    {
        return $this->settingService->save($request);
    }
}
