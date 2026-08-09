<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServerPublicDashboardController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [ServerPublicDashboardController::class, 'index'])->name('welcome');
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
    Route::get('/storage/{id}/download', [StorageController::class, 'download'])->name('storage.download');
    Route::patch('/storage/{id}/move', [StorageController::class, 'move'])->name('storage.move');

    Route::get('/storage/folders/tree', [FolderController::class, 'tree'])->name('storage.folders.tree');
    Route::post('/storage/folders', [FolderController::class, 'store'])->name('storage.folders.store');
    Route::post('/storage/folders/resolve-path', [FolderController::class, 'resolvePath'])->name('storage.folders.resolve');
    Route::patch('/storage/folders/{id}', [FolderController::class, 'rename'])->name('storage.folders.rename');
    Route::patch('/storage/folders/{id}/move', [FolderController::class, 'move'])->name('storage.folders.move');

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/storage/{id}', [StorageController::class, 'destroy'])->name('storage.destroy');
        Route::delete('/storage/folders/{id}', [FolderController::class, 'destroy'])->name('storage.folders.destroy');

    });
});

require __DIR__.'/auth.php';
