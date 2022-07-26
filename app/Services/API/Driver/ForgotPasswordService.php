<?php


namespace App\Services\API\Driver;


use App\Models\User;

class ForgotPasswordService
{
    public $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function resetPassword($request)
    {
        $checkType = $this->authService->checkType($request->login);

        if($checkType == 'email')
        {
            $find = User::where('email',$request->login)->first();
        }
        else{
            $find = User::where('mobile_no',$request->login)->first();
        }

        if($find)
        {
            try{
                $find->update(['password'=>bcrypt($request->password)]);

                return makeResponse('success','Password Change Successfully',200);
            }
            catch (\Exception $e)
            {
                return makeResponse('success','Error in Change Password: '.$e,500);
            }


        }
        else{
            return makeResponse('error','Record Not Found',500);
        }
    }
}
