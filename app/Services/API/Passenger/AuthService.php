<?php


namespace App\Services\API\Passenger;

use App\Helper\ImageUploadHelper;
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

    public function userLoginCheck($mobileNo, $userType)
    {
//        $credentials = ['mobile_no' => $mobileNo,];
        $userFind = User::where('mobile_no', $mobileNo)->where('user_type', $userType)
            ->where('is_verified', 1)
            ->first();

        if ($userFind) {
            Auth::loginUsingId($userFind->id);
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
                'steps' => 5,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                "is_verified" => 1
            ]);


            $user->referral_code = "passenger-00" . $user->id;
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

            $user = User::where('social_uid', $request->social_uid)->where('provider', $request->provider)->first();

            if ($user) {

                $user->first_name = $request->first_name;
                $user->last_name = $request->first_name;
                $user->email = isset($request->email) ? $request->email : null;
                $user->social_uid = $request->social_uid;
                $user->provider = $request->provider;
                $user->fcm_token = $request->fcm_token;
                $user->save();

                $message = "Login Successfully";
            } else {

                if (isset($request->email) && $request->email) {
                    $checkEmail = User::where('email', $request->email)->first();
                    if ($checkEmail) {
                        DB::rollBack();
                        $response = ['result' => 'error', 'message' => 'Email Already Exist', 'code' => 401];
                        return $response;
//                    return makeResponse('error', 'Email Already Exist', 401);
                    }
                }


                $user = User::create([
                    'fcm_token' => $request->fcm_token,
                    'email' => isset($request->email) ? $request->email : null,
                    'user_type' => $request->user_type,
                    'steps' => 5,
                    'provider' => $request->provider,
                    'social_uid' => $request->social_uid,
                    'is_verified' => 1,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name
                ]);
                $user->referral_code = "passenger-00" . $user->id;
                $user->save();

                $message = 'Social User Registered Successfully';

            }

            DB::commit();
            Auth::loginUsingId($user->id, true);
            $token = Auth::user()->createToken('TOTOBookingApp')->plainTextToken;
            $data = $this->getUserData($user, $token);
            $response = ['result' => 'success', 'message' => $message, 'code' => 200, 'data' => $data];

        } catch (Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' => 'Error in Creating User: ' . $e, 'code' => 500];
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
                $data = $this->getUserData($user);
                $response = ['result' => 'success', 'data' => $data, 'message' => 'Password successfully update!'];

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
                $data = $this->getUserData($user);
                $response = ['result' => 'success', 'data' => $data, 'message' => 'Password successfully update!'];

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


//            if (Auth::check()) {
//                $user = Auth::user();
//            } else {
//                $response = ['result' => 'error', 'message' => "Invalid token, try again."];
//            }
            //dd($user);

            if ($request->first_name) {
                Auth::user()->first_name = $request->first_name;
            }

            if ($request->last_name) {
                Auth::user()->last_name = $request->last_name;
            }

            if($request->has('image'))
            {
                $image = ImageUploadHelper::uploadImage($request->image, 'upload/passenger/' . Auth::user()->id . '/');
                Auth::user()->image =  $image;
            }


            Auth::user()->save();

            DB::commit();

            $data = $this->getUserData(Auth::user());

            $response = ['result' => 'success', 'data' => $data, 'message' => 'User Info updated Successfully'];


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
            $balance = CreateUserWalletTrait::passengerWalletBalance($user->id);

            $rating = 0;
            if (isset(Auth::user()->rating)) {
                $rating = Auth::user()->rating->avg('rating');
                if ($rating == null) {
                    $rating = 0;
                }
            }

//            $balance = $passengerWallet->balance ?? 0;

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
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'wallet_balance' => $balance,
                'rating' => $rating
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
