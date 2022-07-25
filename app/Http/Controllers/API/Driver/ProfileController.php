<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\SaveProfileRequest;
use App\Services\API\Driver\AuthService;
use App\Services\API\Driver\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public $profileService;
    public $authService;

    public function __construct(ProfileService $profileService,AuthService $authService)
    {
        $this->profileService =  $profileService;
        $this->authService = $authService;
    }

    public function saveProfile(SaveProfileRequest $request)
    {
        if($request->password)
        {
            $this->validate($request,[
                'password' => 'required|min:8|confirmed',
                'old_password' => 'required|min:8'
            ]);
        }

        $profileServiceResponse = $this->profileService->saveProfile($request);

        if($profileServiceResponse['result'] == 'error')
        {
            return makeResponse($profileServiceResponse['result'],$profileServiceResponse['message'],
                $profileServiceResponse['code'] );
        }

        $loginUserResponse = $this->authService->loginUserResponse();

        return makeResponse('success','Profile Updated Successfully',200,$loginUserResponse);
    }
}
