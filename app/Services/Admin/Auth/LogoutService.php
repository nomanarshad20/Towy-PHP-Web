<?php


namespace App\Services\Admin\Auth;


use Illuminate\Support\Facades\Auth;

class LogoutService
{
    public function logout()
    {
        Auth::logout();

        return redirect()->route('loginPage');
    }
}
