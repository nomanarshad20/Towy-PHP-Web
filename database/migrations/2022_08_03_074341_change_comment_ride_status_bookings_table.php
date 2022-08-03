<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCommentRideStatusBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('ride_status')->comment('0=ride-request,1=ride-accepted,2=passenger-cancelled,3=admin-cancelled,4=completed,5=driver-cancelled,6=all_driver_rejected or ignored')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('ride_status')->comment('0=ride-request,1=ride-accepted,2=passenger-cancelled,3=admin-cancelled,4=completed,5=driver-cancelled')->nullable()->change();

        });
    }
}
