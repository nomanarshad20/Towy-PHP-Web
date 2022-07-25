<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\VoucherCodeRequest;
use App\Services\API\Passenger\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public $voucherService;
    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    public function apply(VoucherCodeRequest $request)
    {
        $voucher =  $this->voucherService->checkVoucherCode($request);

        if($voucher['result']== 'success')
        {
            return makeResponse($voucher['result'],$voucher['message'],$voucher['code'],$voucher['data']);
        }
        else{
            return makeResponse($voucher['result'],$voucher['message'],$voucher['code']);
        }
    }

    public function voucherList()
    {
        $vouchers =  $this->voucherService->getActiveVoucherList();

        if($vouchers['result'] == 'error')
        {
            return makeResponse($vouchers['result'],$vouchers['message'],$vouchers['code']);
        }

        return makeResponse($vouchers['result'],$vouchers['message'],$vouchers['code'],$vouchers['data']);

    }
}
