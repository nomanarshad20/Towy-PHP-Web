<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelColumnsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('cancel_ride_time')->comment('in minutes')->after('search_range')->nullable();
            $table->string('passenger_cancel_fine_amount')->after('cancel_ride_time')->comment('%')->nullable();
            $table->string('driver_cancel_fine_amount')->after('passenger_cancel_fine_amount')->comment('%')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('cancel_ride_time');
            $table->dropColumn('passenger_cancel_fine_amount');
            $table->dropColumn('driver_cancel_fine_amount');
        });
    }
}
