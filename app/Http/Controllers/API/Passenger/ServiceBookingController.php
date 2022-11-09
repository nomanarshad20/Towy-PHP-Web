<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Passenger\ServiceBookingCreate;
use App\Services\API\Passenger\ServiceBookingService;
use App\Traits\FindServiceDriverTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceBookingController extends Controller
{
    public $serviceBooking;
    public function __construct(ServiceBookingService $bookingService)
    {
        $this->serviceBooking = $bookingService;
    }

    public function create(ServiceBookingCreate $request)
    {
        DB::beginTransaction();
        try{
          $createBooking = $this->serviceBooking->create($request);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return makeResponse('error','Error in Create Booking: '.$e,'500');
        }

        if ($createBooking['result'] == 'error') {
            DB::rollBack();
            return makeResponse('error', $createBooking['message'], 500);
        }

        //find drivers according to pick up lat and lng
        try{
            $availableDrivers = $this->serviceBooking->findNearestDrivers($createBooking['data'],$request->services);

            if ($availableDrivers['result'] == 'error') {
                DB::rollBack();
                return makeResponse('error', $availableDrivers['message'], $availableDrivers['code'], $availableDrivers['data']);
            }
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return makeResponse('error','Error in Driver Find: '.$e,'500');
        }

        try{
            $saveDrivers = $this->serviceBooking->saveAvailableDrivers($availableDrivers['data'], $createBooking['data'],);

            if ($saveDrivers['result'] == 'error') {
                DB::rollBack();
                return makeResponse('error', $saveDrivers['message'], $saveDrivers['code']);
            }
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return makeResponse('error','Error in Driver Save: '.$e,'500');
        }

        try{
            $fareArray = $this->serviceBooking->calculateEstimatedFare($availableDrivers['data'], $createBooking['data']);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            return makeResponse('error','Error in Create Estimated Fare: '.$e,'500');
        }
        DB::commit();

        $data = [
            'bookingRecord' => $createBooking['data'],
            'driverList' => $fareArray
        ];

        return makeResponse('success','Booking Created',200,$data);



    }
}
