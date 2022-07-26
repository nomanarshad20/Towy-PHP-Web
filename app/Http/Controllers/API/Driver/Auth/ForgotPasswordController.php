<?php

namespace App\Http\Controllers\API\Driver\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\ResetPasswordRequest;
use App\Services\API\Driver\ForgotPasswordService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public $forgotPassword;

    public function __construct(ForgotPasswordService $forgotPassword)
    {
        $this->forgotPassword = $forgotPassword;
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->forgotPassword->resetPassword($request);
    }
}
