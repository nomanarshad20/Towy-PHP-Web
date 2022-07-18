<?php


namespace App\Services\API\Driver;


use App\Models\Booking;
use App\Models\DriversCoordinate;
use App\Traits\BookingResponseTrait;
use Illuminate\Support\Facades\Auth;

class DriverService
{
    use BookingResponseTrait;

    public function changeStatus($request)
    {
        try {
            $saveStatus = DriversCoordinate::where('driver_id',Auth::user()->id)
                ->update(['status'=>$request->availability_status]);
//            $saveStatus = Auth::user()->driverCoordinate->update(['status' => $request->availability_status]);

            $response = ['result' => 'success', 'message' => 'Status Update', 'code' => 200];
            return $response;
        } catch (\Exception $e) {
            $response = ['result' => 'error', 'message' => 'Error in Updating Status: ' . $e, 'code' => 500];
            return $response;
        }
    }

    public function getStatus()
    {
        $driverStatus = Auth::user()->driverCoordinate;


        if ($driverStatus->status == 2) {
            $findBooking = Booking::where('driver_id', Auth::user()->id)
//                ->where('ride_status', 1)
                ->where(function ($query){
                    $query->where('ride_status',1)
                        ->orwhere(function($query1){
                            $query1->where('is_passenger_rating_given',0)
                                ->where('is_driver_rating_given',0);
                        });
                })
                ->whereNotNull('driver_status')
                ->first();


            if (!$findBooking) {
                $response = ['result' => 'error', 'message' => 'No Active Booking Found', 'code' => 404];
                return $response;
            }

            $currentBooking = $this->driverBookingResponse($findBooking);

            $response = ['result' => 'success', 'message' => 'Booking Found', 'code' => 200, 'data' => $currentBooking];
        }
        else{
            $response = ['result' => 'success', 'message' => 'Data Found', 'code' => 200];
        }

        return $response;

    }
}
