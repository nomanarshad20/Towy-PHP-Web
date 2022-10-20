<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DriverCreateRequest;
use App\Services\Admin\DriverService;
use Illuminate\Http\Request;
use Carbon\Carbon;
class DriverController extends Controller
{

    public $driverService;

    public function __construct(DriverService $driverService) {
        $this->driverService = $driverService;
    }

    public function index()
    {
        return $this->driverService->index();
    }

    public function create()
    {
        return $this->driverService->create();
    }

    public function save(DriverCreateRequest $request)
    {
        $this->validate($request,[
            'mobile_no' => 'required|unique:users',
            'email' => 'email|unique:users',
            'password' => 'required|min:8'

        ]);

        return $this->driverService->save($request);
    }


    public function edit($id)
    {
        return $this->driverService->edit($id);
    }

    public function update(DriverCreateRequest $request)
    {
        $this->validate($request,[

            'password' => 'nullable|min:8',
            'vehicle_inspection' => 'mimes:jpg,jpeg,png',
            'vehicle_insurance' => 'mimes:jpg,jpeg,png',
            'drivers_license' => 'mimes:jpg,jpeg,png',
            'profile_image' => 'mimes:jpg,jpeg,png',
            'registration_book' => 'mimes:jpg,jpeg,png',
        ]);


        return $this->driverService->update($request);
    }

    public function changeStatus(Request $request)
    {
        return $this->driverService->changeStatus($request);
    }

    public function delete(Request $request)
    {
        return $this->driverService->delete($request);
    }

    public function deleteImage(Request $request)
    {
        return $this->driverService->deleteImage($request);
    }

    public function portal($id=null,Request $request)
    {
        if($id==null){
            $id = $request->driverID;
        }
        if(isset($request->fromDate) && $request->fromDate != null ){
            $fromDate = $request->fromDate;
        }else{
            $fromDate = Carbon::today();
        }
        if(isset($request->tillDate) && $request->tillDate != null ){
            $tillDate = $request->tillDate;
        }else{
            $tillDate = Carbon::now();
        }

        return $this->driverService->portal($id,$fromDate,$tillDate);
    }

    public function payReceiveFromDriver(Request $request)
    {
        return $this->driverService->payOrReceivePartnerAmount($request);
    }

    public function approvalRequest(Request $request)
    {
        return $this->driverService->approvalRequest($request);
    }
}
