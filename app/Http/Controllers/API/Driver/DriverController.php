<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\DriverConnectionStatusRequest;
use App\Services\API\Driver\AuthService;
use App\Services\API\Driver\DriverService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public $driverService;
    public $authService;

    public function __construct(DriverService $driverService, AuthService $service)
    {
        $this->driverService = $driverService;
        $this->authService = $service;
    }

    public function changeDriverOnlineStatus(DriverConnectionStatusRequest $request)
    {
        $saveStatus = $this->driverService->changeStatus($request);

        if ($saveStatus['result'] == 'error') {
            return makeResponse($saveStatus['result'], $saveStatus['message'], $saveStatus['code']);
        }

        $userResponse = $this->authService->loginUserResponse();

        return makeResponse($saveStatus['result'], $saveStatus['message'], $saveStatus['code'], $userResponse);
    }

    public function getCurrentStatus()
    {
        $driverStatus = $this->driverService->getStatus();

        if ($driverStatus['result'] == 'error') {
            return makeResponse($driverStatus['result'], $driverStatus['message'], $driverStatus['code']);
        }

        if (isset($driverStatus['data'])) {
            $data = [
                'booking' => $driverStatus['data'],
                'user' => $this->authService->loginUserResponse()
            ];
        } else {
            $data = [
                'booking' => null,
                'user' => $this->authService->loginUserResponse()
            ];
        }

        return makeResponse($driverStatus['result'], $driverStatus['message'], $driverStatus['code'], $data);
    }
}
