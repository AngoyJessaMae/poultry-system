<?php

use App\Http\Controllers\ActivityLogController;
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
use App\Http\Controllers\DailyProductionReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuantityAdjustmentController;
use App\Http\Controllers\ManagementRequestController;
use App\Http\Controllers\StationFeedingConfigController;
use App\Http\Controllers\FinancialController;
use Illuminate\Support\Facades\Route;

// 1. Guest routes (handled by Breeze)
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// 2. Shared authenticated routes
Route::middleware(['auth', 'verified', 'policy.accepted'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isManager()) {
            return redirect()->route('manager.users.index');
        }
        return redirect()->route('worker.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notifications/dismiss', [NotificationController::class, 'dismiss'])->name('notifications.dismiss');

    Route::middleware('role:manager')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('manager.reports.index');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('manager.reports.export.pdf');
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('manager.reports.export.excel');
        Route::get('/reports/daily', [DailyProductionReportController::class, 'index'])->name('reports.daily.index');
        Route::get('/reports/daily/{batch}', [DailyProductionReportController::class, 'show'])->name('reports.daily.show');
        Route::put('/reports/daily/{batch}/header', [DailyProductionReportController::class, 'updateHeader'])->name('reports.daily.header.update');
        Route::put('/reports/daily/{batch}/row', [DailyProductionReportController::class, 'upsertRow'])->name('reports.daily.row.update');
        Route::put('/reports/daily/standards', [DailyProductionReportController::class, 'upsertStandard'])->name('reports.daily.standard.update');
        Route::get('/reports/daily/{batch}/export/pdf', [DailyProductionReportController::class, 'exportPdf'])->name('reports.daily.export.pdf');
        Route::get('/reports/daily/{batch}/export/excel', [DailyProductionReportController::class, 'exportExcel'])->name('reports.daily.export.excel');
    });
});

// 3. Manager-only group
Route::middleware(['auth', 'verified', 'policy.accepted', 'house-rules.acknowledged', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'managerDashboard'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate'); // Route to activate pending workers

    Route::get('/quantity-adjustments/{batch}/create', [QuantityAdjustmentController::class, 'create'])->name('quantity-adjustments.create');
    Route::post('/quantity-adjustments/{batch}', [QuantityAdjustmentController::class, 'store'])->name('quantity-adjustments.store');
    Route::get('/management-requests', [ManagementRequestController::class, 'index'])->name('management-requests.index');
    Route::post('/management-requests/{managementRequest}/approve', [ManagementRequestController::class, 'approve'])->name('management-requests.approve');
    Route::post('/management-requests/{managementRequest}/reject', [ManagementRequestController::class, 'reject'])->name('management-requests.reject');
    Route::get('/stations/{station}/feeding-config', [StationFeedingConfigController::class, 'edit'])->name('stations.feeding-config.edit');
    Route::put('/stations/{station}/feeding-config', [StationFeedingConfigController::class, 'update'])->name('stations.feeding-config.update');
    Route::resource('sales', SaleController::class);
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('/financial/feed-prices', [FinancialController::class, 'storePrice'])->name('financial.feed-prices.store');
    Route::post('/financial/expenses', [FinancialController::class, 'storeExpense'])->name('financial.expenses.store');
    Route::post('/financial/incomes', [FinancialController::class, 'storeIncome'])->name('financial.incomes.store');
    Route::post('/financial/adjustments', [FinancialController::class, 'storeAdjustment'])->name('financial.adjustments.store');
    Route::post('/financial/expenses/{expense}/cancel', [FinancialController::class, 'cancelExpense'])->name('financial.expenses.cancel');
    Route::post('/financial/incomes/{income}/cancel', [FinancialController::class, 'cancelIncome'])->name('financial.incomes.cancel');

    // Activity Logs routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/history', [ActivityLogController::class, 'history'])->name('activity-logs.history');
    Route::get('/activity-logs/{user}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
});

// 4. Worker-only group
Route::middleware(['auth', 'verified', 'policy.accepted', 'house-rules.acknowledged', 'role:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'workerDashboard'])->name('dashboard');
    Route::get('/management-requests', [ManagementRequestController::class, 'index'])->name('management-requests.index');
    Route::get('/feeding-logs/station/{station}', [FeedingLogController::class, 'stationIndex'])->name('feeding-logs.station');

    Route::resource('stations', StationController::class);
    Route::resource('batches', BatchController::class);

    Route::get('feeding-logs/select-batch', [FeedingLogController::class, 'selectBatch'])->name('feeding-logs.select-batch');
    Route::post('feeding-logs/create-for-batch', [FeedingLogController::class, 'createForBatch'])->name('feeding-logs.create-for-batch');
    Route::resource('feeding-logs', FeedingLogController::class)->only(['index', 'show']);
    Route::get('batches/{batch}/feeding-logs/create', [FeedingLogController::class, 'create'])->name('batches.feeding-logs.create');
    Route::post('batches/{batch}/feeding-logs', [FeedingLogController::class, 'store'])->name('batches.feeding-logs.store');
    Route::resource('growth-records', GrowthRecordController::class);
    Route::resource('health-records', HealthRecordController::class);
    Route::resource('mortality-records', MortalityRecordController::class);
});
