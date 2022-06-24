<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('min_fare')->nullable();
            $table->string('per_km_rate')->nullable();
            $table->string('per_min_rate')->nullable();
            $table->string('tax_rate')->comment('GST %')->nullable();
            $table->text('description')->nullable();
            $table->string('waiting_price_per_min')->nullable();
            $table->boolean('status')->comment('0=inactive,1=active')->default(1);

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->nullable();


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
        Schema::dropIfExists('vehicle_types');
    }
}
