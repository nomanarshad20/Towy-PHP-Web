<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Services\Admin\Auth\ForgotPasswordService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function forgetPasswordForm(ForgotPasswordService $forgotPasswordService)
    {
        return $forgotPasswordService->forgetPasswordForm();
    }

    public function forgetPassword(ForgotPasswordRequest $request,ForgotPasswordService $forgotPasswordService)
    {
        return $forgotPasswordService->forgetPassword($request);
    }

    public function resetPassword($token,ForgotPasswordService $forgotPasswordService)
    {
        return $forgotPasswordService->resetPassword($token);
    }

    public function changePassword(ResetPasswordRequest $request,ForgotPasswordService $forgotPasswordService)
    {
        return $forgotPasswordService->changePassword($request);
    }
}
