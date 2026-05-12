<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Home route
Route::get('/', function () {
    return redirect()->route('products.index');
});

// Product routes
Route::resource('products', ProductController::class);

// Review routes
Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

// Simple authentication routes
Route::get('/test-login', function () {
    // Check if any user exists
    $user = \App\Models\User::first();
    
    if (!$user) {
        // Create a test user if none exists
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }
    
    Auth::login($user);
    return redirect()->route('products.index')->with('success', 'Logged in as: ' . $user->name);
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/')->with('success', 'Logged out successfully');
});