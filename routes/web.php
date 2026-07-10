<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RemoteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/remote', [RemoteController::class, 'index'])->name('remote.index');
Route::post('/remote', [RemoteController::class, 'execute'])->name('remote.execute');
Route::post('/remote/clear', [RemoteController::class, 'clearOutput'])->name('remote.clear');
Route::post('/remote/switch', [RemoteController::class, 'switchServer'])->name('remote.switch');
Route::get('/remote/test/{id}', [RemoteController::class, 'testConnection'])->name('remote.test');
Route::get('/remote/history', [RemoteController::class, 'getHistory'])->name('remote.history');
Route::post('/remote/rerun/{id}', [RemoteController::class, 'rerun'])->name('remote.rerun');

Route::resource('products', ProductController::class);
Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::get('/test-login', function () {
    $user = \App\Models\User::first();
    if (!$user) {
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