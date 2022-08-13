<?php


namespace App\Services\Admin;


use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\VoucherCode;
use App\Models\VoucherCodePassenger;
use App\Notifications\VoucherSendNotification;
use App\Traits\SendFirebaseNotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VoucherCodeService
{
    use SendFirebaseNotificationTrait;

    public function index()
    {
        $data = VoucherCode::all();

        return view('admin.voucher_code.listing', compact('data'));
    }

    public function create()
    {
        $passengers = User::where('is_verified', 1)->where('user_type', 1)->get();

        return view('admin.voucher_code.create', compact('passengers'));
    }

    public function save($request)
    {
        try {
            $check = VoucherCode::where('voucher_code', $request->voucher_code)->first();
            if ($check) {
                return makeResponse('error', 'Voucher Code Must be Unique', 200);
            }

            $date = Carbon::parse($request->expiry_date)->format('Y-m-d');

            VoucherCode::create(['voucher_code' => $request->voucher_code, 'expiry_date' => $date,
                'discount_value' => $request->discount_value, 'discount_type' => $request->discount_type]);
            return makeResponse('success', 'Voucher Code Created Successfully', 200);
        } catch (\Exception $e) {
            return makeResponse('error', 'Error in Creating Voucher Code: ' . $e, 200);
        }
    }

    public function edit($id)
    {
        $data = VoucherCode::find($id);

        if ($data) {
            $passengers = User::where('is_verified', 1)->where('user_type', 1)->get();
            return view('admin.voucher_code.edit', compact('passengers', 'data'));
        } else {
            return redirect()->route('voucherCodeListing')->with('error', 'Record Not Found');
        }

    }

    public function update($request)
    {
        $data = VoucherCode::find($request->id);

        if ($data) {
            try {

                $check = VoucherCode::where('voucher_code', $request->voucher_code)
                    ->where('id', '!=', $request->id)
                    ->first();
                if ($check) {
                    return makeResponse('error', 'Voucher Code Must be Unique', 200);
                }

                $date = Carbon::parse($request->expiry_date)->format('Y-m-d');


                $data->update(['voucher_code' => $request->voucher_code, 'expiry_date' => $date,
                    'discount_value' => $request->discount_value, 'discount_type' => $request->discount_type]);

                return makeResponse('success', 'Voucher Code Updated Successfully', 200);
            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Updating Voucher Code: ' . $e, 200);
            }

        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }
    }

    public function delete($request)
    {
        $data = VoucherCode::find($request->id);
        if ($data) {
            try {
                $data->delete();

                return makeResponse('success', 'Voucher Code Deleted Successfully', 200);
            } catch (\Exception $e) {
                return makeResponse('error', 'Error in Deleting Voucher Code: ' . $e, 200);
            }
        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }
    }

    public function sendToPassengerView($request)
    {
        $data = VoucherCode::find($request->id);
        if ($data) {
            $expiryDate = Carbon::parse($data->expiry_date);
            $checkDate = Carbon::now();

            if($expiryDate < $checkDate) {
                return redirect()->route('voucherCodeListing')->with('error','Voucher Code Expired');
            }

            $alreadySelectedPassengers = VoucherCodePassenger::where('voucher_code_id',$data->id)->pluck('passenger_id')->toArray();

            if(sizeof($alreadySelectedPassengers))
            {
                $passengers = User::where('is_verified', 1)
                    ->whereNotIn('id',$alreadySelectedPassengers)
                    ->where('user_type', 1)->get();
            }
            else{
                $passengers = User::where('is_verified', 1)->where('user_type', 1)->get();
            }


            return view('admin.voucher_code.send_passenger',compact('data','passengers','alreadySelectedPassengers'));
        } else {
            return makeResponse('error', 'Record Not Found', 200);
        }
    }

    public function send($request)
    {
        DB::beginTransaction();
        $data =  VoucherCode::find($request->id);

        if(!$data)
        {
            return makeResponse('error','Record Not Found',200);
        }

        $voucherPassengers = VoucherCodePassenger::where('voucher_code_id',$data->id)
            ->select('passenger_id','is_applied','is_used')->get()->toArray();

//        dd($voucherPassengers);

        foreach($request->passengers as $passenger)
        {
            $checkRequestAlreadySendToPassenger = in_array($passenger,$voucherPassengers);

//            dd($checkRequestAlreadySendToPassenger);

            $voucherRecord = VoucherCodePassenger::create([
                'voucher_code_id' => $data->id,
                'passenger_id' => $passenger,
                'discount_type' => $data->discount_type,
                'expiry_date' => $data->expiry_date,
                'discount_amount' => $data->discount_value,
                'is_applied' => 0,
                'is_used' => 0,
                'voucher_code' => $data->voucher_code
            ]);

            $passengerFCMToken = $voucherRecord->passenger->fcm_token;

            if($passengerFCMToken)
            {
                $notificationCode = 6;
                $title = 'Voucher Created';
                $message = 'A Voucher has been created for you';
                $sendNotification = $this->voucherNotificaiton($passengerFCMToken,$notificationCode,$title,$message,$voucherRecord);
            }

        }

        try{
            $passenger = User::whereIn('id',$request->passengers)->get();

            Notification::send($passenger,new VoucherSendNotification($voucherRecord));
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return makeResponse('error','Error in Creating Exception: '.$e,200);
        }

        DB::commit();
        return makeResponse('success','Notification Send to Passengers Successfully',200);

    }

}
