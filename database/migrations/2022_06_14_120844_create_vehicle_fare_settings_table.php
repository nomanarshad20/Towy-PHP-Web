<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleFareSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_fare_settings', function (Blueprint $table) {
            $table->id();

            $table->string('min_fare')->nullable();
            $table->string('per_km_rate')->nullable();
            $table->string('per_min_rate')->nullable();
            $table->string('tax_rate')->comment('GST %')->nullable();
            $table->string('waiting_price_per_min')->nullable();


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
        Schema::dropIfExists('vehicle_fare_settings');
    }
}
