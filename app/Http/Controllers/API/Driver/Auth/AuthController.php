<?php

namespace App\Http\Controllers\API\Driver\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\DriverLoginRequest;
use App\Http\Requests\API\Driver\DriverRegisterRequest;
use App\Http\Requests\API\Driver\ResendOtpRequest;
use App\Http\Requests\API\Driver\VerifyOtpRequest;
use App\Models\User;
use App\Services\API\Driver\AuthService;
use App\Services\API\Driver\DriverInformationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public $authService;
    public $driverService;

    public function __construct(AuthService $authService,DriverInformationService $driverService)
    {
        $this->authService = $authService;
        $this->driverService =  $driverService;
    }

    public function login(DriverLoginRequest $request)
    {
        try{
            $checkUser = $this->authService->checkUser($request->login,$request->password,$request->user_type);
        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Checking User: '.$e,500);
        }



        if($checkUser['result'] == 'success')
        {
            if(Auth::check())
            {
                $saveUserRecord = $this->authService->updateUserRecord($request);
                $data = $this->authService->loginUserResponse($checkUser['data']);

            }
            else{
                if($checkUser['data'])
                {
                    $loginUser = $this->authService->loginUser($checkUser['data']['id']);
                    $saveUserRecord = $this->authService->updateUserRecord($request);

                    $data = $loginUser['data'];

                }
                else{
                    return makeResponse('error','Server Error Occur. Please Try Again After Some Time',500);
                }

            }
        }
        elseif($checkUser['result'] == 'error'){
            return makeResponse($checkUser['result'],$checkUser['message'],401);
        }


        return makeResponse('success','User Login Successfully',200,$data);
    }

    public function register(DriverRegisterRequest $request)
    {
        DB::beginTransaction();
        try{
            $createUser = $this->authService->createUser($request);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return makeResponse('error','Error in Saving User Record: '.$e,500);
        }


        if($createUser['result'] == 'error')
        {
            DB::rollBack();
            return makeResponse($createUser['result'],$createUser['message'],$createUser['code']);
        }

        $saveUserInformation = $this->driverService->save($request,$createUser['data']);

        if($saveUserInformation['result'] == 'error')
        {
            DB::rollBack();
            return makeResponse($saveUserInformation['result'],$saveUserInformation['message'],$saveUserInformation['code']);
        }


        $data = $this->authService->loginUser($createUser['data']['id']);



        DB::commit();
        return makeResponse('success',$createUser['message'],200,$data['data']);
    }


    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->authService->verifyOtp($request);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        return $this->authService->resendOTP($request);
    }
}
