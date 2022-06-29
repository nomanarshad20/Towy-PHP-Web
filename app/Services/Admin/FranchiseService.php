<?php


namespace App\Services\Admin;


use App\Models\Franchise;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Traits\CreateUserWalletTrait;

class FranchiseService
{
    use CreateUserWalletTrait;
    public function index()
    {
        $data = User::where('user_type', 3)->get();

        return view('admin.franchise.listing', compact('data'));
    }

    public function create()
    {
        return view('admin.franchise.create');
    }

    public function save($request)
    {
        DB::beginTransaction();
        try {
            $user = User::create(['name' => $request->name, 'mobile_no' => $request->mobile_no,
                'email' => $request->email, 'password' => bcrypt($request->password), 'user_type' => 3
            ]);

            $franchiseWalletName = $request->name."-Wallet";
            // Get Franchise Wallet
            $franchiseWallet     =    $user->wallet($franchiseWalletName);

            if(!isset($franchiseWallet) || $franchiseWallet == null) {
                // Create New Franchise Wallet
                $this->createUserWallet($user,$franchiseWalletName);
                //Again Get Franchise Wallet
                $franchiseWallet = $user->wallet($franchiseWalletName);
                $balance = $franchiseWallet->balance ?? 0;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Franchise: ' . $e, 500);
        }

        try {
            Franchise::create(['address' => $request->address, 'lat' => $request->lat,
                'lng' => $request->lng, 'user_id' => $user->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Franchise: ' . $e, 500);
        }

        DB::commit();
        return makeResponse('success', 'Franchise Successfully Created', 200);


    }


    public function edit($id)
    {
        $data = User::find($id);

        if ($data && $data->user_type == 3) {
            return view('admin.franchise.edit', compact('data'));
        } else {
            return redirect()->route('franchiseListing')->with('error', 'Record Not Found');
        }

    }

    public function update($request)
    {
        $data = User::find($request->id);
        DB::beginTransaction();

        $checkForEmail = User::where('email', $request->email)->where('id', '!=', $request->id)
            ->first();

        if ($checkForEmail) {
            return makeResponse('error', 'Email Already Exist', 500);
        }

        $checkForMobilePhone = User::where('mobile_no', $request->mobile_no)->where('id', '!=', $request->id)
            ->first();

        if ($checkForMobilePhone) {
            return makeResponse('error', 'Mobile No Already Exist', 500);
        }


        try {
            $user = $data->update(['name' => $request->name, 'mobile_no' => $request->mobile_no,
                'email' => $request->email]);

            if ($request->password) {
                $data->update(['password' => bcrypt($request->password)]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Updating Franchise: ' . $e, 500);
        }

        try {


            if($data->franchise)
            {
                $data->franchise->update(['address' => $request->address, 'lat' => $request->lat,
                    'lng' => $request->lng]);
            }
            else{
                Franchise::create(['address' => $request->address, 'lat' => $request->lat,
                    'lng' => $request->lng, 'user_id' => $data->id]);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Updating Franchise: ' . $e, 500);
        }
        DB::commit();
        return makeResponse('success', 'Franchise Updated Successfully', 200);


    }

    public function delete($request)
    {
        $data = User::find($request->id);

        if ($data) {
            try {

                $data->delete();

                return makeResponse('success', 'Franchise Deleted Successfully', 200);

            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Deleting Franchise: ' . $e, 500);
            }
        } else {
            return makeResponse('error', 'Record Not Found', 404);
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
                return makeResponse('error', 'Error in Change Status: ' . $e, 500);

            }
        } else {
            return makeResponse('error', 'Record Not Found', 404);
        }
    }
}

