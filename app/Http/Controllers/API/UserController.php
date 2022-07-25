<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\API\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function saveUserType(Request $request)
    {
        return $this->userService->saveUserType($request);
    }
}
