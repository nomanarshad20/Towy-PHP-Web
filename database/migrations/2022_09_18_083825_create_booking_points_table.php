<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_points', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->integer('type')->comment('0=driver_start_point,1=pick_up_point,2=ride_end_point')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_points');
    }
}
