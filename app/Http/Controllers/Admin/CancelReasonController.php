<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateCancelReasonRequest;
use App\Services\Admin\CancelReasonService;
use Illuminate\Http\Request;

class CancelReasonController extends Controller
{

    public $cancelReasonService;

    public function __construct(CancelReasonService $cancelReasonService)
    {
        $this->cancelReasonService = $cancelReasonService;
    }

    public function index()
    {
        return $this->cancelReasonService->index();
    }

    public function create()
    {
        return $this->cancelReasonService->create();
    }

    public function save(CreateCancelReasonRequest $request)
    {
        return $this->cancelReasonService->save($request);
    }

    public function edit($id)
    {
        return $this->cancelReasonService->edit($id);
    }

    public function update(CreateCancelReasonRequest $request)
    {
        return $this->cancelReasonService->update($request);
    }

    public function delete(Request $request)
    {
        return $this->cancelReasonService->delete($request);
    }
}
