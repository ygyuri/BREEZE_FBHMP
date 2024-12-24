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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Index for fast searches
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 15)->nullable()->unique(); // Optional phone number
            $table->enum('role', ['admin', 'foodbank', 'donor', 'recipient'])->default('recipient')->index(); // Indexed for query performance
            $table->string('profile_picture')->nullable(); // To store URLs for user profile images
            // $table->string('google_id')->nullable()->unique(); // To support Google OAuth
            $table->rememberToken();
            $table->softDeletes(); // For logical deletes
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Use email as primary key
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Unique session ID
            $table->foreignId('user_id')->nullable()->index()->constrained()->onDelete('cascade'); // Cascade on user deletion
            $table->string('ip_address', 45)->nullable(); // IPv4/IPv6 compatibility
            $table->text('user_agent')->nullable(); // Store browser/device details
            $table->longText('payload'); // Session data payload
            $table->integer('last_activity')->index(); // Timestamp for session activity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};