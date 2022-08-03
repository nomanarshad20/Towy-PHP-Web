<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRideSendAssignBookingDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_booking_drivers', function (Blueprint $table) {
            $table->string('ride_send_time')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_booking_drivers', function (Blueprint $table) {
            $table->dropColumn('ride_send_time');
        });
    }
}
