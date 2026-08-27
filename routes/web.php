<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\FeedingLogController;
use App\Http\Controllers\GrowthRecordController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\MortalityRecordController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// 1. Guest routes (handled by Breeze)
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// 2. Shared authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isManager()) {
            return redirect()->route('manager.dashboard');
        }
        return redirect()->route('worker.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 3. Manager-only group
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'managerDashboard'])->name('dashboard');

    Route::resource('stations', StationController::class);
    Route::resource('batches', BatchController::class);
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate'); // Route to activate pending workers

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::post('reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::resource('feeding-logs', FeedingLogController::class)->only(['index', 'show']);
    Route::resource('growth-records', GrowthRecordController::class)->only(['index', 'show']);
    Route::resource('health-records', HealthRecordController::class)->only(['index', 'show']);
    Route::resource('mortality-records', MortalityRecordController::class)->only(['index', 'show']);
    Route::resource('sales', SaleController::class)->only(['index', 'show']);
});

// 4. Worker-only group
Route::middleware(['auth', 'role:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'workerDashboard'])->name('dashboard');

    Route::resource('stations', StationController::class);
    Route::resource('batches', BatchController::class);

    Route::resource('feeding-logs', FeedingLogController::class)->except(['destroy']);
    Route::resource('growth-records', GrowthRecordController::class)->except(['destroy']);
    Route::resource('health-records', HealthRecordController::class)->except(['destroy']);
    Route::resource('mortality-records', MortalityRecordController::class)->except(['destroy']);
    Route::resource('sales', SaleController::class)->except(['destroy']);
});