<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;

// Donation routes
Route::middleware(['auth:sanctum'])->prefix('donations')->name('donations.')->group(function () {
    Route::get('/', [DonationController::class, 'index'])->name('index'); // List donations
    Route::post('/', [DonationController::class, 'store'])->name('store'); // Create a donation
    Route::get('/{donation}', [DonationController::class, 'show'])->name('show'); // View a specific donation
    Route::put('/{donation}', [DonationController::class, 'update'])->name('update'); // Update a donation
    Route::delete('/{donation}', [DonationController::class, 'destroy'])->name('destroy'); // Delete a donation
    Route::post('donations/{donation}/assign/{recipient}', [DonationController::class, 'assignRecipient']);   // Route to assign recipient to donation
    Route::post('donations/{donation}/complete', [DonationController::class, 'markAsCompleted']);  // Route to mark donation as completed


});

// Feedback routes
Route::middleware(['auth:sanctum'])->prefix('feedbacks')->name('feedbacks.')->group(function () {
    Route::get('/', [FeedbackController::class, 'index'])->name('index'); // List feedback
    Route::post('/', [FeedbackController::class, 'store'])->name('store'); // Create feedback
    Route::get('/{feedback}', [FeedbackController::class, 'show'])->name('show'); // View specific feedback
    Route::put('/{feedback}', [FeedbackController::class, 'update'])->name('update'); // Update feedback
    Route::delete('/{feedback}', [FeedbackController::class, 'destroy'])->name('destroy'); // Delete feedback
});

// Request routes
Route::middleware(['auth:sanctum'])->prefix('requests')->name('requests.')->group(function () {
    Route::get('/', [RequestController::class, 'index'])->name('index');
    Route::post('/', [RequestController::class, 'store'])->name('store');
    Route::get('/{id}', [RequestController::class, 'show'])->name('show');
    Route::put('/{id}', [RequestController::class, 'update'])->name('update');
    Route::delete('/{id}', [RequestController::class, 'destroy'])->name('destroy');
});

// Subscription routes
Route::middleware(['auth:sanctum'])->prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::post('/', [SubscriptionController::class, 'store'])->name('store');
    Route::get('/{id}', [SubscriptionController::class, 'show'])->name('show');
    Route::put('/{id}', [SubscriptionController::class, 'update'])->name('update');
    Route::delete('/{id}', [SubscriptionController::class, 'destroy'])->name('destroy');
});

// User routes
Route::middleware(['auth:sanctum'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/register', [UserController::class, 'store'])->name('register');
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::put('/{id}/approve-reject/{status}', [UserController::class, 'approveRejectUser'])->name('approveRejectUser');
    Route::get('/filtered', [UserController::class, 'getFilteredUsersByStatus'])->name('filtered');
});