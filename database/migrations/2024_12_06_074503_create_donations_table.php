<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->enum('type', ['food', 'clothing', 'money']); // Limit types of donations to valid options
            $table->integer('quantity')->unsigned(); // Positive quantity, ensures valid data
            $table->foreignId('donor_id')->constrained('users')->onDelete('cascade'); // Foreign key for donor, cascade on delete
            $table->foreignId('foodbank_id')->constrained('users')->onDelete('cascade'); // Foreign key for foodbank, cascade on delete
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key for recipient, set null on delete
            $table->timestamps(); // Timestamps for created_at and updated_at

            // Indexes for performance on frequently queried columns
            $table->index(['donor_id', 'foodbank_id', 'recipient_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('donations');
    }
}