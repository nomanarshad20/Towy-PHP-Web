<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\DriverSaveInformationRequest;
use App\Http\Requests\API\Driver\SaveDriverDocument;
use App\Http\Requests\API\Driver\SaveVehicleRequest;
use App\Services\API\Driver\AuthService;
use App\Services\API\Driver\DriverInformationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverInformationController extends Controller
{
    public $driverService;
    public $authService;

    public function __construct(DriverInformationService $driverService, AuthService $authService)
    {
        $this->driverService = $driverService;
        $this->authService = $authService;
    }

    public function save(DriverSaveInformationRequest $request)
    {
        DB::beginTransaction();
        try {
            $saveDriverInformation = $this->driverService->save($request);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Some Error Occur During Driver Save Information: ' . $e, 500);
        }

        if ($saveDriverInformation['result'] == 'success') {
            try {
                $userResponse = $this->authService->loginUserResponse();
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Some Error Occur During Driver Save Information: ' . $e, 500);
            }

        }
        else {
            return makeResponse('error', $saveDriverInformation['message'], $saveDriverInformation['code']);
        }

        DB::commit();
        return makeResponse('success', 'Information Save Successfully', 200, $userResponse);
    }

    public function saveDocument(SaveDriverDocument $request)
    {
        if ($request->cnic_front_side) {
            $this->driverService->saveCNICFrontSide($request->cnic_front_side);
        }

        if ($request->cnic_back_side) {
            $this->driverService->saveCNICBackSide($request->cnic_back_side);
        }

        if ($request->license_front_side) {
            $this->driverService->saveLicenseFrontSide($request->license_front_side);
        }

        if ($request->license_back_side) {
            $this->driverService->saveLicenseBackSide($request->license_back_side);
        }

        if ($request->image) {
            $this->driverService->savePhoto($request->image);
        }

        $data = $this->authService->loginUserResponse();

        return makeResponse('success', 'Document Upload Successfully', 200, $data);
    }

    public function saveVehicleInformation(SaveVehicleRequest $request)
    {
        $vehicleInformation = $this->driverService->saveVehicleInformation($request);

        if($vehicleInformation['result'] == 'success')
        {
            $data = $this->authService->loginUserResponse();

        }
        else{
            return makeResponse('error',$vehicleInformation['message'],500);
        }

        return makeResponse('success',$vehicleInformation['message'] , 200, $data);

    }
}
