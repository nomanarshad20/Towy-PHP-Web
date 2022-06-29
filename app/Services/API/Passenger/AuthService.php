<?php


namespace App\Services\API\Passenger;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use App\Traits\CreateUserWalletTrait;
//use Illuminate\Support\Facades\Validator;
use Validator;

class AuthService
{
    use CreateUserWalletTrait;

    public function checkPhoneNumber($mobile_no)
    {
        $check = User::where('mobile_no', $mobile_no)->where('user_type', 1)->first();

        return $check;
    }

    /*public function loginUser($id)
    {

        Auth::loginUsingId($id, true);

        Auth::user()->tokens()->delete();

        $token = Auth::user()->createToken('TOTOBookingApp')->plainTextToken;

        $data = $this->getUserData(Auth::user(),$token);


        $response = ['result' => 'success', 'data' => $data];

        return $response;

    }
    */

    public function userLoginCheck($mobileNo, $password, $userType)
    {
        $credentials = ['mobile_no' => $mobileNo, 'password' => $password];

        if (Auth::attempt($credentials)) {
            if (Auth::user()->user_type == $userType) {
                Auth::user()->tokens()->delete();
                $token = Auth::user()->createToken('TOTOBookingApp')->plainTextToken;
                $data = $this->getUserData(Auth::user(), $token);
                $response = ['result' => 'success', 'message' => 'Login Successful', 'data' => $data];
                return $response;
            } else {
                $response = ['result' => 'error', 'message' => 'Your Phone Number is already registered as a Driver'];
                return $response;
            }
        } else {
            $response = ['result' => 'error', 'message' => 'Invalid Credentials'];
            return $response;
        }


    }

    public function updateUserRecord($request)
    {
        Auth::user()->fcm_token = $request->fcm_token;

        Auth::user()->save();
    }


    public function createUser($request)
    {
        DB::beginTransaction();
        try {
//            $otpCode = mt_rand(1000, 9999);
            $user = User::create([
                'mobile_no' => $request->mobile_no,
                'fcm_token' => $request->fcm_token,
                'user_type' => $request->user_type,
                'steps' => 1
//                'otp' => $otpCode,
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request['password']);
            $user->referral_code = "passenger-00" . $user->id;
            $user->is_verified = 1;
            $user->save();

            Auth::loginUsingId($user->id, true);

            $token = Auth::user()->createToken('TOTOBookingApp')->plainTextToken;

            $data = $this->getUserData($user, $token);

            DB::commit();
            $response = ['result' => 'success', 'data' => $data, 'message' => 'Passenger Registered Successfully'];

        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'data' => $e];
        }

        return $response;

    }


