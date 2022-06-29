<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelColumnBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('cancel_id')->nullable()->after('actual_fare')
                ->constrained('booking_cancel_reasons')->nullOnDelete();

            $table->string('other_cancel_reason')->after('cancel_id')->nullable();

            $table->string('fine_amount')->after('other_cancel_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['cancel_id']);
            $table->dropColumn('cancel_id');
            $table->dropColumn('other_cancel_reason');
        });
    }
}
