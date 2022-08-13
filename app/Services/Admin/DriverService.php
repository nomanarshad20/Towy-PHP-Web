<?php


namespace App\Services\Admin;


use App\Helper\ImageUploadHelper;
use App\Models\Driver;
use App\Models\Franchise;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Traits\CreateUserWalletTrait;
use App\Traits\DriverPortalTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverService
{

    use CreateUserWalletTrait,DriverPortalTrait;

    public function index()
    {
        $data = User::where('user_type', 2)->get();
        return view('admin.driver.listing', compact('data'));
    }

    public function create()
    {
//        $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
        $vehicleTypes =  VehicleType::where('status',1)->get();

        return view('admin.driver.create', compact('vehicleTypes'));
    }

    public function save($request)
    {
        DB::beginTransaction();

        try {
            $user = User::create(['first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'mobile_no' => $request->mobile_no, 'user_type' => 2,


            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating User: ' . $e, 200);
        }


        try {
            $vehicle = Vehicle::create(['name' => $request->vehicle_name,
                'model' => $request->model,'vehicle_type_id'=>$request->vehicle_type_id,
                'model_year' => $request->model_year,
                'registration_number' => $request->registration_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Vehicle: ' . $e, 200);
        }

        try {

            $driver = Driver::create(['city' => $request->city,
                'franchise_id' => $request->franchise_id,
                'vehicle_type_id'=>$request->vehicle_type_id,
                'vehicle_id' => $vehicle->id, 'user_id' => $user->id]);


            $user->referral_code = "partner-00" . $user->id;

            $user->save();

//            $vehicleTypes =  VehicleType::where('status',1)->get();

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Driver: ' . $e, 200);
        }


        DB::commit();
        return makeResponse('success', 'Driver Information Save Successfully', 200);
    }

    public function edit($id)
    {
        $data = User::find($id);

        if ($data) {
            $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
//            $vehicleTypes =  VehicleType::where('status',1)->get();


            return view('admin.driver.edit', compact('data', 'franchises'));
        } else {
            return redirect()->route('driverListing')->with('error', 'Record Not Found');
        }

    }

    public function update($request)
    {
        DB::beginTransaction();

        $checkForEmail = User::where('email', $request->email)->where('id', '!=', $request->id)
            ->first();

        if ($checkForEmail) {
            return makeResponse('error', 'Email Already Exist', 200);
        }

        $checkForMobilePhone = User::where('mobile_no', $request->mobile_no)->where('id', '!=', $request->id)
            ->first();

        if ($checkForMobilePhone) {
            return makeResponse('error', 'Mobile No Already Exist', 200);
        }

        $data = User::find($request->id);

        if ($data && $data->user_type == 2) {
            try {
                $data->update(['first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile_no' => $request->mobile_no, 'user_type' => 2,
                ]);

                if ($request->password) {
                    $data->update(['password' => bcrypt($request->password)]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating User: ' . $e, 200);
            }


            try {
                $data->driver->vehicle->update(['name' => $request->vehicle_name,
                    'model' => $request->model,'vehicle_type_id'=>$request->vehicle_type_id,
                    'model_year' => $request->model_year,
                    'registration_number' => $request->registration_number]);

            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating Vehicle: ' . $e, 200);
            }

            try {

                $driver = $data->driver->update(['city' => $request->city,
                    'franchise_id' => $request->franchise_id,
                    'vehicle_type_id'=>$request->vehicle_type_id,
                    'vehicle_id' => $data->driver->vehicle->id, 'user_id' => $request->id]);

                $data->save();

            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating Driver: ' . $e, 200);
            }

            try {
                if ($request->has('vehicle_inspection')) {
                    $image = ImageUploadHelper::uploadImage($request->vehicle_inspection, 'upload/driver/' . $request->id . '/');
                    $data->driver->vehicle_inspection = $image;

                }

                if ($request->has('vehicle_insurance')) {
                    $image = ImageUploadHelper::uploadImage($request->vehicle_insurance, 'upload/driver/' . $request->id . '/');
                    $data->driver->vehicle_insurance = $image;
                }

                if ($request->has('drivers_license')) {
                    $image = ImageUploadHelper::uploadImage($request->drivers_license, 'upload/driver/' . $request->id . '/');
                    $data->driver->drivers_license = $image;
                }



                if ($request->has('registration_book')) {
                    $image = ImageUploadHelper::uploadImage($request->registration_book, 'upload/vehicle/' . $data->driver->vehicle->id . '/');
                    $data->driver->vehicle->registration_book = $image;
                }

                if ($request->has('profile_image')) {
                    $profile_image = ImageUploadHelper::uploadImage($request->profile_image, 'upload/driver/' . $request->id . '/');
                    $data->image = $profile_image;
                    $vehicleTypes =  VehicleType::where('status',1)->get();

                    $data->save();


//                    $data->update(['image'=>$profile_image]);
                }

                $data->driver->save();
                $data->driver->vehicle->save();

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Saving Documents: ' . $e, 200);
            }

            DB::commit();
            return makeResponse('success', 'Driver Information Save Successfully', 200,);

        } else {
            return makeResponse('error', 'Record Not Found', 200);

        }


    }


    public function changeStatus($request)
    {
        $data = User::find($request->id);

        if ($data) {
            try {
                if ($data->is_verified == 1) {
                    $data->is_verified = 0;
                } else {
                    $data->is_verified = 1;
                }
                $data->save();

                return makeResponse('success', 'Status Change Successfully', 200);

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Change Status: ' . $e, 200);

            }
        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }
    }

    public function delete($request)
    {
        $data = User::find($request->id);

        if ($data) {
            try {

                $data->delete();

                return makeResponse('success', 'Driver Deleted Successfully', 200);

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Deleting Driver: ' . $e, 200);
            }
        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }
    }

    public function deleteImage($request)
    {
        $data = User::find($request->id);

        if ($data) {
            if ($request->image == 'profile_image') {
                $data->image = null;
                $data->save();

            } elseif ($request->image == 'registration_book') {
                $data->driver->vehicle->registration_book = null;

                $data->driver->vehilce->save();
            } else {
                $data->driver->update(['' .$request->image => null]);
            }

            return makeResponse('success', 'Image Removed Successfully', 200);

        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }


    }


    public function portal($id,$fromDate=null,$tillDate=null)
    {
        $ridesSummary       = [];
        $previousAmount     = 0;

        if(!isset($fromDate) && $fromDate == null){
            $fromDate           = Carbon::today();
        }
        if(!isset($tillDate) && $tillDate == null){
            $tillDate           = Carbon::now();
        }

        $userInfo               = User::where('id', $id)->first();

        if(isset($userInfo) && $userInfo != null){
            $firstDate          = Carbon::parse($fromDate);
            $secondDate         = Carbon::parse($userInfo->created_at);

            if ($firstDate->greaterThan($secondDate)) {
                $preFromDate            = $secondDate;
                $preTillDate            = $firstDate;

                $preDriverCalculations  = DriverPortalTrait::driverPortalPreviousDetails($id, $preFromDate, $preTillDate);

                if(isset($preDriverCalculations) && $preDriverCalculations['previous_final_total_amount'] != 0)
                    $previousAmount     = $preDriverCalculations['previous_final_total_amount'];

            }

            if ($firstDate->lessThanOrEqualTo($secondDate))
                $fromDate       = $secondDate;

            $ridesSummary       = DriverPortalTrait::driverPortalDetails($id, $fromDate, $tillDate,$previousAmount);

            return view('admin.driver.portal', compact('ridesSummary','userInfo'));

        }else {
            return Redirect()->back()->withErrors(['error', 'Invalid Request, Driver not found!']);
        }
    }

    public function payOrReceivePartnerAmount($request)
    {
        try{
            $amount         = $request->amount;
            $partnerId      = $request->id;
            $payReceiveFlag = $request->payReceiveFlag;
            $payment_method = "cash_paid";
            $driver_type    = "Public Partner";
            $type           = "debit";
            $description    = "Rs. " . $amount . " " . $payReceiveFlag . " to Partner";

            $user           = User::with('driver')->where('id',$partnerId)->first();
            if(isset($user) && $amount > 0) {
                if ($amount > 0) {
                    $data[] = [
                        "driver_id"     => $user->id,
                        "franchise_id"  => $user->driver->franchise_id
                    ];
                    if($payReceiveFlag == 'bonus'){

                        $type               = "credit";
                        $payment_method     = "bonus";
                        $description        = "Received  Bonus Rs. " . $amount . " From Toto Admin.";
                        // Update Partner Wallet
                        $driverCalculations = CreateUserWalletTrait::driverWalletUpdate($data, 0, 0, 0, $amount, "credit", $payment_method, $description);

                    }else{
                        if ($payReceiveFlag == "received") {
                            $type           = "credit";
                            $payment_method = "cash_received";
                            $description    = "Rs. " . $amount . " " . $payReceiveFlag . " from Partner";
                        }

                        $franchiseCalculations = CreateUserWalletTrait::franchiseWalletUpdate($data, 0, 0, 0, $amount, $type, $payment_method, $description);
                    }

                    return Redirect()->back()->with('success', 'Amount successfully Paid');
                } else {
                    return Redirect()->back()->withErrors(['error', 'Invalid Request, Driver not found!']);
                }
            }else{
                return Redirect()->back()->withErrors(['error', 'Invalid Request, Driver not found! or Invalid Amount']);
            }
        } catch (\Exception $e) {
            return Redirect()->back()->withErrors(['error', $e->getMessage()]);
        }
    }
}
