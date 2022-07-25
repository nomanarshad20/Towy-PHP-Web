<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherRelatedColumnBookingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->boolean('is_voucher')->after('mobile_initial_distance')->comment('0=not,1=yes')->default(0)->nullable();
            $table->text('voucher_detail')->after('is_voucher')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('is_voucher');
            $table->dropColumn('voucher_detail');
        });
    }
}
