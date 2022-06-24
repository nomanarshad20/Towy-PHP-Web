<?php


namespace App\Services\Admin;


use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\User;
use App\Models\VehicleFareSetting;
use App\Models\VehicleType;
use App\Traits\FindDistanceTraits;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    use FindDistanceTraits;

    public function index()
    {
        $data = Booking::with('bookingDetail')->get();

        return view('admin.booking.listing', compact('data'));
    }

    public function create()
    {
        $passengers = User::where('user_type', 1)->where('is_verified', 1)->get();
        $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
//        $vehicleTypes = VehicleType::where('status', 1)->get();

        return view('admin.booking.create', compact('passengers', 'franchises'));
    }

    public function save($request)
    {
        DB::beginTransaction();

        //finding distance
        try {
            $findingDistance = $this->getDistance($request->pick_up_lat, $request->pick_up_lng,
                $request->drop_off_lat, $request->drop_off_lng);


            $distanceInKm = str_replace(',', '', str_replace('km', '', $findingDistance['text']));

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Finding Distance: ' . $e, 500);
        }

        //calculating estimated fare and getting vehicle type record
        try {
            $gettingVehicleTypeRecords = $this->gettingVehicleTypeRecords(trim($distanceInKm));
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Calculating Estimated Fare & Getting Vehicle Type Record: ' . $e, 500);
        }


        //savingData in Booking Table
        try {

            $pick_up_date = $pick_up_time = null;
            if ($request->booking_type == 'book_later') {
                $pick_up_date = Carbon::parse($request->pick_up_date)->format('Y-m-d');
                $pick_up_time = Carbon::parse($request->pick_up_time)->format('H:i:S');
            }


            $bookingTable = Booking::create([
                'booking_unique_id' => uniqid('TOTO-'),
                'passenger_id' => $request->passenger_id,
                'vehicle_type_id' => 1,
                'booking_type' => $request->booking_type,
                'pick_up_area' => $request->pick_up_area,
                'pick_up_latitude' => $request->pick_up_lat,
                'pick_up_longitude' => $request->pick_up_lng,
                'pick_up_date' => $pick_up_date,
                'pick_up_time' =>$pick_up_time,
                'drop_off_area' => $request->drop_off_area,
                'drop_off_latitude' => $request->drop_off_lat,
                'drop_off_longitude' => $request->drop_off_lng,
                'total_distance' => $distanceInKm,
                'payment_type' => 'cash',
                'estimated_fare' => $gettingVehicleTypeRecords[0]['estimated_fare'],
                'actual_fare' => 0,
                'ride_status' => 0
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Booking Request: ' . $e, 500);
        }

        try {
            $bookingDetail = BookingDetail::create([
                'booking_id' => $bookingTable->id,
                'waiting_price_per_min' => $gettingVehicleTypeRecords[0]['waiting_price_per_min'],
                'vehicle_tax' => $gettingVehicleTypeRecords[0]['tax_rate'],
                'vehicle_per_km_rate' => $gettingVehicleTypeRecords[0]['per_km_rate'],
                'vehicle_per_min_rate' => $gettingVehicleTypeRecords[0]['per_min_rate'],
                'min_vehicle_fare' => $gettingVehicleTypeRecords[0]['min_fare']
            ]);

            DB::commit();

            return makeResponse('success', 'Booking Created Successfully' , 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return makeResponse('error', 'Error in Creating Booking Detail Request: ' . $e, 500);
        }

    }


    public function edit($id)
    {
        $data = Booking::find($id);

        if($data)
        {
            $passengers = User::where('user_type', 1)->where('is_verified', 1)->get();
            $franchises = User::where('user_type', 3)->where('is_verified', 1)->get();
            $drivers = User::where('user_type',2)->where('is_verified', 1)->get();
            return view('admin.booking.edit',compact('passengers','drivers','franchises','data'));
        }
        else{
            return redirect()->route('bookingListing')->with('error','Record Not Found');
        }
    }

    public function update($request)
    {
        DB::beginTransaction();

        $data  = Booking::find($request->id);

        if($data)
        {
            //finding distance
            try {
                $findingDistance = $this->getDistance($request->pick_up_lat, $request->pick_up_lng,
                    $request->drop_off_lat, $request->drop_off_lng);


                $distanceInKm = str_replace(',', '', str_replace('km', '', $findingDistance['text']));

            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Finding Distance: ' . $e, 500);
            }

            //calculating estimated fare and getting vehicle type record
            try {
                $gettingVehicleTypeRecords = $this->gettingVehicleTypeRecords(trim($distanceInKm));
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Calculating Estimated Fare & Getting Vehicle Type Record: ' . $e, 500);
            }

            //savingData in Booking Table
            try {

                $pick_up_date = $pick_up_time = null;
                if ($request->booking_type == 'book_later') {
                    $pick_up_date = Carbon::parse($request->pick_up_date)->format('Y-m-d');
                    $pick_up_time = Carbon::parse($request->pick_up_time)->format('H:i:s');
                }


                $data->update([
                    'booking_unique_id' => uniqid('TOTO-'),
                    'passenger_id' => $request->passenger_id,
                    'vehicle_type_id' => 1,
                    'booking_type' => $request->booking_type,
                    'pick_up_area' => $request->pick_up_area,
                    'pick_up_latitude' => $request->pick_up_lat,
                    'pick_up_longitude' => $request->pick_up_lng,
                    'pick_up_date' => $pick_up_date,
                    'pick_up_time' =>$pick_up_time,
                    'drop_off_area' => $request->drop_off_area,
                    'drop_off_latitude' => $request->drop_off_lat,
                    'drop_off_longitude' => $request->drop_off_lng,
                    'total_distance' => $distanceInKm,
                    'payment_type' => 'cash',
                    'estimated_fare' => $gettingVehicleTypeRecords['estimated_fare'],
                    'actual_fare' => 0,
                    'ride_status' => $request->ride_status
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Updating Booking Request: ' . $e, 500);
            }

            try {
                $data->bookingDetail->update ([
                    'waiting_price_per_min' => $gettingVehicleTypeRecords['waiting_price_per_min'],
                    'vehicle_tax' => $gettingVehicleTypeRecords['tax_rate'],
                    'vehicle_per_km_rate' => $gettingVehicleTypeRecords['per_km_rate'],
                    'vehicle_per_min_rate' => $gettingVehicleTypeRecords['per_min_rate'],
                    'min_vehicle_fare' => $gettingVehicleTypeRecords['min_fare']
                ]);

                DB::commit();

                return makeResponse('success', 'Booking Updated Successfully' , 200);

            } catch (\Exception $e) {
                DB::rollBack();
                return makeResponse('error', 'Error in Updating Booking Detail Request: ' . $e, 500);
            }
        }
        else{
            return makeResponse('error', 'Record Not Found', 404);
        }
    }

    public function delete($request)
    {
        $data = Booking::find($request->id);

        if($data)
        {
            try{
                $data->delete();
                return makeResponse('success', 'Record Deleted', 200);

            }
            catch (\Exception $e)
            {
                return makeResponse('error', 'Error in Delete Booking Record: '.$e, 500);

            }
        }
        else{
            return makeResponse('error', 'Record Not Found', 404);
        }
    }
}
