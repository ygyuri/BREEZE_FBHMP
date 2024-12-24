<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\User; // Assuming User model is used for donor, foodbank, and recipient
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get random users for donor, foodbank, and recipient
        $donors = User::where('role', 'donor')->pluck('id');
        $foodbanks = User::where('role', 'foodbank')->pluck('id');
        $recipients = User::where('role', 'recipient')->pluck('id');

        foreach ($donors as $donor_id) {
            foreach ($foodbanks as $foodbank_id) {
                Donation::create([
                    'donor_id' => $donor_id,
                    'foodbank_id' => $foodbank_id,
                    'recipient_id' => $faker->randomElement($recipients), // Random recipient from the list
                    'type' => $faker->randomElement(['food', 'clothing', 'money']),
                    'quantity' => $faker->numberBetween(1, 100), // Random quantity
                ]);
            }
        }
    }
}