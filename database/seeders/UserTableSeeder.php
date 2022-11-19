<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $users =  [
            ['first_name'=>'admin','last_name'=>'admin','email'=>'admin@gmail.com',
                'password'=>bcrypt('11223344'), 'is_verified'=>1]
        ];

        foreach($users as $user)
        {
            $createUser = User::create($user);
            $createUser->attachRole('administrator');

        }
    }
}
