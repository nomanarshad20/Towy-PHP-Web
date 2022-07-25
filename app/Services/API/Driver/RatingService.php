<?php


namespace App\Services\API\Driver;


use App\Models\Booking;
use App\Models\BookingRating;
use App\Traits\BookingResponseTrait;
use Illuminate\Support\Facades\Auth;

class RatingService
{
    use BookingResponseTrait;

    public function saveRating($request)
    {
        try{

            $findBooking = Booking::where('id',$request->booking_id)
                ->where('passenger_id',$request->passenger_id)
                ->where('driver_id',Auth::user()->id)->first();

            if(!$findBooking)
            {
                return makeResponse('error','Record Not Found',404);
            }


            BookingRating::create(['booking_id'=>$request->booking_id,'receiver_id'=>$request->passenger_id,
                'giver_id'=>Auth::user()->id,'rating'=>$request->rating,'description'=>$request->message
            ]);

            $findBooking->is_driver_rating_given = 1;

            $findBooking->save();


            $findBooking->driver->driverCoordinate->update(['status'=>1]);


            $bookingResponse = $this->driverBookingResponse($findBooking);

            return makeResponse('success','Rating Save Successfully',200,(object)$bookingResponse);

        }
        catch (\Exception $e)
        {
            return makeResponse('error','Error in Saving Rating: '.$e,500);

        }
    }
}
