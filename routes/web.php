<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WasteScanController;
use App\Http\Controllers\FloodReportController;
use App\Http\Controllers\JunkshopController;

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

    Route::get('/report', [FloodReportController::class, 'index'])->name('reports.index'); // "Flood Report Status" list
    Route::get('/report/create', [FloodReportController::class, 'create'])->name('reports.create');
    Route::post('/report', [FloodReportController::class, 'store'])->name('reports.store');
    Route::post('/report/{floodReport}/verify-cleanup', [FloodReportController::class, 'verifyCleanup'])->name('reports.verify-cleanup');

    Route::get('/market', [JunkshopController::class, 'index'])->name('market.index');
    Route::get('/market/history', [JunkshopController::class, 'history'])->name('market.history');
    Route::get('/market/{junkshop}', [JunkshopController::class, 'show'])->name('market.show');
    Route::post('/market/{junkshop}/schedule', [JunkshopController::class, 'schedule'])->name('market.schedule');
    

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/heatmap', [FloodReportController::class, 'heatmap'])->name('heatmap');

require __DIR__.'/auth.php';
