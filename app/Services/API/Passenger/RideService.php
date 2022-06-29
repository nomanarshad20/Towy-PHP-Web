<?php


namespace App\Services\API\Passenger;

use App\Models\AssignBookingDriver;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Setting;
use App\Models\User;
use App\Models\VehicleType;
use App\Traits\BookingResponseTrait;
use App\Traits\FindDistanceTraits;
use App\Traits\FindDriverTrait;
//use App\Traits\UserWalletTraits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RideService
{

    use FindDistanceTraits;
    use FindDriverTrait;
    use BookingResponseTrait;
    //use UserWalletTraits;

    public function saveBooking($request)
    {
        DB::beginTransaction();

        //finding distance
//        try {
//            $findingDistance = $this->getDistance($request->pick_up_latitude, $request->pick_up_longitude,
//                $request->drop_off_latitude, $request->drop_off_longitude);
//
//
//            $distanceInKm = str_replace(',', '', str_replace('km', '', $findingDistance['text']));
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            $response = ['result' => 'error', 'message' => 'Error in Finding Distance: ' . $e, 'code' => 500];
//            return $response;
//        }

        //calculating estimated fare and getting vehicle type record
//        try {
//
//            $gettingVehicleTypeRecords = $this->gettingVehicleTypeRecords(trim($distanceInKm));
//
//
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            $response = ['result' => 'error', 'message' => 'Error in Finding Distance: ' . $e, 'code' => 500];
//            return $response;
//        }

        //savingData in Booking Table
        try {

            $pick_up_date = $pick_up_time = null;
            if ($request->booking_type == 'book_later') {
                $pick_up_date = Carbon::parse($request->pick_up_date)->format('Y-m-d');
                $pick_up_time = Carbon::parse($request->pick_up_time)->format('H:i:S');
            }


            $bookingArray = [
                'booking_unique_id' => uniqid('TOTO-'),
                'passenger_id' => Auth::user()->id,
                'vehicle_type_id' => $request->vehicle_type_id,
                'booking_type' => $request->booking_type,
                'pick_up_area' => $request->pick_up_area,
                'pick_up_latitude' => $request->pick_up_latitude,
                'pick_up_longitude' => $request->pick_up_longitude,
                'pick_up_date' => $pick_up_date,
                'pick_up_time' => $pick_up_time,
                'drop_off_area' => $request->drop_off_area,
                'drop_off_latitude' => $request->drop_off_latitude,
                'drop_off_longitude' => $request->drop_off_longitude,
//                'total_distance' => $distanceInKm,
                'total_distance' => $request->total_distance,
                'payment_type' => $request->payment_type,
//                'estimated_fare' => $gettingVehicleTypeRecords['estimated_fare'],
                'estimated_fare' => $request->estimated_fare,
                'actual_fare' => 0,
                'ride_status' => 0,
            ];

            if($request->booking_id)
            {
                $findBooking = Booking::find($request->booking_id);
                if($findBooking)
                {
                    $bookingTable = $findBooking;

                    $bookingTable->update($bookingArray);
                }
            }
            else{

                $bookingTable = Booking::create($bookingArray);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['result' => 'error', 'message' =>  'Error in Creating Booking Request: ' . $e, 'code' => 500];
            return $response;
        }

        try {

            $vehicleTypeRecord  = VehicleType::find($request->vehicle_type_id);

            if($vehicleTypeRecord)
            {

                $setting  = Setting::first();
                $cancel_allowed_time = 0;
                $cancel_ride_passenger_fine_amount = 0;
                $cancel_ride_driver_fine_amount = 0;
                if($setting)
                {
                    $cancel_allowed_time = $setting->cancel_ride_time;
                    $cancel_ride_passenger_fine_amount = $setting->cancel_ride_passenger_fine_amount;
                    $cancel_ride_driver_fine_amount = $setting->cancel_ride_driver_fine_amount;
                }


                $bookingDetailArray = [
                    'booking_id' => $bookingTable->id,
                    'waiting_price_per_min' => $vehicleTypeRecord->waiting_price_per_min,
                    'vehicle_tax' => $vehicleTypeRecord->tax_rate,
                    'vehicle_per_km_rate' => $vehicleTypeRecord->per_km_rate,
                    'vehicle_per_min_rate' => $vehicleTypeRecord->per_min_rate,
                    'min_vehicle_fare' => $vehicleTypeRecord->min_fare,
                    'cancel_ride_time' => $cancel_allowed_time,
                    'cancel_ride_passenger_fine_amount' => $cancel_ride_passenger_fine_amount,
                    'cancel_ride_driver_fine_amount' => $cancel_ride_driver_fine_amount
                ];


                if($bookingTable->bookingDetail)
                {
                    $bookingDetail = $bookingTable->bookingDetail;

                    $bookingDetail->update($bookingDetailArray);

                }
                else{
                    $bookingDetail = $bookingTable->bookingDetail()->create($bookingDetailArray);
                }

                $bookingTable->refresh();

            }
            else{
                DB::rollBack();
                $response = ['result'=>"error",'message'=>'Vehicle Type Not Found','code'=>500];
                return $response;
            }


            DB::commit();



            $bookingData = $this->driverBookingResponse($bookingTable);

            $response = ['result' => 'success', 'data' => $bookingData, 'message' => 'Booking Created Successfully'];

            return $response;

        } catch (\Exception $e) {
            DB::rollBack();
            $response =  ['result' => 'error', 'message' => 'Error in Creating Booking Detail Request: ' . $e, 'code' => 500];
            return $response;
        }

    }


    public function findNearestDrivers($booking)
    {
        //calculating estimated fare and getting vehicle type record
        try {

            $gettingDrivers = $this->fetchDrivers($booking);


            if (sizeof($gettingDrivers) > 0) {
                $response = ['result' => 'success', 'data' => $gettingDrivers, 'message' => 'Nearest Drivers Find Successfully'];
            } else {
                $response = ['result' => 'error', 'data' => $booking, 'message' => 'Driver Not Found. Try Again Later','code'=>404];
            }


            return $response;

        } catch (\Exception $e) {
//            DB::rollBack();
            $response = ['result' => 'error', 'data' => $booking, 'message' => 'Error in find nearest drivers : ' . $e,'code'=>500];
            return $response;
        }
    }

    public function saveAvailableDrivers($driversList,$booking)
    {

        try{
            $firstDriver = null;
            foreach ($driversList as $key => $driver)
            {

                if($key == 0)
                {
                    $firstDriver = $driver;
                }


                AssignBookingDriver::create(['booking_id'=>$booking['id'],
                    'driver_id'=>$driver['id']
                ]);
            }

            $response = ['result'=>'success','message'=>'Driver Save','code'=>200,'data'=>$firstDriver];
            return $response;
        }
        catch (\Exception $e)
        {
            $response = ['result'=>'error','message'=>'Error in Saving Driver: '.$e,'code'=>500];
            return $response;
        }




    }

}
