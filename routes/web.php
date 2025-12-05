<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TnewsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DisCoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeatherController;
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
use App\Http\Controllers\DailyPowerEquipmentController;
use App\Http\Controllers\ElectricDailyRegimeController;
use App\Http\Controllers\DailyEquipmentReportController;
use App\Http\Controllers\DailyPowerHourReportController;
use App\Http\Controllers\PowerDistributionWorkController;
use App\Http\Controllers\PowerPlantDailyReportController;
use App\Http\Controllers\WesternRegionCapacityController;



Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/dashboard/realtime', [DashboardController::class, 'realtimeData'])->name('dashboard.realtime');

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
    Route::get('daily-balance-journals/plant/{plant}', [DailyBalanceJournalController::class, 'showPlant'])
        ->name('daily-balance-journals.showPlant');
    Route::resource('daily-balance-journals', DailyBalanceJournalController::class);

    Route::resource('divisions', DivisionController::class);
    Route::resource('dis_coal', DisCoalController::class);
    Route::resource('daily_power_equipments', DailyPowerEquipmentController::class);

    Route::get('/reports/daily-power-equipment', [DailyPowerEquipmentController::class, 'index'])
        ->name('reports.dailyPowerEquipment');

    Route::get('/reports/daily-power-equipment', [ReportController::class, 'dailyPowerEquipment'])->name('reports.dailyPowerEquipment');

    Route::get('/users/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::resource('users', UserController::class);
    Route::resource('tnews', TnewsController::class);

    Route::resource('order-journals', OrderJournalController::class);
    Route::post('order-journals/{orderJournal}/forward', [OrderJournalController::class, 'forward'])->name('order-journals.forward');
    // Route::post('order-journal-approvals/{approval}/approve', [OrderJournalController::class, 'approve'])->name('order-journal-approvals.approve');
    Route::post('order-journals/approve-opinion/{approval}', [OrderJournalController::class, 'approveOpinion'])
        ->name('order-journals.approveOpinion');
    Route::post('order-journals/{orderJournal}/approve', [OrderJournalController::class, 'approve'])
        ->name('order-journals.approve');


    Route::resource('organizations', OrganizationController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily', [ReportController::class, 'dailyReport'])->name('reports.dailyReport');
    Route::get('/reports/local-daily', [ReportController::class, 'localDailyReport'])->name('reports.localDailyReport');
    Route::get('/reports/power-plant', [ReportController::class, 'powerPlantReport'])->name('reports.powerPlantReport');
    Route::get('/reports/power-plant-renewable', [ReportController::class, 'powerPlantRenewableReport'])->name('reports.powerPlantRenewableReport');
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



    Route::get('/daily_power_hour_reports/report', [DailyPowerHourReportController::class, 'userPowerReport'])
        ->name('daily_power_hour_reports.report');

    Route::resource('daily_power_hour_reports', DailyPowerHourReportController::class)
        ->except(['edit', 'update', 'show']);


    Route::get('daily-power-hour-reports/{time}/edit', [DailyPowerHourReportController::class, 'editByTime'])
        ->name('daily_power_hour_reports.edit')
        ->where('time', '[0-9:]+'); // цаг форматанд : зөвшөөрнө

    Route::put(
        'daily-power-hour-reports/{time}',
        [DailyPowerHourReportController::class, 'updateByTime']
    )
        ->name('daily_power_hour_reports.updateByTime');




    Route::resource('western_region_capacities', WesternRegionCapacityController::class);

    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
    Route::get('/sms/messages', [SmsController::class, 'messages'])->name('sms.messages');
});

Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
Route::get('/api/weather', [WeatherController::class, 'getWeather'])->name('weather.api');

require __DIR__ . '/auth.php';
