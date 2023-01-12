<?php


namespace App\Services\API\Driver;


use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\CreateUserWalletTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mime\Email;
use App\Models\DriverService;

class AuthService
{
    use CreateUserWalletTrait;

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


            $checkType = $this->checkType($request->login);

            $mobile = $email = null;

            if ($checkType == 'mobile_no') {
                $mobile = $request->login;
                $email = null;
//                $otp = null;
                $checkUser = User::where('mobile_no', $mobile)->where('user_type',2)->first();
                if ($checkUser) {
                    $response = ['result' => 'error', 'message' => 'This mobile number is already is in use', 'code' => 422];
                    return $response;
                }
            } elseif ($checkType == 'email') {
                $mobile = null;
                $email = $request->login;
//                $otpCode = mt_rand(1000, 9999);

                $checkUser = User::where('email', $email)->where('user_type',2)->first();
                if ($checkUser) {
                    $response = ['result' => 'error', 'message' => 'The email is already is in use', 'code' => 422];
                    return $response;
                }
            } else {
                $response = ['result' => 'error', 'message' => 'Please Enter Valid Mobile No or Email', 'code' => 422];
                return $response;
            }


            $user = User::create([
//                'otp' => $otpCode,
                'mobile_no' => $mobile,
                'fcm_token' => $request->fcm_token,
                'user_type' => $request->user_type,
//                'steps' => 1,
//                'name' => $request->name,
                'email' => $email,
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
            $response = ['result' => 'error', 'data' => $e, 'code' => 500];
        }

        return $response;


    }

    public function loginUser($id)
    {
        Auth::loginUsingId($id, true);
        Auth::user()->tokens()->delete();

        $token = Auth::user()->createToken('TowyBookingApp')->plainTextToken;


        $data = $this->loginUserResponse($token);


        $response = ['result' => 'success', 'data' => $data];

        return $response;

    }

