<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->string('driver_waiting_time')->nullable();

            $table->float('driver_arrival_estimate_time');
            $table->string('driver_arrival_estimate_distance');

            $table->float('waiting_price_per_min')->nullable();
            $table->float('vehicle_tax')->nullable();
            $table->float('vehicle_per_km_rate')->default(0);
            $table->float('vehicle_per_min_rate')->default(0);
            $table->float('min_vehicle_fare');

            $table->string('p2p_before_pick_up_distance')->nullable();
            $table->string('p2p_after_pick_up_distance')->nullable();
            $table->float('peak_factor_rate')->nullable();

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
        Schema::dropIfExists('booking_details');
    }
}
