<?php


namespace App\Services\Admin\Auth;


use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function loginPage()
    {
        return view('authentication.login');
    }

    public function login($request)
    {
        $credentials = $request->only('email', 'password');

        if ($request->remember_me) {
            $remember = true;
            if (Auth::attempt($credentials, $remember)) {
                //save user status
                $url = route('adminDashboard');
                $result = 'success';


                if (Auth::user()->hasRole('administrator')) {
//                    $url = route('adminDashboard');
                    $message = 'Login Successful';

                }
                else{
                    $message = 'You are not Authorize for making this request';
                    $result = 'error';

                }

                return response()->json(['result' => $result, 'message' => $message,
                    'url' => $url
                ], 200);


            }
            else {
                return response()->json(['result' => 'error', 'message' => 'Invalid Credentials'], 200);
            }
        }
        else {
            if (Auth::attempt($credentials)) {
                $url = route('adminDashboard');
                $result = 'success';
                if (Auth::user()->hasRole('administrator')) {
//                    $url = route('adminDashboard');
                    $message = 'Login Successful';
                }
                else{
                    $message = 'You are not Authorize for making this request';
                    $result = 'error';

                }

                return response()->json(['result' => $result, 'message' => $message,
                    'url' => $url
                ], 200);


            } else {
                return response()->json(['result' => 'error', 'message' => 'Invalid Credentials'], 200);
            }
        }

    }
}
