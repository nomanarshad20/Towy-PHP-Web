<?php

namespace App\Http\Controllers\API\Passenger;

use App\Http\Controllers\Controller;
use App\Services\API\Passenger\TripHistoryService;
use Illuminate\Http\Request;

class TripHistoryController extends Controller
{

    public $tripService;
    public function __construct(TripHistoryService $tripHistoryService)
    {
        $this->tripService = $tripHistoryService;
    }


    public function history()
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
