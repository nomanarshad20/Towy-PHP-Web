<?php


namespace App\Services\API\Passenger;


use App\Models\VoucherCode;
use App\Models\VoucherCodePassenger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VoucherService
{
    public function checkVoucherCode($request)
    {
        $data = VoucherCodePassenger::where('voucher_code', $request->voucher_code)
            ->where('passenger_id', Auth::user()->id)
            ->first();

        if ($data) {
//            if($data->passenger_id != Auth::user()->id)
//            {
//                $response = ['result'=>'error','message'=>'This Voucher Code is not applicable for you','code'=>421];
//                return $response;
//            }

            $checkDate = Carbon::parse($data->expiry_date);
            if (Carbon::parse($data->expiry_date) < Carbon::now()) {
                $response = ['result' => 'error', 'message' => 'This Voucher Code is expired', 'code' => 404];
                return $response;
            }

//            if($data->is_applied == 1)
//            {
//                $response = ['result'=>'error','message'=>'You have already applied that voucher code','code'=>404];
//                return $response;
//            }

            if ($data->is_used == 1) {
                $response = ['result' => 'error', 'message' => 'You have already used that voucher code', 'code' => 404];
                return $response;
            }

            $data->is_applied = 1;

            $data->save();

            $response = ['result' => 'success', 'message' => 'Voucher code applied Successfully', 'code' => 404,
                'data' => $data];

            return $response;

        } else {
            $response = ['result' => 'error', 'message' => 'Invalid Voucher Code', 'code' => 404];
            return $response;
        }

    }

    public function getActiveVoucherList()
    {
        $data = VoucherCodePassenger::where('passenger_id', Auth::user()->id)
            ->where('expiry_date', '>', Carbon::now())
            ->where('is_used', 0)
            ->get();

        if (sizeof($data) > 0) {
            $response = ['result' => 'success', 'message' => 'Voucher code list retrieve Successfully', 'code' => 200,
                'data' => $data];

            return $response;
        } else {
            $response = ['result' => 'error', 'message' => 'Record Not Found', 'code' => 404,];

            return $response;
        }
    }
}
