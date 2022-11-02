<?php


namespace App\Services\API\Passenger;


use App\Models\Service;

class ServiceService
{
    public function index()
    {
        $data = Service::select('id', 'name', 'base_rate', 'description', 'image')->get();

        if (sizeof($data) > 0) {
            return makeResponse('success', 'Service Found', 200, $data);
        } else {
            return makeResponse('error', 'No Record Found', 404);
        }
    }

    public function createBooking()
    {

    }
}
