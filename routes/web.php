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
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\PowerPlantController;
use App\Http\Controllers\OrderJournalController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PermissionLevelController;
use App\Http\Controllers\TelephoneMessageController;
use App\Http\Controllers\StationThermoDataController;
use App\Http\Controllers\ThermoDailyRegimeController;
use App\Http\Controllers\DailyBalanceJournalController;
use App\Http\Controllers\ElectricDailyRegimeController;
use App\Http\Controllers\DailyEquipmentReportController;
use App\Http\Controllers\PowerDistributionWorkController;
use App\Http\Controllers\PowerPlantDailyReportController;



Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // // Route::post('/users', [UserController::class, 'store'])->name('users.store');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('power-plant-daily-reports/status', [PowerPlantDailyReportController::class, 'status'])->name('power-plant-daily-reports.status');
    Route::resource('power-plant-daily-reports', PowerPlantDailyReportController::class);
    Route::resource('power-plants', PowerPlantController::class);
    // Нэмэлт маршрутууд
    // web.php
    Route::get('power-plants/{powerPlant}/add-equipment', [PowerPlantController::class, 'addEquipment'])->name('power-plants.add-equipment');
    Route::post('power-plants/store-equipment', [PowerPlantController::class, 'storeEquipment'])->name('power-plants.store-equipment');

    Route::resource('permission_levels', PermissionLevelController::class);

    Route::get('daily-balance-journals/report', [DailyBalanceJournalController::class, 'dailyMatrixReport'])->name('daily-balance-journals.report');
    Route::resource('daily-balance-journals', DailyBalanceJournalController::class);
    Route::resource('divisions', DivisionController::class);
    Route::resource('dis_coal', DisCoalController::class);

    Route::get('/users/profile', [UserController::class, 'profile'])->name('users.profile');
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

    Route::get('/electric-daily-regimes/report', [ElectricDailyRegimeController::class, 'report'])->name('electric_daily_regimes.report');
    Route::resource('electric_daily_regimes', ElectricDailyRegimeController::class);

    // Daily Equipment Report Routes
    // Route::get('daily-equipment-report/create', [DailyEquipmentReportController::class, 'create'])->name('daily-equipment-report.create');
    Route::get('/daily-equipment-report/create/{powerPlant}', [DailyEquipmentReportController::class, 'create'])->name('daily-equipment-report.create');
    Route::post('daily-equipment-report/store', [DailyEquipmentReportController::class, 'store'])->name('daily-equipment-report.store');
    Route::get('get-equipments/{powerPlantId}', [DailyEquipmentReportController::class, 'getEquipmentsByStation']); // AJAX route
    Route::get('/daily-equipment-report', [DailyEquipmentReportController::class, 'index'])
        ->name('daily-equipment-report.index');
    // Edit / Update report
    Route::get('/daily-equipment-report/{powerPlant}/edit', [DailyEquipmentReportController::class, 'edit'])->name('daily-equipment-report.edit');
    Route::put('/daily-equipment-report/{powerPlant}', [DailyEquipmentReportController::class, 'update'])->name('daily-equipment-report.update');
    Route::get('/daily-equipment-report/details/{powerPlant}', [DailyEquipmentReportController::class, 'details'])
        ->name('daily-equipment-report.details');
    Route::delete('/daily-equipment-report/{powerPlant}', [DailyEquipmentReportController::class, 'destroy'])
        ->name('daily-equipment-report.destroy');

    Route::resource('equipments', EquipmentController::class);
    Route::get('thermo-daily-regimes/report', [ThermoDailyRegimeController::class, 'report'])
        ->name('thermo-daily-regimes.report');
    Route::resource('thermo-daily-regimes', ThermoDailyRegimeController::class);

    Route::resource('telephone_messages', TelephoneMessageController::class)->middleware('auth');
});

require __DIR__ . '/auth.php';
