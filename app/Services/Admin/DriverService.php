<?php


namespace App\Services\Admin;


use App\Helper\ImageUploadHelper;
use App\Models\Driver;
use App\Models\Franchise;
use App\Models\ResendRequest;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\API\Passenger\StripeService;
use App\Traits\CreateUserWalletTrait;
use App\Traits\DriverPortalTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverService
{
    use CreateUserWalletTrait, DriverPortalTrait;

    public function index()
    {
        $data = User::whereIn('user_type', [2, 4])->get();
        return view('admin.driver.listing', compact('data'));
    }

    public function create()
    {
//        $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
        $vehicleTypes = VehicleType::where('status', 1)->get();
        $services = Service::all();

        return view('admin.driver.create', compact('vehicleTypes', 'services'));
    }

    public function save($request)
    {
        DB::beginTransaction();

        try {
            $user = User::create(['first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'mobile_no' => $request->mobile_no, 'user_type' => $request->user_type,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating User: ' . $e, 200);
        }

        if ($request->user_type == 2) {

            try {
                $vehicle = Vehicle::create(['name' => $request->vehicle_name,
                    'model' => $request->model, 'vehicle_type_id' => $request->vehicle_type_id,
                    'model_year' => $request->model_year,
                    'registration_number' => $request->registration_number]);

            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating Vehicle: ' . $e, 200);
            }
        } elseif ($request->user_type == 4) {
            try {
                foreach ($request->services as $service) {
                    $driverService = \App\Models\DriverService::create([
                        'user_id' => $user->id,
                        'service_id' => $service
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Saving Driver Service: ' . $e, 200);
            }
        }

        try {

            $vehicle = null;
            if (isset($vehicle->id)) {
                $vehicle = $vehicle->id;
            }


            $driver = Driver::create(['city' => $request->city,
                'franchise_id' => $request->franchise_id,
                'vehicle_type_id' => $request->vehicle_type_id,
                'vehicle_id' => $vehicle, 'user_id' => $user->id]);


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

            $services = Service::all();
            $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
            $userServices = \App\Models\DriverService::where('user_id', $id)->pluck('service_id')->toArray();
//            $vehicleTypes =  VehicleType::where('status',1)->get();


            return view('admin.driver.edit', compact('data', 'franchises', 'services', 'userServices'));
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

        if ($data) {
            try {
                $data->update(['first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile_no' => $request->mobile_no, 'user_type' => $request->user_type,
                ]);

                if ($request->password) {
                    $data->update(['password' => bcrypt($request->password)]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Creating User: ' . $e, 200);
            }


            if ($request->user_type == 2) {
                try {
                    if (isset($data->driver->vehicle)) {
                        $data->driver->vehicle->update(['name' => $request->vehicle_name,
                            'model' => $request->model, 'vehicle_type_id' => $request->vehicle_type_id,
                            'model_year' => $request->model_year,
                            'registration_number' => $request->registration_number]);

                        $saveVehicle = $data->driver->vehicle;
                    } else {
                        $saveVehicle = Vehicle::create(['name' => $request->vehicle_name,
                            'model' => $request->model, 'vehicle_type_id' => $request->vehicle_type_id,
                            'model_year' => $request->model_year,
                            'registration_number' => $request->registration_number]);
                    }


                } catch (\Exception $e) {
                    DB::rollBack();
                    return makeResponse('error', 'Error in Creating Vehicle: ' . $e, 200);
                }
            } elseif ($request->user_type == 4) {
                try {
                    \App\Models\DriverService::where('user_id', $request->id)->delete();

                    foreach ($request->services as $service) {
                        $driverService = \App\Models\DriverService::create([
                            'user_id' => $request->id,
                            'service_id' => $service
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return makeResponse('error', 'Error in Saving Driver Service: ' . $e, 200);
                }
            }

            try {

                $vehicle = null;
                if (isset($saveVehicle->id)) {
                    $vehicle = $saveVehicle->id;
                }

                if (isset($data->driver->vehicle)) {
                    $vehicle = $data->driver->vehicle->id;
                }

                if (isset($data->driver)) {
                    $driver = Driver::where('user_id', $request->id)
                        ->update(['city' => $request->city,
                            'franchise_id' => $request->franchise_id,
                            'vehicle_type_id' => $request->vehicle_type_id,
//                            'vehicle_id' => isset($data->driver->vehicle) ? $data->driver->vehicle->id : $saveVehicle->id,
                            'vehicle_id' => $vehicle,
                            'user_id' => $request->id]);

                } else {
                    $driver = Driver::create(['city' => $request->city,
                        'franchise_id' => $request->franchise_id,
                        'vehicle_type_id' => $request->vehicle_type_id,
//                        'vehicle_id' => $saveVehicle->id,
                        'vehicle_id' => $vehicle,
                        'user_id' => $request->id,

                    ]);

                }

//                $data->save();

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
                    $saveVehicle->registration_book = $image;
                    $saveVehicle->save();
                }

                if ($request->has('profile_image')) {
                    $profile_image = ImageUploadHelper::uploadImage($request->profile_image, 'upload/driver/' . $request->id . '/');
                    $data->image = $profile_image;
                    $vehicleTypes = VehicleType::where('status', 1)->get();

                    $data->save();


//                    $data->update(['image'=>$profile_image]);
                }

                $data->driver->save();
//                $data->driver->vehicle->save();

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Saving Documents: ' . $e, 200);
            }


            if ($data->driver->vehicle_inspection && $data->driver->vehicle_insurance
            && $data->driver->drivers_license
            && isset($saveVehicle) ? $saveVehicle->registration_book : false
                && $data->image) {

                $data->steps = 4;
                $data->save();

            }


            DB::commit();

            return makeResponse('success', 'Driver Information Save Successfully', 200);

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
                $data->driver->update(['' . $request->image => null]);
            }

            return makeResponse('success', 'Image Removed Successfully', 200);

        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }


    }


    public function portal($id, $fromDate = null, $tillDate = null)
    {
        $ridesSummary = [];
        $previousAmount = 0;

        if (!isset($fromDate) && $fromDate == null) {
            $fromDate = Carbon::today();
        }
        if (!isset($tillDate) && $tillDate == null) {
            $tillDate = Carbon::now();
        }

        $userInfo = User::where('id', $id)->first();

        if (isset($userInfo) && $userInfo != null) {
            $firstDate = Carbon::parse($fromDate);
            $secondDate = Carbon::parse($userInfo->created_at);

            if ($firstDate->greaterThan($secondDate)) {
                $preFromDate = $secondDate;
                $preTillDate = $firstDate;

                $preDriverCalculations = $this->driverPortalPreviousDetails($id, $preFromDate, $preTillDate);

                if (isset($preDriverCalculations) && $preDriverCalculations['previous_final_total_amount'] != 0)
                    $previousAmount = $preDriverCalculations['previous_final_total_amount'];

            }

            if ($firstDate->lessThanOrEqualTo($secondDate))
                $fromDate = $secondDate;

            $ridesSummary = $this->driverPortalDetails($id, $fromDate, $tillDate, $previousAmount);

            $driverWalletBalance = $this->driverWalletBalance($id);


            return view('admin.driver.driver_portal', compact('ridesSummary', 'userInfo', 'driverWalletBalance'));

        } else {
            return redirect()->back()->with('error', 'Invalid Request, Driver not found!');
        }
    }

    public function payOrReceivePartnerAmount($request)
    {
        DB::beginTransaction();
        try {
            $amount = $request->amount;
            $partnerId = $request->id;
            $payReceiveFlag = $request->payReceiveFlag;
            $payment_method = "cash_paid";
            $driver_type = "Public Partner";
            $type = "debit";
            $description = "Rs. " . $amount . " " . $payReceiveFlag . " to Partner";

            $user = User::with('driver')->where('id', $partnerId)->first();

            if (isset($user) && $amount > 0) {

                if (!$user->stripe_customer_id) {
                    return Redirect()->back()->with('error', 'User is not connected with our Account on Stripe!');
                }


                if ($amount > 0) {
                    $data[] = [
                        "driver_id" => $user->id,
                        "franchise_id" => $user->driver->franchise_id
                    ];
                    if ($payReceiveFlag == 'bonus') {

                        $type = "credit";
                        $payment_method = "bonus";
                        $description = "Received  Bonus Rs. " . $amount . " From Toto Admin.";
                        // Update Partner Wallet
                        $driverCalculations = $this->driverWalletUpdate($data, 0, 0, 0, $amount, "credit", $payment_method, $description);

                    } else {
                        if ($payReceiveFlag == "received") {
                            $type = "credit";
                            $payment_method = "cash_received";
                            $description = "Rs. " . $amount . " " . $payReceiveFlag . " from Partner";
                        }

                        $franchiseCalculations = $this->franchiseWalletUpdate($data, 0, 0, 0, $amount, $type, $payment_method, $description);
                    }

//                    return Redirect()->back()->with('success', 'Amount successfully Paid');
                } else {
                    return Redirect()->back()->with('error', 'Invalid Request, Driver not found!');
                }
            } else {
                return Redirect()->back()->with('error', 'Invalid Request, Driver not found! or Invalid Amount');
            }

            //payFromStripe
            try {
                $stripeService = new StripeService;

                $amount = $stripeService->transferAmount($amount, $user->stripe_customer_id);

                if ($amount['type'] == 'error') {
                    DB::rollBack();
                    return Redirect()->back()->with('error', $amount['message']);
                }
            }
            catch (\Exception $e) {
                DB::rollBack();
                return Redirect()->back()->with('error', $e->getMessage());
            }
            DB::commit();
            return Redirect()->back()->with('success', 'Amount successfully Paid');

        } catch (\Exception $e) {
            DB::rollBack();

            return Redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approvalRequest($request)
    {
        $data = ResendRequest::all();

        return view('admin.driver.approval_request_list', compact('data'));
    }
}
