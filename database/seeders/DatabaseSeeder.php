<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create a default user
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

            // Prevent duplicate seeding of the same user
            $user = User::firstOrCreate(
                ['email' => 'test@example.com'],  // Ensure the email is unique
                [
                    'name' => 'Test User',
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),  // Set a default password (bcrypt or any other method)
                ]
            );

        $this->call(UserSeeder::class);
        // Call the RequestSeeder to seed requests
        $this->call(RequestSeeder::class);

         // Call the FeedbackSeeder to seed feedback data
         $this->call(FeedbackSeeder::class);

         // Call the SubscriptionSeeder to seed subscription data
        $this->call(SubscriptionSeeder::class);

        // Call the DonationSeeder to seed donation data
        $this->call(DonationSeeder::class);
    }
}