<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Admin\BookingService;
use Illuminate\Http\Request;

class FareDistributionsController
{
    public $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function bookingFareDistributes()
    {
        $booking = Booking::with('bookingDetail', 'driver', 'passenger', 'franchise')->orderBy('id', 'desc')->first();
        if (isset($booking) && $booking != null) {
            $responseWallet = $this->bookingService->updateFareWallets($booking);
            if ($responseWallet && $responseWallet['result'] == "success" && isset($responseWallet['data'])) {
                //dd($responseWallet['data']);
                return makeResponse('success', 'Fare calculate Successfully', 200, $responseWallet['data']);
            }else {
                return makeResponse('error', $responseWallet['message'], 401);

            }

        }else
            return makeResponse('error', "Booking not found.", 401);

    }

}
