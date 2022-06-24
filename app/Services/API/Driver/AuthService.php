<?php


namespace App\Services\API\Driver;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function checkPhoneNumber($mobile_no)
    {
        $check = User::where('mobile_no', $mobile_no)->first();

        return $check;
    }

    public function createUser($request)
    {
        DB::beginTransaction();
        try {
//            $otpCode = mt_rand(1000, 9999);


            $user = User::create([
//                'otp' => $otpCode,
                'mobile_no' => $request->mobile_no,
                'fcm_token' => $request->fcm_token,
                'user_type' => $request->user_type,
//                'steps' => 1,
//                'name' => $request->name,
//                'email' => $request->email,
//                'password' => bcrypt($request->password),
//                'steps' => 3,
//                'referrer' => $request->referrer,

            ]);


            $user->referral_code = "partner-00" . $user->id;

            $user->save();


            DB::commit();
            $response = ['result' => 'success', 'data' => $user,
                'message' => 'Driver Registered Successfully'];

        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'data' => $e];
        }

        return $response;


    }

    public function loginUser($id)
    {
        Auth::loginUsingId($id, true);
        Auth::user()->tokens()->delete();

        $token = Auth::user()->createToken('TOTOBookingApp')->plainTextToken;


        $data = $this->loginUserResponse($token);


        $response = ['result' => 'success', 'data' => $data];

        return $response;

    }

    public function checkUser($mobileNo, $password, $userType)
    {

        if ($password) {
            $credentials = ['mobile_no' => $mobileNo, 'password' => $password];

            if (Auth::attempt($credentials)) {
                if (Auth::user()->user_type == $userType) {
                    Auth::user()->tokens()->delete();
                    $token = Auth::user()->createToken('TOTOBookingApp')->plainTextToken;
                    $response = ['result' => 'success', 'message' => 'Login Successful', 'data' => $token];
                    return $response;
                } else {
                    $response = ['result' => 'error', 'message' => 'Your Phone Number is already registered as a Passenger'];
                    return $response;
                }
            } else {
                $response = ['result' => 'error', 'message' => 'Invalid Credentials'];
                return $response;
            }
        }
        else {
            $checkUserState = User::where('mobile_no', $mobileNo)->whereNUll('password')
                ->where('user_type', $userType)
                ->first();


            if ($checkUserState) {
                $response = $this->checkUserState($checkUserState);
                return $response;
            } else {

                $checkUserState = User::where('mobile_no', $mobileNo)
                    ->where('user_type', $userType)
                    ->first();

                if ($checkUserState) {
                    if ($checkUserState->password) {
                        //some kind of data missing error
                        $response = ['result' => 'error', 'message' => 'Password is a Required Field'];
                        return $response;
                    }


                } else {
                    $response = ['result' => 'error', 'message' => 'Invalid Login Credentials'];
                    return $response;
                }


            }


        }

    }

    public function updateUserRecord($request)
    {
        Auth::user()->fcm_token = $request->fcm_token;

        Auth::user()->save();
    }

    public function loginUserResponse($token = null)
    {

        $driverDocumentStatus = array();
        if (Auth::user()->is_verified == 0) {
            if (Auth::user()->steps == 3) {

                $driverDocumentStatus = [
                    'cnic_front_side' => isset(Auth::user()->driver) ? Auth::user()->driver->cnic_front_side ? 1 : 0 : 0,
                    'cnic_back_side' => isset(Auth::user()->driver) ? Auth::user()->driver->cnic_back_side ? 1 : 0 : 0,
                    'license_front_side' => isset(Auth::user()->driver) ? Auth::user()->driver->license_front_side ? 1 : 0 : 0,
                    'license_back_side' => isset(Auth::user()->driver) ? Auth::user()->driver->license_back_side ? 1 : 0 : 0,
                    'image' => Auth::user()->image ? 1 : 0,
                    'registration_book' => isset(Auth::user()->driver->vehicle) ? Auth::user()->driver->vehicle->registration_book ? 1 : 0 : 0
                ];
            }
        }


        $data = [
            'mobile_no' => Auth::user()->mobile_no,
            'fcm_token' => Auth::user()->fcm_token,
            'user_type' => (int)Auth::user()->user_type,
            'referral_code' => Auth::user()->referral_code,
            'is_step' => (int)Auth::user()->steps,
            'is_verified' => (int)Auth::user()->is_verified,
            'user_id' => Auth::user()->id,
            'city' => isset(Auth::user()->driver->vehicleType) ? Auth::user()->driver->city : 'N/A',
            'vehicle_id' => isset(Auth::user()->driver->vehicle_id) ? Auth::user()->driver->vehicle_id : 'N/A',
            'vehicle_name' => isset(Auth::user()->driver->vehicle) ? Auth::user()->driver->vehicle->name : 'N/A',
            'vehicle_type_id' => isset(Auth::user()->driver->vehicleType) ? Auth::user()->driver->vehicle_type_id : 'N/A',
            'vehicle_type_name' => isset(Auth::user()->driver->vehicleType) ? Auth::user()->driver->vehicleType->name : 'N/A',
            'documents' => (object)$driverDocumentStatus,
            'image' => Auth::user()->image

        ];

        if ($token) {
            $data['token'] = $token;
        }

        return $data;
    }

    public function simpleUserResponse($user)
    {

        $driverDocumentStatus = array();
        if ($user->is_verified == 0) {
            if ($user->steps == 3) {
                $driverDocumentStatus = [
                    'cnic_front_side' => $user->driver->cnic_front_side ? '1' : '0',
                    'cnic_back_side' => $user->driver->cnic_front_side ? '1' : '0',
                    'license_front_side' => $user->driver->license_front_side ? '1' : '0',
                    'license_back_side' => $user->driver->license_back_side ? '1' : '0',
                    'image' => $user->image ? 'done' : 'not-done',
                    'registration_book' => $user->driver->vehicle->registration_book ? '1' : '0'
                ];
            }
        }

        $data = [
            'mobile_no' => $user->mobile_no,
            'fcm_token' => $user->fcm_token,
            'user_type' => (int)$user->user_type,
            'referral_code' => $user->referral_code,
            'is_step' => (int)$user->steps,
            'is_verified' => (int)$user->is_verified,
            'user_id' => $user->id,
            'city' => isset($user->driver) ? $user->driver->city : 'N/A',
            'vehicle_id' => isset($user->driver->vehicle_id) ? $user->driver->vehicle_id : 'N/A',
            'vehicle_name' => isset($user->driver->vehicle) ? $user->driver->vehicle->name : 'N/A',
            'vehicle_type_id' => isset($user->driver->vehicle) ? $user->driver->vehicle_type_id : 'N/A',
            'vehicle_type_name' => isset($user->driver->vehicle) ? $user->driver->vehicleType->name : 'N/A',
            'documents' => (object)$driverDocumentStatus,
            'image' => $user->image
//            'otp' => $user->otp
        ];

        return $data;
    }

    public function verifyOtp($request)
    {
        $findUser = User::where('id', $request->user_id)->where('otp', $request->otp)
            ->first();

        if (!$findUser) {
            DB::rollBack();
//            $response = ['result'=>'error','messasge'=>'Invalid OTP Code'];
//            return $response;
            return makeResponse('error', 'Invalid OTP Code', 404);

        }

        $findUser->otp = null;

        $findUser->steps = 1;

        $findUser->save();

        DB::commit();
//        $response = ['result'=>'success','message'=>'Invalid OTP Code'];
//        return $response;
        return makeResponse('success', 'OTP Verified Successfully', 200);
    }

    public function resendOtp($request)
    {
        DB::beginTransaction();
        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            DB::rollBack();
            return makeResponse('error', 'User ID does not Exist', 401);
        }


        $otpCode = mt_rand(1000, 9999);

        $user->otp = $otpCode;
        $user->save();
        $data = [

            'confirmation_code' => $otpCode,
            'email' => $user->email,
        ];

        DB::commit();
        return makeResponse('success', 'OTP Code is Send to your Registered Mobile Number', '200', $data);
    }

    public function checkUserState($user)
    {

        if ($user->steps == 3) {
            $check = array();
            if (isset($user->driver) && $user->driver->cnic_front_side) {
                $check['cnic_front_side'] = 'done';
            }
            if (isset($user->driver) && $user->driver->cnic_back_side) {
                $check['cnic_back_side'] = 'done';
            }
            if (isset($user->driver) && $user->driver->license_front_side) {
                $check['license_front_side'] = 'done';
            }
            if (isset($user->driver) && $user->driver->license_back_side) {
                $check['license_back_side'] = 'done';
            }
            if (isset($user->driver->vehicle) && $user->driver->vehicle->license_back_side) {
                $check['registration_book'] = 'done';
            }
            if ($user->image) {
                $check['profile_photo'] = 'done';
            }

            $response = ['result' => 'success', 'message' => '', 'data' => $check];
            return $response;
        } elseif ($user->steps == 0) {
            $response = ['result' => 'success', 'message' => 'OTP Required', 'data' => $user];
            return $response;
        } elseif ($user->steps == 1) {
            $response = ['result' => 'success', 'message' => 'User Detail Required', 'data' => $user];
            return $response;
        } else {
            $response = ['result' => 'error', 'message' => 'Invalid Credentials'];
            return $response;
        }

    }

}