    public function checkUser($login, $password, $userType)
    {
        $checkLoginType = $this->checkType($login);


        if ($password) {

            if ($checkLoginType == 'mobile_no') {
                $credentials = ['mobile_no' => $login, 'password' => $password];

                $checkForUser =  User::where('mobile_no',$login)
//                    ->where('password',$password)
                    ->whereIn('user_type',[2,4])->first();


                if($checkForUser)
                {
                    dd(Hash::check($checkForUser->password,$password),!Hash::check($checkForUser->password,$password));
                    if(!Hash::check($checkForUser->password,$password))
                    {
                        $response = ['result' => 'error', 'message' => 'Invalid Credentials'];
                        return $response;
                    }

                }

            } elseif ($checkLoginType == 'email') {
                $credentials = ['email' => $login, 'password' => $password];
                $checkForUser =  User::where('email',$login)
//                    ->where('password',$password)
                        ->whereIn('user_type',[2,4])->first();
                if($checkForUser)
                {
                    if(!Hash::check($checkForUser->password,$password))
                    {
                        $response = ['result' => 'error', 'message' => 'Invalid Credentials'];
                        return $response;
                    }

                }
            } else {
                $response = ['result' => 'error', 'message' => 'Please Enter Valid Mobile No or Email', 'code' => 422];
                return $response;
            }


            if ($checkForUser) {
                    Auth::loginUsingId($checkForUser->id);
//                if (Auth::user()->user_type == 2 || Auth::user()->user_type == 4) {
                    Auth::user()->tokens()->delete();
                    $token = Auth::user()->createToken('TowyBookingApp')->plainTextToken;
                    $response = ['result' => 'success', 'message' => 'Login Successful', 'data' => $token];
                    return $response;
//                }
//                else {
//                    $response = ['result' => 'error', 'message' => 'Your Phone Number is already registered as a Passenger'];
//                    return $response;
//                }
            } else {
                $response = ['result' => 'error', 'message' => 'Invalid Credentials'];
                return $response;
            }
        }
        else {
            if ($checkLoginType == 'mobile_no') {
                $checkUserState = User::where('mobile_no', $login)
                    ->whereNUll('password')
                    ->whereIn('user_type', [2,4])
                    ->first();
            } elseif ($checkLoginType == 'email') {
                $checkUserState = User::where('email', $login)
                    ->whereNUll('password')
                    ->whereIn('user_type', [2,4])
                    ->first();
            }


            if ($checkUserState) {
                $response = $this->checkUserState($checkUserState);
                return $response;
            } else {

                if ($checkLoginType == 'mobile_no') {
                    $checkUserState = User::where('mobile_no', $login)
                        ->whereIn('user_type', [2,4])
                        ->first();

                } elseif ($checkLoginType == 'email') {

                    $checkUserState = User::where('email', $login)
                        ->whereIn('user_type', [2,4])
                        ->first();

                }


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
            if (Auth::user()->steps == 2) {

                $driverDocumentStatus = [
                    'vehicle_insurance' => isset(Auth::user()->driver) ? Auth::user()->driver->vehicle_insurance ? 1 : 0 : 0,
                    'vehicle_inspection' => isset(Auth::user()->driver) ? Auth::user()->driver->vehicle_inspection ? 1 : 0 : 0,
                    'drivers_license' => isset(Auth::user()->driver) ? Auth::user()->driver->drivers_license ? 1 : 0 : 0,
                    'image' => Auth::user()->image ? 1 : 0,
                    'ssn' => Auth::user()->driver->ssn ? 1 : 0,
                    'registration_book' => isset(Auth::user()->driver->vehicle) ? Auth::user()->driver->vehicle->registration_book ? 1 : 0 : 0
                ];
            }
        }

        $services = array();
        if (Auth::user()->user_type == 4) {
            foreach (Auth::user()->service as $service) {
                $services[] = ['service_id' => $service->service_id, 'service_name' => $service->service->name,
                    'service_image' => isset($service->service) ? $service->service->image ?  $service->service->image:'':''];
            }
        }

        // Get Driver Wallet
//        $passengerWallet = Auth::user()->wallet('Driver-Wallet');
//
//        if (!isset($passengerWallet) || $passengerWallet == null) {
//            // Create New Wallet For Driver
//            $this->createUserWallet(Auth::user(), 'Driver-Wallet');
//            //Again Get Driver Wallet
//            $passengerWallet = Auth::user()->wallet('Driver-Wallet');
//        }

        $driverStatus = null;
        if (isset(Auth::user()->driverCoordinate)) {

            $driverStatus = Auth::user()->driverCoordinate->status;
//            if( Auth::user()->driverCoordinate->status == 0 )
//            {
//                $driverStatus = 'offline';
//            }
//            elseif(Auth::user()->driverCoordinate->status == 1)
//            {
//                $driverStatus = 'online';
//            }
//            elseif(Auth::user()->driverCoordinate->status == 2)
//            {
//                $driverStatus = 'onride';
//            }
//            elseif(Auth::user()->driverCoordinate->status == 3)
//            {
//                $driverStatus = 'ride_request';
//            }
//
        }


        $balance = $this->driverWalletBalance(Auth::user()->id);

        $rating = 0;
        if (isset(Auth::user()->rating)) {
            $rating = Auth::user()->rating->avg('rating');
            if ($rating == null) {
                $rating = 0;
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
            'image' => Auth::user()->image,
            'driver_wallet_balance' => $balance,
            'availability_status' => $driverStatus,
            'name' => Auth::user()->name,
            'rating' => $rating,
            'vehicle_registration_number' => isset(Auth::user()->driver->vehicle) ? Auth::user()->driver->vehicle->registration_number : null,
            'email' => Auth::user()->email,
            'first_name' => Auth::user()->first_name,
            'last_name' => Auth::user()->last_name,
            'vehicle_model_year' => isset(Auth::user()->driver->vehicle) ? Auth::user()->driver->vehicle->model_year : null,
            'ssn' => isset(Auth::user()->driver) ? Auth::user()->driver->ssn : null,
            'services' => $services,
            'stripe_customer_id' => Auth::user()->stripe_customer_id
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
            'image' => $user->image,
//            'otp' => $user->otp
        ];

        return $data;
    }

    public function verifyOtp($request)
    {
        $findUser = User::where('email', $request->email)->where('otp', $request->otp)
            ->first();

        if (!$findUser) {
            DB::rollBack();
//            $response = ['result'=>'error','messasge'=>'Invalid OTP Code'];
//            return $response;
            return makeResponse('error', 'Invalid OTP Code', 401);

        }

        $findUser->otp = null;

//        $findUser->steps = 1;

        $findUser->save();

        DB::commit();
//        $response = ['result'=>'success','message'=>'Invalid OTP Code'];
//        return $response;
        return makeResponse('success', 'OTP Verified Successfully', 200);
    }

    public function resendOtp($request)
    {
        DB::beginTransaction();
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            DB::rollBack();
            return makeResponse('error', 'Email does not Exist', 401);
        }


        $otpCode = mt_rand(100000, 999999);

        $user->otp = $otpCode;
        $user->save();
        $data = [
            'otp' => $otpCode,
            'email' => $request->login
        ];

        Notification::send($user, new ResetPasswordNotification($data));


        DB::commit();
        return makeResponse('success', 'OTP Code is Send to your Email Address', '200', $data);
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

    public function checkType($login)
    {
        if (is_numeric($login)) {
            return 'mobile_no';
        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
    }

    public function sendOTP($request)
    {
        DB::beginTransaction();
//        $user = User::where('email', $request->login)->first();
//
//        if (!$user) {
//            DB::rollBack();
//            return makeResponse('error', 'User Email does not Exist In Our System', 401);
//        }


        $otpCode = mt_rand(100000, 999999);

//        $user->otp = $otpCode;
//        $user->save();
        $data = [

            'otp' => $otpCode,
            'email' => $request->login,
        ];

        Notification::route('mail', $request->login)->notify(new EmailVerificationNotification($data));


        DB::commit();
        return makeResponse('success', 'OTP Code is Send on your email address', '200', $data);

    }

}
