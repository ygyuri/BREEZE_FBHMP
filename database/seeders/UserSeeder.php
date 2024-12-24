<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Seed Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Seed Foodbanks
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Foodbank User $i",
                'email' => "foodbank$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'foodbank',
            ]);
        }

        // Seed Donors
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Donor User $i",
                'email' => "donor$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'donor',
            ]);
        }

        // Seed Recipients
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Recipient User $i",
                'email' => "recipient$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'recipient',
            ]);
        }
    }
}