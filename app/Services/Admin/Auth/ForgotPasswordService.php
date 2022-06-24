<?php


namespace App\Services\Admin\Auth;


use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordService
{
    public function forgetPasswordForm()
    {
        return view('authentication.forgot_password');
    }

    public function forgetPassword($request)
    {
        $user = User::where('email', $request->email)->first();

        if($user)
        {
            if(!$user->hasRole('administrator'))
            {
                return response()->json(['result' => 'error', 'message' => 'You are not allowed to perform this action!'], 200);
            }
            $confirmation_code = Str::random(30);
            PasswordReset::insert(['email' => $request->email,
                'token' => $confirmation_code]);

            $user->sendPasswordResetNotificationAdmin($confirmation_code);
            return response()->json(['result' => 'success', 'message' => 'We just emailed a link to reset your password'], 200);
        }
        else {
            return response()->json(['result' => 'error', 'message' => 'Email does not exist!'], 200);
        }
    }

    public function resetPassword($token)
    {
        $data = PasswordReset::where('token', '=', $token)->first();

        if ($data) {
            PasswordReset::where('token', '=', $token)->delete();
            PasswordReset::where('email', '=', $data->email)->delete();

//            return redirect()->route('home')
//                ->with(['openUpdatePasswordForm' => 'updatePasswordModal', 'email' => $data->email]);
            return view('authentication.reset_password',compact('data'));
        } else {
            return redirect()->route('loginPage')
                ->with(['error' => 'Your token has been expired. Please request again']);
        }
    }

    public function changePassword($request)
    {
        $user = User::where('email', '=', $request->email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['result' => 'success', 'message' => 'Password successfully changed'], 200);
        } else {
            return response()->json(['result' => 'error', 'message' => 'Email is not found in database'], 200);
        }
    }

}