    // Social Login
    public function socialLogin($request)
    {

        DB::beginTransaction();
        try {

            $user = User::where('email', $request->email)->orWhere('social_uid', $request->social_uid)->first();
            if (isset($user) && !empty($user)) {
                if ($user->status != 1 || $request->user_type != $user->user_type) {
                    $message = "Your Account is disabled. Kindly contact To our support!";
                    if ($request->user_type == 1 && $request->user_type != $user->account_type)
                        $message = "Your are not allowed to login, email already register as Driver.";
                    if ($request->user_type == 2 && $request->user_type != $user->account_type)
                        $message = "Your are not allowed to login, email already register as Passenger.";

                    $response = ['result' => 'error', 'message' => $message];

                } else {
                    $user->fcm_token = $request->fcm_token;
                    $user->save();
                }
            } else if (!isset($user) && $user == "") {
//                $allowed_platforms = [
//                    'facebook',
//                    'google',
//                    'apple'
//                    //'instagram'
//                ];
//                if (!in_array($request->provider, $allowed_platforms, true)) {
//                      $message = "Wrong Platform or Platform not allowed!";
//                        $response       = ['result' => 'error', 'message' => $message];
//                }else{
//                }

                $user = User::create([
                    'fcm_token' => $request->fcm_token,
                    'email' => $request->email,
                    'user_type' => $request->user_type,
                    'steps' => 1
                ]);
                $user->referral_code = "passenger-00" . $user->id;
//                $user->fcm_token            = $request->fcm_token;
//                $user->email                = $request->email;
                $user->provider = $request->provider;
                $user->social_uid = $request->social_uid;
                $user->is_verified = 1;
                $user->email_verified_at = date("Y-m-d h:i:s");
                $user->save();

            }

            DB::commit();
            Auth::loginUsingId($user->id, true);
            $token = Auth::user()->createToken('TOTOBookingApp')->plainTextToken;
            $data = $this->getUserData($user, $token);
            $response = ['result' => 'success', 'data' => $data, 'message' => 'Social User Successfully Login'];

        } catch (Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'data' => $e];
            return $response;
        }
        return $response;


    }

    public function userLogout($request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                // Revoke the user's current token...
                $user->tokens()->delete();
            } else if (isset(auth()->user()->id)) {
                $user_id = auth()->user()->id;
                $user = User::where('id', $user_id)->first();
                // Revoke the user's current token...
                $user->tokens()->delete();
            }

            $response = ['result' => 'success', 'data' => "", 'message' => 'You have Logged out successfully'];
        } catch (Exception $e) {

            DB::rollBack();
            $response = ['result' => 'error', 'data' => $e];
        }

        return $response;


    }

    //update Password
    public function updatePassword(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'old_password' => ['required', new MatchOldPassword],
                'password' => ['required']
            ]);

            if ($validator->fails()) {
                $response = ['result' => 'error', 'message' => $validator->errors()->first()];
                return $response;
            }

            if (Auth::check()) {

                $user = Auth::user();

            } else if (isset(auth()->user()->id)) {

                $user_id = auth()->user()->id;

                $user = User::where('id', $user_id)->first();
            }

            if (isset($user)) {

                $user->password = Hash::make($request->password);
                $user->save();

                DB::commit();
                $data       = $this->getUserData($user);
                $response   = ['result' => 'success', 'data' => $data, 'message' => 'Password successfully update!'];

            } else {
                $response = ['result' => 'error', 'message' => "Invalid request try again."];
            }

        } catch (Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => $e];
        }

        return $response;


    }

    // Forget Password
    public function forgetPassword(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'mobile_no' => 'required',
            ]);
            if ($validator->fails()) {
                $response = ['result' => 'error', 'message' => $validator->errors()->first()];
                return $response;
            }

            $user = User::where('mobile_no', $request->mobile_no)->first();

            if (isset($user)) {
                $user->password = Hash::make($request->password);
                $user->save();
                DB::commit();
                $data       = $this->getUserData($user);
                $response   = ['result' => 'success', 'data' => $data, 'message' => 'Password successfully update!'];

            } else {

                $response = ['result' => 'error', 'message' => "Invalid request try again."];
            }

        } catch (Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'data' => $e];
        }

        return $response;


    }


    // Update Profile
    public function updateUserInfo($request)
    {

        DB::beginTransaction();

        try {
            /*$validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'name' => 'nullable|max:255',
                //'email' => 'nullable|email|unique:users',
                //'mobile_no' => 'nullable|unique:users',
            ]);

            if ($validator->fails()) {
                $response = ['result' => 'error', 'message' => $validator->errors()->first()];
                return $response;
            }*/

            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $response = ['result' => 'error', 'message' => "Invalid token, try again."];
            }
            //dd($user);
            if (isset($user)) {

                if (isset($request->name))
                    $user->name = $request->name;

                $user->save();

                DB::commit();

                $data = $this->getUserData($user);

                $response = ['result' => 'success', 'data' => $data, 'message' => 'User Info updated Successfully'];

            } else {

                $response = ['result' => 'error', 'message' => "Invalid User, Try Again with valid user info."];
            }


        } catch (Exception $e) {

            DB::rollBack();
            $response = ['result' => 'error', 'data' => $e];
        }

        return $response;


    }


    public function getUserData($user, $accessToken = null)
    {

        if (isset($user)) {
            // Get Passenger Wallet
            $passengerWallet    =    $user->wallet('Passenger-Wallet');

            if(!isset($passengerWallet) || $passengerWallet == null) {
                // Create Wallet For Passenger
                $this->createUserWallet($user,'Passenger-Wallet');
                //Again Get Passenger Wallet
                $passengerWallet = $user->wallet('Passenger-Wallet');
            }

            $balance = $passengerWallet->balance ?? 0;

            $userArr = [
                'user_id' => $user->id,
                'email' => $user->email,
                'mobile_no' => $user->mobile_no,
                'fcm_token' => $user->fcm_token,
                'user_type' => $user->user_type,
                'is_verified' => $user->is_verified,
                'referral_code' => $user->referral_code,
                'steps' => $user->steps,
                'provider' => $user->provider,
                'image' => $user->image,
                'name' => $user->name,
                'wallet_balance'=> $balance
            ];

            if (isset($accessToken) && $accessToken != null) {
                $userArr['access_token'] = $accessToken;
            }

        } else {
            $userArr = [];
        }

        return $userArr;


    }



}
