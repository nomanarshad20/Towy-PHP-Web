<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxamountColumnTableFranchiseWallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('franchise_wallets', function (Blueprint $table) {
            $table->decimal('tax_amount',10,2)->after('franchise_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('franchise_wallets', function (Blueprint $table) {
            $table->dropColumn('tax_amount');
        });
    }
}
