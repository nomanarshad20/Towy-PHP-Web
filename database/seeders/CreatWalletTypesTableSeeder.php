<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CreatWalletTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        DB::table('wallet_types')->insert([
            ['name' => 'Passenger-Wallet', 'decimals' => 2],
            ['name' => 'Driver-Wallet', 'decimals' => 2]
//            ['name' => 'Admin-Franchise-Wallet', 'decimals' => 2],
//            ['name' => 'ToTo-Admin-Wallet', 'decimals' => 2]
        ]);

    }
}
