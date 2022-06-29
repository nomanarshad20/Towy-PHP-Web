<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingColumnBookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('driver_status')->comment('0=onway-to-pickup, 1=reached-pickup,2=start-ride,3=complete-ride,4=fare-collected')->nullable()->change();


            $table->boolean('is_passenger_rating_given')->default(0)
                ->after('fine_amount')->nullable();
            $table->boolean('is_driver_rating_given')->default(0)
                ->after('is_passenger_rating_given')->nullable();
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
            $table->integer('driver_status')->comment('0=onway-to-pickup, 1=reached-pickup,2=start-ride,3=complete-ride')->nullable()->change();

            $table->dropColumn('is_passenger_rating_given');
            $table->dropColumn('is_driver_rating_given');
        });
    }
}
