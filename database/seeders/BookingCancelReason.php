<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BookingCancelReason as CancelReason;

class BookingCancelReason extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CancelReason::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        DB::statement(
            DB::raw("
INSERT INTO `booking_cancel_reasons` (`id`, `reason`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 'I have urgent problem', 'driver', '2021-12-20 00:49:40', '2021-12-20 00:49:40'),
(2, 'Passenger abused me', 'driver', '2021-12-20 00:49:50', '2021-12-20 00:49:50'),
(3, 'Passenger is taking too long,I am at pickup point', 'driver', '2021-12-20 00:49:59', '2021-12-20 00:49:59'),
(4, 'Passenger got urgent problem', 'driver', '2021-12-20 00:50:08', '2021-12-20 00:50:08'),
(5, 'Other', 'driver', '2021-12-20 00:50:17', '2021-12-20 00:50:17'),
(6, 'The listed vehicle was not what hoped for', 'passenger', '2021-12-20 02:49:52', '2021-12-20 02:49:52'),
(7, 'The driver was too far away', 'passenger', '2021-12-20 02:50:02', '2021-12-20 02:50:02'),
(8, 'Driver Asked me to cancel', 'passenger', '2021-12-20 02:50:13', '2021-12-20 02:50:13'),
(9, 'Other', 'passenger', '2021-12-20 02:50:21', '2021-12-20 02:50:21');
           "));
    }


}
