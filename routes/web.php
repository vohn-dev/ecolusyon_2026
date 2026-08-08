<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WasteScanController;
use App\Http\Controllers\FloodReportController;
use App\Http\Controllers\JunkshopController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\ShopProfileController;
use App\Http\Controllers\MaterialPriceController;
use App\Http\Controllers\PickupRequestController;
use App\Http\Controllers\OperatorTransactionController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\OperatorNotificationController;


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
    Route::get('/scan/{wasteScan}/retake', [WasteScanController::class, 'retakeForm'])->name('scan.retake.form');
    Route::match(['get', 'post'], '/scan/{wasteScan}/retake', [WasteScanController::class, 'retake'])->name('scan.retake');

    Route::get('/report', [FloodReportController::class, 'index'])->name('reports.index'); // "Flood Report Status" list
    Route::get('/report/create', [FloodReportController::class, 'create'])->name('reports.create');
    Route::post('/report', [FloodReportController::class, 'store'])->name('reports.store');
    Route::post('/report/auto-detect', [FloodReportController::class, 'autoDetect'])->name('reports.auto-detect');
    Route::post('/report/{floodReport}/verify-cleanup', [FloodReportController::class, 'verifyCleanup'])->name('reports.verify-cleanup');

    Route::get('/market', [JunkshopController::class, 'index'])->name('market.index');
    Route::get('/market/history', [JunkshopController::class, 'history'])->name('market.history');
    Route::get('/market/{junkshop}', [JunkshopController::class, 'show'])->name('market.show');
    Route::post('/market/{junkshop}/schedule', [JunkshopController::class, 'schedule'])->name('market.schedule');
    
    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/{key}/redeem', [RewardsController::class, 'redeem'])->name('rewards.redeem');
    Route::get('/leaderboard', [RewardsController::class, 'leaderboard'])->name('rewards.leaderboard');
});

Route::middleware(['auth', 'junkshop'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/onboarding', [ShopProfileController::class, 'create'])->name('onboarding');
    Route::post('/onboarding', [ShopProfileController::class, 'store'])->name('onboarding.store');

    Route::middleware('shop.registered')->group(function () {
        Route::get('/', [OperatorDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ShopProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ShopProfileController::class, 'update'])->name('profile.update');

        Route::get('/prices', [MaterialPriceController::class, 'edit'])->name('prices.edit');
        Route::put('/prices', [MaterialPriceController::class, 'update'])->name('prices.update');

        Route::get('/requests', [PickupRequestController::class, 'index'])->name('requests.index');
        Route::post('/requests/{pickupRequest}/accept', [PickupRequestController::class, 'accept'])->name('requests.accept');
        Route::post('/requests/{pickupRequest}/decline', [PickupRequestController::class, 'decline'])->name('requests.decline');

        Route::get('/transactions', [OperatorTransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transactions', [OperatorTransactionController::class, 'store'])->name('transactions.store');

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');

        Route::get('/notifications', [OperatorNotificationController::class, 'index'])->name('notifications.index');
    });
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
});

require __DIR__.'/auth.php';
