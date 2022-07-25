<?php


namespace App\Services\API;


use App\Services\API\Driver\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Exception;

class UserService
{

    public $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService =  $authService;
    }

    public function saveUserType($request)
    {
        try{
            Auth::user()->user_type = $request->user_type;

            Auth::user()->save();

            $data = $this->authService->loginUserResponse();

            return makeResponse('success','User Type Save Successfully',200,$data);

        }
        catch (Exception $e)
        {
            return makeResponse('error','Error in Saving User Type:  '.$e,500);
        }
    }
}
