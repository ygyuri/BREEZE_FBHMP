<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foodbank_id')
                ->constrained('users')  // Reference the `users` table for foodbanks
                ->onDelete('cascade');  // Automatically delete subscriptions if the foodbank is deleted
            $table->enum('status', ['trial', 'active', 'expired'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->decimal('monthly_fee', 8, 2)->default(0);  // Decimal format for the monthly fee
            $table->timestamps();

            // Index for performance (queries based on `foodbank_id` or `status`)
            $table->index('foodbank_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};