<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServerPublicDashboardController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // file
    Route::get('/storage', [StorageController::class, 'index'])->name('storage.index');
    Route::post('/storage/upload', [StorageController::class, 'storeChunk'])->name('storage.upload');

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/storage/{id}', [StorageController::class, 'destroy'])->name('storage.destroy');
    });
});

require __DIR__.'/auth.php';
