<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Administrator',
            'email' => 'admin@comp1641.xyz',
            'gender' => 'Male',
            'phone' => '1234567890',
            'password' => Hash::make('123456789')
        ]);

        $user->assignRole('Super Administrator');
    }
}
