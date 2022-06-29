<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelColumnBookingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->string('cancel_ride_time')->after('peak_factor_rate')->nullable();
            $table->string('cancel_ride_passenger_fine_amount')->after('cancel_ride_time')->nullable();
            $table->string('cancel_ride_driver_fine_amount')->after('cancel_ride_passenger_fine_amount')->nullable();
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
            $table->dropColumn('cancel_ride_time');
            $table->dropColumn('cancel_ride_passenger_fine_amount');
            $table->dropColumn('cancel_ride_driver_fine_amount');
        });
    }
}
