<?php

namespace App\Console\Commands;

use App\Models\AssignBookingDriver;
use App\Models\Booking;
use App\Models\DriversCoordinate;
use App\Models\User;
use App\Services\API\Socket\RideAcceptRejectService;
use App\Traits\SendFirebaseNotificationTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TransferRideRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:ride';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a CRON JOB which is used to transfer Booking Request To other driver in case of current driver is not responding';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $rideAcceptRejectService;
    use SendFirebaseNotificationTrait;
    public function __construct(RideAcceptRejectService $service)
    {
        parent::__construct();
        $this->rideAcceptRejectService = $service;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $findBookings = Booking::where('booking_type', 'book_now')
            ->whereNull('driver_id')
            ->where('ride_status', 0)
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->get();




        foreach ($findBookings as $booking) {
            $currentTime = Carbon::now();

            $getCurrentAssignDriver = AssignBookingDriver::where('booking_id', $booking->id)
                ->whereNotNull('ride_send_time')
                ->whereNull('status')->orderBy('id', 'asc')
                ->first();

            if($getCurrentAssignDriver)
            {
                $driverTime = Carbon::parse($getCurrentAssignDriver->ride_send_time);
                $getSecondDiff = $currentTime->diffInSeconds($driverTime);

                if($getSecondDiff > 30)
                {
                    DriversCoordinate::where('driver_id',$getCurrentAssignDriver->driver_id)
                        ->update(['status'=>0]);

//                    $getCurrentAssignDriver->update(['status',0]);

                    $driverFind = User::find($getCurrentAssignDriver->driver_id);

                    $findNextDriver = $this->rideAcceptRejectService->findNextDriver($booking,$driverFind);

                    if($findNextDriver)
                    {
//                        if($findNextDriver->)
                        $driverRecord = User::find($findNextDriver->driver_id);

                        $driverRecord->driverCoordinate->update(['status' => 3]);

                        AssignBookingDriver::where('driver_id',$findNextDriver->driver_id)
                            ->where('booking_id',$booking->id)
                            ->whereNull('status')
                            ->update(['ride_send_time'=>Carbon::now()->format('Y-m-d H:i:s')]);

                        $notification_type = 11;
                        if ($driverRecord->fcm_token) {
                            $fcmToken = ['fcm_token' => $driverRecord->fcm_token];
                            $sendNotificationToDriver = $this->rideRequestNotification($fcmToken, $booking, $notification_type);
                        }
                    }
                    else{
                        $booking->update(['ride_status'=>6]);
                        $notification_type = 12;

                        if ($booking->passenger->fcm_token) {
                            $fcmToken = ['fcm_token' => $booking->passenger->fcm_token];
                            $sendFCMNotification = $this->bookingEndNotification($fcmToken, $booking, $notification_type);
                        }
                    }




                }

            }

        }
    }
}
