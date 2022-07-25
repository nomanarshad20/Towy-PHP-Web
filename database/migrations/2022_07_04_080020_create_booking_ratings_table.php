<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->nullable()->constrained('bookings')
                ->nullOnDelete();

            $table->foreignId('receiver_id')->nullable()->constrained('users')
                ->nullOnDelete();

            $table->foreignId('giver_id')->nullable()->constrained('users')
                ->nullOnDelete();

            $table->integer('rating');
            $table->text('description')->nullable();

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
        Schema::dropIfExists('booking_ratings');
    }
}
