<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDriverFranchiseCompanyTaxSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('settings', function (Blueprint $table) {
            $table->string('driver_share')->after('driver_cancel_fine_amount')->comment('%')->default(0);
            $table->string('franchise_share')->after('driver_share')->comment('%')->default(0);
            $table->string('tax_share')->after('franchise_share')->comment('%')->default(0);
            $table->string('company_share')->after('tax_share')->comment('%')->default(0);
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
            $table->dropColumn('driver_share');
            $table->dropColumn('franchise_share');
            $table->dropColumn('tax_share');
            $table->dropColumn('company_share');
        });
    }

}
