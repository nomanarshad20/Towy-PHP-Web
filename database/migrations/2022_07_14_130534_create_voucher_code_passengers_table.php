<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherCodePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_code_passengers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('voucher_code_id')->nullable()
                ->constrained('voucher_codes')->nullOnDelete();

            $table->foreignId('passenger_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('voucher_code');

            $table->string('discount_type');
            $table->string('discount_amount');
            $table->string('expiry_date');

            $table->boolean('is_applied')->comment('0=not_apply,1=applied')
                ->default(0)
                ->nullable();
            $table->boolean('is_used')->comment('0=unused,1=used')
                ->default(0)
                ->nullable();

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
        Schema::dropIfExists('voucher_code_passengers');
    }
}
