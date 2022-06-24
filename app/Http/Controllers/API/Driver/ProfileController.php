<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\SaveProfileRequest;
use App\Services\API\Driver\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService =  $profileService;
    }

    public function saveProfile(SaveProfileRequest $request)
    {
        if($request->password)
        {
            $this->validate($request,[
                'password' => 'required|min:8'
            ]);
        }

        return $this->profileService->saveProfile($request);
    }
}
