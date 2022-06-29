<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsBookingDetailsWallatCashExtraPay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->decimal('passenger_total_cash_paid',11,2)->after('peak_factor_rate')->default(0);
            $table->decimal('passenger_extra_cash_paid',11,2)->after('passenger_total_cash_paid')->default(0);
            $table->decimal('passenger_wallet_paid',11,2)->after('passenger_extra_cash_paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('passenger_total_cash_paid');
            $table->dropColumn('passenger_extra_cash_paid');
            $table->dropColumn('passenger_wallet_paid');

        });
    }
}
