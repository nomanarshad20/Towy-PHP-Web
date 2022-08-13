<?php


namespace App\Services\Admin;


use App\Models\User;

class PassengerService
{
    public function index()
    {
        $data =  User::where('user_type',1)->get();

        return view('admin.passenger.listing',compact('data'));
    }

    public function create()
    {
        return view('admin.passenger.create');
    }

    public function save($user, $request)
    {
        $checkMobileNo = User::where('mobile_no',$request->mobile_no);
        $checkEmail = User::where('email',$request->email);

        if($user && isset($user->id ))
        {
            $checkMobileNo =  $checkMobileNo->where('id','!=',$user->id);
            $checkEmail = $checkEmail->where('id','!=',$user->id);
        }

        $checkMobileNo =  $checkMobileNo->first();

        if($checkMobileNo)
        {
            return makeResponse('error','Mobile Number already exists',200);
        }

        $checkEmail =  $checkEmail->first();

        if($checkEmail)
        {
            return makeResponse('error','Email already exists',200);
        }


        try{

            $user->first_name =  $request->first_name;
            $user->last_name =  $request->last_name;
            $user->email =  $request->email;
            $user->password =  bcrypt($request->password);
            $user->mobile_no =  $request->mobile_no;
            $user->user_type = 1;

            $user->save();

            if(!$user->referral_code)
            {
                $user->referral_code = "passenger-00" . $user->id;
                $user->save();
            }



            return makeResponse('success','Passenger Information Save Successfully',200);

        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Saving Passenger: ',$e,200);
        }
    }

    public function edit($id)
    {
        $data = User::find($id);

        if($data)
        {
            if($data->user_type != 1)
            {
                return redirect()->route('passengerListing')->with('error','User is not Registered as Passenger');
            }

            return view('admin.passenger.edit',compact('data'));
        }
        else{
            return redirect()->route('passengerListing')->with('error','Record Not Found');
        }
    }


    public function delete($request)
    {
        $data =  User::find($request->id);
        if($data)
        {
            try{
                $data->delete();
                return makeResponse('success','Passenger Deleted Successfully',200);

            }
            catch (\Exception $e)
            {
                return makeResponse('error','Error in Delete Passenger: '.$e,200);

            }
        }
        else{
            return makeResponse('error','Record Not Found',200);
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
}
