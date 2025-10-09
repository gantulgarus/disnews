<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TnewsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DisCoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PowerPlantController;
use App\Http\Controllers\OrderJournalController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PermissionLevelController;
use App\Http\Controllers\StationThermoDataController;
use App\Http\Controllers\DailyBalanceJournalController;
use App\Http\Controllers\PowerDistributionWorkController;
use App\Http\Controllers\PowerPlantDailyReportController;



Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('power-plant-daily-reports', PowerPlantDailyReportController::class);
    Route::resource('power-plants', PowerPlantController::class);
    Route::resource('permission_levels', PermissionLevelController::class);

    Route::get('daily-balance-journals/report', [DailyBalanceJournalController::class, 'dailyMatrixReport'])->name('daily-balance-journals.report');
    Route::resource('daily-balance-journals', DailyBalanceJournalController::class);
    Route::resource('divisions', DivisionController::class);
    Route::resource('dis_coal', DisCoalController::class);

    Route::resource('users', UserController::class);
    Route::resource('tnews', TnewsController::class);

    Route::resource('order-journals', OrderJournalController::class);
    Route::resource('organizations', OrganizationController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily', [ReportController::class, 'dailyReport'])->name('reports.dailyReport');
    Route::get('/reports/power-plant', [ReportController::class, 'powerPlantReport'])->name('reports.powerPlantReport');
    Route::resource('power-distribution-works', PowerDistributionWorkController::class);
    Route::get('station_thermo/news', [StationThermoDataController::class, 'news'])->name('station_thermo.news');
    Route::resource('station_thermo', StationThermoDataController::class);
});

require __DIR__ . '/auth.php';
