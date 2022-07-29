<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->renameColumn('cnic_front_side','drivers_license');
            $table->renameColumn('cnic_back_side','vehicle_insurance');
            $table->renameColumn('license_front_side','vehicle_inspection');
            $table->dropColumn('license_back_side');
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
            $table->renameColumn('drivers_license','cnic_front_side');
            $table->renameColumn('vehicle_insurance','cnic_back_side');
            $table->renameColumn('vehicle_inspection','license_front_side');
            $table->string('license_back_side')->after('license_front_side')->nullable();
        });
    }
}
