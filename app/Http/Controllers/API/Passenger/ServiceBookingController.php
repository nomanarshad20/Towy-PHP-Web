<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Passenger\ServiceBookingCreate;
use App\Services\API\Passenger\ServiceBookingService;
use Illuminate\Http\Request;

class ServiceBookingController extends Controller
{
    public $serviceBooking;
    public function __construct(ServiceBookingService $bookingService)
    {
        $this->serviceBooking = $bookingService;
    }

    public function create(ServiceBookingCreate $request)
    {
        try{
          $createBooking = $this->serviceBooking->create($request);
        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Create Booking: '.$e,'500');
        }

    }
}
