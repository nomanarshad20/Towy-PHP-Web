<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateVoucherCode;
use App\Services\Admin\VoucherCodeService;
use Illuminate\Http\Request;

class VoucherCodeController extends Controller
{
    public $voucherService;

    public function __construct(VoucherCodeService $voucherCodeService)
    {
        $this->voucherService = $voucherCodeService;
    }

    public function index()
    {
        return $this->voucherService->index();
    }

    public function create()
    {
        return $this->voucherService->create();
    }

    public function save(CreateVoucherCode $request)
    {
        return $this->voucherService->save($request);
    }

    public function edit($id)
    {
        return $this->voucherService->edit($id);
    }

    public function update(CreateVoucherCode $request)
    {
        return $this->voucherService->update($request);
    }

    public function delete(Request $request)
    {
        return $this->voucherService->delete($request);
    }

    public function sendToPassengerView(Request $request)
    {
        return $this->voucherService->sendToPassengerView($request);
    }

    public function send(Request $request)
    {
        return $this->voucherService->send($request);
    }
}



