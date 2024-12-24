<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;  // Assuming User model is used for foodbanks
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get a list of foodbanks (users who are foodbanks)
        $foodbanks = User::where('role', 'foodbank')->pluck('id');

        foreach ($foodbanks as $foodbank_id) {
            Subscription::create([
                'foodbank_id' => $foodbank_id,
                'status' => $faker->randomElement(['trial', 'active', 'expired']),
                'trial_ends_at' => $faker->dateTimeBetween('now', '+1 month'),
                'subscription_ends_at' => $faker->dateTimeBetween('now', '+1 year'),
                'monthly_fee' => $faker->randomFloat(2, 10, 100), // Random fee between 10 and 100
            ]);
        }
    }
}