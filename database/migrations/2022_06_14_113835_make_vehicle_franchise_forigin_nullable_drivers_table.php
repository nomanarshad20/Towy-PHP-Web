<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeVehicleFranchiseForiginNullableDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable()->change();

            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')
                ->on('vehicles')->nullOnDelete();

            $table->unsignedBigInteger('franchise_id')->nullable()->change();

            $table->dropForeign(['franchise_id']);
            $table->foreign('franchise_id')->references('id')
                ->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->change();

            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')
                ->on('vehicles')->nullOnDelete();

            $table->unsignedBigInteger('franchise_id')->change();

            $table->dropForeign(['franchise_id']);
            $table->foreign('franchise_id')->references('id')
                ->on('users')->nullOnDelete();
        });
    }
}
