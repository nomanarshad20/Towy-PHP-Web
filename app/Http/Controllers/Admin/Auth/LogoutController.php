<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\Admin\Auth\LogoutService;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(LogoutService $logoutService)
    {

        return $logoutService->logout();
    }
}
