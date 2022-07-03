<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeBookingDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->string('mobile_final_distance')->after('passenger_wallet_paid')->nullable();
            $table->string('mobile_initial_distance')->after('mobile_final_distance')->nullable();
            $table->dropColumn('driver_arrival_estimate_time');
            $table->dropColumn('driver_arrival_estimate_distance');
            $table->string('ride_pickup_time')->after('driver_waiting_time')->nullable();
            $table->string('ride_start_time')->after('ride_pickup_time')->nullable();
            $table->string('ride_end_time')->after('ride_start_time')->nullable();

            $table->string('total_minutes_to_reach_pick_up_point')->after('ride_end_time')->nullable();
            $table->string('total_ride_minutes')->after('total_minutes_to_reach_pick_up_point')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('mobile_final_distance');
            $table->dropColumn('mobile_initial_distance');
            $table->double('driver_arrival_estimate_time')->after('driver_waiting_time')->nullable();
            $table->string('driver_arrival_estimate_distance')->after('driver_arrival_estimate_time')->nullable();
            $table->dropColumn('ride_pickup_time');
            $table->dropColumn('ride_start_time');
            $table->dropColumn('ride_end_time');
            $table->dropColumn('total_minutes_to_reach_pick_up_point');
            $table->dropColumn('total_ride_minutes');
        });
    }
}
