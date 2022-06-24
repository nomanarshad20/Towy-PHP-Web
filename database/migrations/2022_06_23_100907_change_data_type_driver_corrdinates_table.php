<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDataTypeDriverCorrdinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers_coordinates', function (Blueprint $table) {
            $table->float('latitude')->change();
            $table->float('longitude')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers_coordinates', function (Blueprint $table) {
            $table->decimal('latitude',11,2)->change();
            $table->decimal('longitude',11,2)->change();
        });
    }
}
