<?php

namespace App\Http\Controllers\API\Passenger\Auth;

use App\Http\Controllers\Controller;

//use App\Http\Requests\API\PassengerRegisterRequest;
use App\Http\Requests\API\Passenger\RegisterRequest;
use App\Http\Requests\API\Passenger\SocialRegisterRequest;
use App\Models\User;
use App\Services\API\Passenger\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

class AuthController extends Controller
{
    public $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function checkPhoneNumber(Request $request)
    {
        try {
            $phoneResponse = $this->authService->checkPhoneNumber($request->mobile_no);
            //dd($phoneResponse);
            if ($phoneResponse && $phoneResponse != null) {
                $data = ['user_exist' => true];
                return makeResponse('success', "User Exist.", 200, $data);
            } else {
                $data = ['user_exist' => false];
                return makeResponse('error', 'User Not Exist. ', 401, $data);
            }
        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Checking Phone Number: ' . $e, 500);
        }
    }

    // PassengerLoginRequest
    public function login(Request $request)
    {
        try {

            $checkUser = $this->authService->userLoginCheck($request->mobile_no, $request->user_type);
            //dd($checkUser);
            if ($checkUser['result'] == 'success') {
                if ($checkUser['data']) {
                    //dd($checkUser['data']);
                    //$loginUser = $this->authService->loginUser($checkUser['data']->id);
                    $saveUserRecord = $this->authService->updateUserRecord($request);
                    return makeResponse('success', $checkUser['message'], 200, $checkUser['data']);
                } else {
                    return makeResponse('error', 'Server Error Occur. Please Try Again After Some Time', 500);
                }

            } elseif ($checkUser['result'] == 'error') {
                return makeResponse($checkUser['result'], $checkUser['message'], 401);
            }


        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Checking User: ' . $e, 500);
        }


    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {

            $createUser = $this->authService->createUser($request);

            if ($createUser['result'] == 'error') {

                DB::rollBack();
                return makeResponse('error', 'Error in Saving User Record: ' . $createUser['data'], 500);

            } else {

                DB::commit();
                return makeResponse('success', $createUser['message'], 200, $createUser['data']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Saving User Record: ' . $e, 500);
        }

        //$data = $this->authService->loginUser($createUser['data']['id']);

    }


    public function socialLoginMobile(SocialRegisterRequest $request)
    {

        try {

            $responseFromService = $this->authService->socialLogin($request);

            if ($responseFromService && $responseFromService['result'] == "success") {
                return makeResponse('success', $responseFromService['message'], 200, $responseFromService['data']);
            } else
                return makeResponse('error', $responseFromService['message'], $responseFromService['code']);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Social Login: ' . $e, 500);
        }

    }

    public function userDataUpdate(Request $request)
    {

        try {
            $responseFromService = $this->authService->updateUserInfo($request);

            if ($responseFromService && $responseFromService['result'] == "success") {
                return makeResponse('success', $responseFromService['message'], 200, $responseFromService['data']);
            } else
                return makeResponse('error', $responseFromService['message'], 401);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error User Data Update: ' . $e, 500);
        }
    }

    public function logout(Request $request)
    {

        try {
            $responseFromService = $this->authService->userLogout($request);

            if ($responseFromService && $responseFromService['result'] == "success")
                return makeResponse('success', $responseFromService['message'], 200, $responseFromService['data']);
            else
                return makeResponse('error', $responseFromService['message'], 401);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in verify OTP: ' . $e, 500);
        }

    }

    public function passwordUpdate(Request $request)
    {

        try {
            $responseFromService = $this->authService->updatePassword($request);

            if ($responseFromService && $responseFromService['result'] == "success")
                return makeResponse('success', $responseFromService['message'], 200, $responseFromService['data']);
            else
                return makeResponse('error', $responseFromService['message'], 401);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in verify OTP: ' . $e, 500);
        }

    }

    public function forgetPassword(Request $request)
    {


        try
        {
            $responseFromService = $this->authService->forgetPassword($request);

            if ($responseFromService && $responseFromService['result'] == "success")
                return makeResponse('success', $responseFromService['message'], 200, $responseFromService['data']);
            else
                return makeResponse('error', $responseFromService['message'], 401);

        } catch (\Exception $e) {
            return makeResponse('error', 'Error in verify OTP: ' . $e, 500);
        }

    }


    /*
    public function checkPhoneNumberOld(PassengerRegisterRequest $request, AuthService $authService)
    {

        $createUser = array();
        $loginUserResponse = array();

        try {
            $phoneResponse = $authService->checkPhoneNumber($request->mobile_no);

        }
        catch (\Exception $e) {
            return makeResponse('error', 'Error in Checking Phone Number: ' . $e, 500);
        }


        if ($phoneResponse) {
            $loginUserResponse = $authService->loginUser($phoneResponse->id,'no');
            $message = 'User Login Successfully';
        }
        else {
            $createUser = $authService->createUser($request);
            //dd($createUser);
            $message = $createUser['message'];
        }


        if(sizeof($createUser) > 0)
        {
            try{
                $loginUserResponse = $authService->loginUser($createUser['data']->id);
            }
            catch(\Exception $e){
                return makeResponse('error', $createUser['data'], 500);

            }
        }

        if (Auth::check()) {
            return makeResponse('success',$message,200,$loginUserResponse['data'],$loginUserResponse['token']);
        }
        else{
            return makeResponse('error','Error in Login User: ',500);
        }


    }

    public function resendOTPCode(Request $request, AuthService $authService)
    {
        try
        {
            $responseFromService    =   $authService->resendOTP($request);

            if ($responseFromService && $responseFromService['result'] == "success")
                return makeResponse('success',$responseFromService['message'],200,$responseFromService['data']);
            else
                return makeResponse('error',$responseFromService['message'],401);

        }catch (\Exception $e) {

            return makeResponse('error', 'Error in Resend OTP: ' . $e, 500);
        }
    }

    public function verifyOTPCode(Request $request, AuthService $authService)
    {

        try
        {
            $responseFromService = $authService->verifyOTP($request);

            if ($responseFromService && $responseFromService['result'] == "success")
                return makeResponse('success',$responseFromService['message'],200,$responseFromService['data']);
            else
                return makeResponse('error',$responseFromService['message'],401);

        }catch (\Exception $e) {
            return makeResponse('error', 'Error in verify OTP: ' . $e, 500);
        }

    }
    */

}
