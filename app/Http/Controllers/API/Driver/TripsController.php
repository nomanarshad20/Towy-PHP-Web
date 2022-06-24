<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Services\API\Driver\TripsService;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class TripsController extends Controller
{

    public $tripService;
    public function __construct(TripsService $tripService)
    {
        $this->tripService = $tripService;
    }

    public function index()
    {
        $upcomingBooking = $this->tripService->upcomingTrip();

        $pastBooking = $this->tripService->pastTrip();


        if(sizeof($upcomingBooking) == 0 && sizeof($pastBooking) == 0)
        {
            return makeResponse('error','Record Not Found',404);

        }

        $data = ['upcoming_booking' => $upcomingBooking, 'past_booking' => $pastBooking];

        return makeResponse('success','Trip History Retrieve Successfully',200,$data);
    }
}
