<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverWalletModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('drivers_wallet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('franchise_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('payment_method')->comment('cash,wallet,bonus,fine,extras');
            $table->string('type')->comment('debit,credit');
            $table->decimal('ride_total_amount',10,2)->default(0);
            $table->decimal('franchise_amount',10,2)->default(0);
            $table->decimal('driver_total_amount',10,2)->default(0);
            $table->decimal('tax_amount',10,2)->default(0);
            $table->decimal('amount',10,2)->default(0);
            $table->string('description');
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
        Schema::dropIfExists('drivers_wallet');
    }
}
