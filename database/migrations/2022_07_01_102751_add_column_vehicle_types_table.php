<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnVehicleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->string('initial_distance_rate')->after('per_min_rate')->comment('per_min_rate')->nullable();
            $table->string('initial_time_rate')->after('initial_distance_rate')->comment('per_km_rate')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->dropColumn('initial_distance_rate');
            $table->dropColumn('initial_time_rate');
        });
    }
}
