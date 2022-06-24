<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_unique_id');

            $table->foreignId('passenger_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('franchise_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('booking_type')->comment('book_now,book_later')->nullable();

            $table->string('pick_up_area');
            $table->string('pick_up_latitude');
            $table->string('pick_up_longitude');

            $table->date('pick_up_date')->nullable();
            $table->time('pick_up_time')->nullable();

            $table->string('drop_off_area')->nullable();
            $table->string('drop_off_latitude')->nullable();
            $table->string('drop_off_longitude')->nullable();

            $table->string('total_distance')->nullable();

            $table->text('description')->nullable();

            $table->string('payment_type')->comment('cash,wallet,payment_gateway');

            $table->decimal('estimated_fare')->nullable();
            $table->decimal('actual_fare')->comment('will be full fare')->nullable();

            $table->integer('driver_status')->comment('0=onway-to-pickup, 1=reached-pickup,2=start-ride,3=complete-ride')->nullable();
            $table->integer('ride_status')->comment('0=ride-request,1=ride-accepted,2=passenger-cancelled,3=admin-cancelled,4=completed,5=driver-cancelled')->nullable();

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
        Schema::dropIfExists('bookings');
    }
}
