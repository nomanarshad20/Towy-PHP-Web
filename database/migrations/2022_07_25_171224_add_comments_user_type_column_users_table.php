<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentsUserTypeColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('user_type')->after('remember_token')
                ->comment('1=Passenger, 2=Driver, 3=Franchise,4= Service Provider')->nullable()->change();
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
            $table->integer('user_type')->after('remember_token')
                ->comment('1=Passenger, 2=Driver, 3=Franchise')->nullable()->change();
        });
    }
}
