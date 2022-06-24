<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->after('name')->nullable();
            $table->string('mobile_no')->after('referral_code')->unique()
                ->nullable();
            $table->integer('user_type')->after('remember_token')
                ->comment('1=Passenger, 2=Driver, 3=Franchise')->nullable();
            $table->boolean('is_verified')->after('user_type')
                ->comment('0=unverified,1=verified')->default(0);
            $table->integer('otp')->after('is_verified')->nullable();
            $table->string('fcm_token')->after('otp')->nullable();
            $table->string('image')->nullable()->after('fcm_token');

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
            $table->dropColumn('mobile_no');
            $table->dropColumn('user_type');
            $table->dropColumn('otp');
            $table->dropColumn('referral_code');
            $table->dropColumn('fcm_token');
            $table->dropColumn('is_verified');
            $table->dropColumn('image');
        });
    }
}
