<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignBookingDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_booking_drivers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->nullable()
                ->constrained('bookings')->nullOnDelete();

            $table->foreignId('driver_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->integer('status')->comment('0=reject,1=accept,2=pending')->nullable();


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
        Schema::dropIfExists('assign_booking_drivers');
    }
}
