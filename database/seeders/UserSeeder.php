<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 100 ; $i++) { 
            # code...
            DB::table('users')->insert([
                'username' => ''.rand(10000,99999),
                'email' => rand(10000,99999).'@gmail.com',
                'password' => Hash::make('1234')
            ]);
        }
        
    }
}
