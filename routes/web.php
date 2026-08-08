<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WasteScanController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'resident'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/scan', [WasteScanController::class, 'create'])->name('scan.create');
    Route::post('/scan', [WasteScanController::class, 'store'])->name('scan.store');
    Route::get('/scan/{wasteScan}', [WasteScanController::class, 'show'])->name('scan.show');
    Route::post('/scan/{wasteScan}/confirm-category', [WasteScanController::class, 'confirmCategory'])->name('scan.confirm-category');
    Route::post('/scan/{wasteScan}/confirm-disposal', [WasteScanController::class, 'confirmDisposal'])->name('scan.confirm-disposal');

    Route::get('/scan/{wasteScan}/guide', [WasteScanController::class, 'guide'])->name('scan.guide');

});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
