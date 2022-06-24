<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateBookingRequest;
use App\Services\Admin\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public $bookingService;


    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }


    public function index()
    {
        return $this->bookingService->index();
    }

    public function create()
    {
        return $this->bookingService->create();
    }

    public function save(CreateBookingRequest $request)
    {
        return $this->bookingService->save($request);
    }

    public function edit($id)
    {
        return $this->bookingService->edit($id);
    }

    public function update(CreateBookingRequest $request)
    {
        return $this->bookingService->update($request);
    }

    public function delete(Request $request)
    {
        return $this->bookingService->delete($request);
    }
}
