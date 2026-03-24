<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemoteController;

Route::get('/', function () {
    return redirect('/remote');
});

Route::get('/remote', [RemoteController::class, 'index'])->name('remote.index');
Route::post('/remote', [RemoteController::class, 'execute'])->name('remote.execute');
Route::post('/remote/clear', [RemoteController::class, 'clearOutput'])->name('remote.clear');
