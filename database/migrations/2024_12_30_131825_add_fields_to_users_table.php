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
        Schema::table('users', function (Blueprint $table) {
            // Add fields specific to foodbank, donor, and recipient roles
            $table->string('location')->nullable(); // For foodbank and donor location
            $table->string('address')->nullable(); // To store addresses for recipients
            $table->string('organization_name')->nullable(); // For recipients who are organizations
            $table->enum('recipient_type', ['individual', 'organization'])->nullable(); // To differentiate between individual and organization recipients
            $table->string('donor_type')->nullable(); // To categorize donor types (e.g., corporate, individual)
            $table->text('notes')->nullable(); // For any extra notes, particularly useful for foodbanks and recipients

            // Ensure proper indexing where necessary for better query performance
            $table->index(['location', 'organization_name', 'recipient_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the newly added columns if rolling back
            $table->dropColumn([
                'location',
                'address',
                'organization_name',
                'recipient_type',
                'donor_type',
                'notes',
            ]);
        });
    }
};