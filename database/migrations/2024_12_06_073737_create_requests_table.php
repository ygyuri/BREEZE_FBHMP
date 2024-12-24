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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foodbank_id')->constrained('users')->onDelete('cascade');  // Ensure a foreign key constraint for the 'foodbank_id'
            $table->enum('type', ['food', 'supplies', 'monetary']); // Enum to specify request types
            $table->integer('quantity')->unsigned();  // Ensuring quantity is a positive number
            $table->timestamps();
            $table->softDeletes();  // Soft delete field to mark the record as deleted instead of removing it completely
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};