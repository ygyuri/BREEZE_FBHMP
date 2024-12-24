<?php

namespace Database\Seeders;

use App\Models\Feedback;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Assuming foodbanks and recipients are users
        $foodbanks = User::where('role', 'foodbank')->pluck('id'); // Only foodbanks have feedback
        $recipients = User::where('role', 'recipient')->pluck('id'); // Only recipients receive feedback

        foreach ($foodbanks as $foodbank_id) {
            foreach ($recipients as $recipient_id) {
                Feedback::create([
                    'foodbank_id' => $foodbank_id,
                    'recipient_id' => $recipient_id,
                    'thank_you_note' => $faker->paragraph,
                    'rating' => $faker->numberBetween(1, 5), // Random rating between 1 and 5
                ]);
            }
        }
    }
}