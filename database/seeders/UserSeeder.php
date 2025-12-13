<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'reyva',
                'store_name' => 'Reyva Store',
                'email' => 'reyva@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('wawuwiri'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'andhika',
                'store_name' => 'Andhika Store',
                'email' => 'andhika@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('kahandula'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
        ]);
    }
}