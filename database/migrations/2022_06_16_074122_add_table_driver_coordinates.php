<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableDriverCoordinates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers_coordinates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('latitude',11,2)->nullable();
            $table->decimal('longitude',11,2)->nullable();
            $table->string('bearing')->nullable();
            $table->string('city')->nullable();
            $table->string('area_name')->nullable();
            $table->integer('status')->default(0)->comment('0=offline,1=online,2=onride,3=ride_request');
            $table->rememberToken();
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
        Schema::dropIfExists('drivers_coordinates');
    }
}
