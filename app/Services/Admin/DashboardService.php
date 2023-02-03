<?php


namespace App\Services\Admin;


use App\Models\Booking;
use App\Models\User;
use App\Services\API\Passenger\StripeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public $stripeService;
    public function __construct(StripeService  $service)
    {
        $this->stripeService = $service;
    }

    public function index()
    {
        $users = User::select('user_type', DB::raw('count(*) as total'))
            ->groupBy('user_type')
            ->get()
            ->toArray();

        $bookings = Booking::select('ride_status','booking_type', DB::raw('count(*) as total'))
            ->groupBy('ride_status','booking_type')
            ->whereNotNull('booking_type')
            ->get()
            ->toArray();
        $totalBooking = array_sum(array_column($bookings, 'total'));

        $bookingsData = Booking::with('bookingDetail')->get();
        $bookingData =  $bookingsData->
        whereBetween('created_at',[Carbon::now()->subMonth(6),Carbon::now()])
            ->groupBy(function ($user) {
                return Carbon::parse($user->created_at)->format('M/Y');
            })
            ->map(function ($group) {
                return $group->count();
            });

        $bookingArray =  array();
        foreach($bookingData as $key=>$bookingRecord)
        {
            $bookingArray[] = ['month'=>$key,'count'=>$bookingRecord];
        }


        $getBalance = $this->stripeService->getBalance();

        $amount = $getBalance["data"];

        return view('admin.dashboard.dashboard', compact('users',
            'bookings','totalBooking','bookingArray','amount'));
    }
}
