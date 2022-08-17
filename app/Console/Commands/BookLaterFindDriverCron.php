<?php

namespace App\Console\Commands;

use App\Models\DriversCoordinate;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\AssignBookingDriver;
use App\Services\API\Passenger\RideService;
use App\Traits\SendFirebaseNotificationTrait;
//use App\Models\User;
//use App\Models\AssignBookingDriver;
use Carbon\Carbon;
use \Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookLaterFindDriverCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    use SendFirebaseNotificationTrait;
    public $rideService;

    protected $signature = 'findDriver:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RideService $rideService)
    {
        parent::__construct();
        $this->rideService = $rideService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        Log::info("########################====== Cron JOB Booking-Later Starts ======##########################");

        $bookingsAll    =   Booking::where('booking_type', 'book_later')
                            ->whereNull('driver_id')
                            ->where('ride_status', 0)->orWhereNull('ride_status')
                            ->where('ride_status', 0)->orWhereNull('ride_status')
                            ->get();

        /*
         $bookingsAll = DB::select(\DB::raw("Select * from bookings where booking_type='book_later'
                                    and driver_id=null or driver_id=0
                                    and driver_status=null or driver_status = 0
                                    and ride_status=null or ride_status=0"));
        */
        //Log::debug($bookingsAll);

        if (isset($bookingsAll) && $bookingsAll != null) {
            foreach ($bookingsAll as $booking) {
                //Log::debug($booking);
                $bookingId              = $booking->id;
                $bookingPassengerId     = $booking->passenger_id;
                $passengerRecord        = User::find($bookingPassengerId);
                //$timeZone               = "Asia/Kolkata";
                $timeZone               = "Asia/Karachi";
                if($passengerRecord->timezone){
                    $timeZone           = $passengerRecord->timezone;
                }
                //Log::info("********************* Book Later Booking Id : $bookingId *****************");
                if(isset($booking->pick_up_date) && $booking->pick_up_date != null && isset($booking->pick_up_time) && $booking->pick_up_time != null){
                    $rideDateTime       = $booking->pick_up_date . ' ' . $booking->pick_up_time;
                    $ridePickupDate     = Carbon::createFromFormat('Y-m-d H:i:s', $rideDateTime,$timeZone);
                    //Log::info("Cron JOB Booking-Later Ride DateTime : $ridePickupDate");
                    //$nextDayExpiryDate  = $ridePickupDate->addHours(24);
                    $nextDayExpiryDate  = Carbon::createFromFormat('Y-m-d H:i:s', $rideDateTime)->addHours(24);
                }else{
                    continue;
                }
                //Development Time Zone
                $now                    = Carbon::now()->setTimezone($timeZone);
                $ridePickupDateTime     = $ridePickupDate->setTimezone($timeZone);

                //Log::info("Current DateTime : $now");

                //Log::info("Expiry Date : $nextDayExpiryDate");

                //Log::info("Cron JOB Booking-Later Time Zone Update DateTime : $ridePickupDateTime");
                //Get Time Difference
                $diff_in_minutes        = $now->diffInMinutes($ridePickupDateTime);
                //Log::info("Difference in Time Minutes of Start Ride : $diff_in_minutes");
                $expiryDateDiff         = $ridePickupDateTime->diffInMinutes($nextDayExpiryDate);
                //Log::info("Next Day Difference in Time in Minutes : $expiryDateDiff");

                if ($now->lessThanOrEqualTo($ridePickupDateTime && $diff_in_minutes < 2))
                {

//                    Log::info("Time Difference Is Less than 30 MIns and Difference in Minutes : ".$diff_in_minutes);
                    $availableDrivers    = $this->rideService->findNearestDrivers($booking);
//                    Log::info("Driver Found : ".$availableDrivers['result']);
                    if (isset($availableDrivers) && $availableDrivers['result'] == 'success')
                    {
                        //Log::debug($availableDrivers['data']);
                        $saveDrivers     = $this->rideService->saveAvailableDrivers($availableDrivers['data'], $booking);
                        if ($saveDrivers['result'] == 'error') {
                            $saveDrivers = $this->rideService->saveAvailableDrivers($availableDrivers['data'], $booking);
                        }
                        //Log::info("Driver Saved For Ride Queue : ".$saveDrivers['result']);
                        if (isset($saveDrivers['data']['id']) && $saveDrivers['data']['id'] != null && $saveDrivers['result'] == 'success')
                        {
                            $driverId    = $saveDrivers['data']['id'];
//                            Log::info("******* Book-Later Booking Id : $bookingId AND Available DRIVER ID: $driverId *****************");
                            //$this->sendNotifications($driverId,$booking);  //continue;
                            $driverRecord           = User::find($driverId);
                            if(isset($driverRecord) && $driverRecord->driverCoordinate->status == 1)
                            {
                                $driverRecord->driverCoordinate->update(['status' => 3]);
                                //$updateCoordinates  = DriversCoordinate::where('driver_id',$driverId)->update(['status'=>3]);

//                                Log::info("******* DriversCoordinates Updated *******");

                                $bookingDriver      = AssignBookingDriver::where('driver_id',$driverId)
                                                        ->where('booking_id',$booking->id)
                                                        ->whereNull('status')
                                                        ->update(['ride_send_time' => Carbon::now()->format('Y-m-d H:i:s')]);

//                                Log::info("******* AssignBookingDriver Updated *******");

                                $notification_type  = 11;
                                if ($driverRecord->fcm_token) {
                                    $fcmToken                   = ['fcm_token' => $driverRecord->fcm_token];
                                    $sendNotificationToDriver   = $this->rideRequestNotification($fcmToken, $booking, $notification_type);

//                                    Log::info("*******FCM TOKEN $driverRecord->fcm_token Notification Sent *******");
                                }

//                                Log::info("################ BOOK_LATER NOTIFICATIONS SENT FROM CRON JOB AND DRIVER ID : $driverId ################");
                            }
                        }
                    }
                }else if($now->greaterThan($ridePickupDateTime) && $diff_in_minutes > $expiryDateDiff)
                {
                    $ride_status            = "3";
                    $other_cancel_reason    = "Book-later Ride Time Expired and CronJob Update Ride-Status to Cancel Ride";
                    $bookingUpdate          = \DB::table('bookings')->where('id', $bookingId)
                                                ->update(array('ride_status' =>  $ride_status, 'other_cancel_reason' => $other_cancel_reason));
//                    Log::info("Cron JOB Booking-Later Ride Expired : $bookingId");
                }
            }
        }
//        Log::info("############====== Cron JOB Booking-Later ENDs ======##############");
    }

   /* public function sendNotifications($driverID,$booking){

        $driverRecord           = User::find($driverID);
        if(isset($driverRecord) && $driverRecord->status == 1)
        {
            //$driverRecord->driverCoordinate->update(['status' => 3]);
            $updateCoordinates = DriversCoordinate::where('driver_id',$driverID)->update(['status'=>3]);

            AssignBookingDriver::where('driver_id',$driverID)
                ->where('booking_id',$booking->id)
                ->whereNull('status')
                ->update(['ride_send_time' => Carbon::now()->format('Y-m-d H:i:s')]);

            $notification_type = 11;
            if ($driverRecord->fcm_token) {
                $fcmToken       = ['fcm_token' => $driverRecord->fcm_token];
                $sendNotificationToDriver = $this->rideRequestNotification($fcmToken, $booking, $notification_type);

                Log::debug($sendNotificationToDriver);
            }
            Log::info("################ BOOK_LATER NOTIFICATIONS SENT FROM CRON JOB AND DRIVER ID : $driverID################");
            return true;
        }
        return false;
    }
   */
}
