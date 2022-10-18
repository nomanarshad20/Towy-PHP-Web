<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Driver\DriverSaveInformationRequest;
use App\Http\Requests\API\Driver\SaveDriverDocument;
use App\Http\Requests\API\Driver\SaveSocialSecurityNumberRequest;
use App\Http\Requests\API\Driver\SaveVehicleRequest;
use App\Http\Requests\API\Driver\SaveVehicleTypeRequest;
use App\Services\Admin\Auth\LoginService;
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
        return makeResponse('success', 'Information Saved Successfully', 200, $userResponse);
    }

    public function saveDocument(SaveDriverDocument $request)
    {
        if ($request->drivers_license) {
            $this->driverService->drivers_license($request->drivers_license);
        }

        if ($request->vehicle_insurance) {
            $this->driverService->vehicle_insurance($request->vehicle_insurance);
        }

        if ($request->vehicle_inspection) {
            $this->driverService->vehicle_inspection($request->vehicle_inspection);
        }

//        if ($request->license_back_side) {
//            $this->driverService->saveLicenseBackSide($request->license_back_side);
//        }

        if ($request->image) {
            $this->driverService->savePhoto($request->image);
        }

        $data = $this->authService->loginUserResponse();

        return makeResponse('success', 'Document Uploaded Successfully', 200, $data);
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

    public function documentComplete()
    {
        $documentComplete = $this->driverService->documentComplete();

        if($documentComplete['result'] == 'error')
        {
            return makeResponse('error',$documentComplete['message'],$documentComplete['code']);
        }

        $data = $this->authService->loginUserResponse();

        return makeResponse('success',$documentComplete['message'] , 200, $data);

    }

    public function getVehicleType()
    {
        $getVehicleTypes =  $this->driverService->getVehicleType();

        return makeResponse($getVehicleTypes['result'],$getVehicleTypes['message'],$getVehicleTypes['code'], $getVehicleTypes['data']);
    }

    public function saveVehicleType(SaveVehicleTypeRequest  $request)
    {
        $saveVehicleType =  $this->driverService->saveVehicleType($request);

        if($saveVehicleType['result'] == 'error')
        {
            return makeResponse($saveVehicleType['result'],$saveVehicleType['message'],$saveVehicleType['code']);
        }

        $data = $this->authService->loginUserResponse();

        return makeResponse($saveVehicleType['result'],$saveVehicleType['message'],$saveVehicleType['code'],$data);
    }

    public function saveSocialSecurityNumber(SaveSocialSecurityNumberRequest $request)
    {
        $saveVehicleType =  $this->driverService->saveSocialSecurityNumber($request);

        if($saveVehicleType['result'] == 'error')
        {
            return makeResponse($saveVehicleType['result'],$saveVehicleType['message'],$saveVehicleType['code']);
        }

        $data = $this->authService->loginUserResponse();

        return makeResponse($saveVehicleType['result'],$saveVehicleType['message'],$saveVehicleType['code'],$data);
    }

    public function recommendedVehicle()
    {
        $getVehicleTypes =  $this->driverService->getRecommendedVehicleType();

        return makeResponse($getVehicleTypes['result'],$getVehicleTypes['message'],$getVehicleTypes['code'], $getVehicleTypes['data']);
    }
}
