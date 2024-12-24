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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')
                ->constrained('users') // Assumes 'users' table holds the recipient's data
                ->onDelete('cascade');
            $table->foreignId('foodbank_id')
                ->constrained('users') // Assumes 'users' table holds the foodbank's data
                ->onDelete('cascade');
            $table->text('thank_you_note');
            $table->integer('rating')->default(5)->unsigned(); // Rating between 1 and 5, defaulting to 5
            $table->timestamps();
            $table->softDeletes(); // Soft delete in case feedback is removed but retained for history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};