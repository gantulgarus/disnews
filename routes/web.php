<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TnewsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BufVIntController;
use App\Http\Controllers\DisCoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PowerPlantController;
use App\Http\Controllers\OrderJournalController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RegionalReportController;
use App\Http\Controllers\PermissionLevelController;
use App\Http\Controllers\TelephoneMessageController;
use App\Http\Controllers\ZenonHourlyPowerController;
use App\Http\Controllers\PowerPlantReadingController;
use App\Http\Controllers\StationThermoDataController;
use App\Http\Controllers\ThermoDailyRegimeController;
use App\Http\Controllers\AltaiRegionCapacityController;
use App\Http\Controllers\DailyBalanceBatteryController;
use App\Http\Controllers\DailyBalanceJournalController;
use App\Http\Controllers\DailyPowerEquipmentController;
use App\Http\Controllers\ElectricDailyRegimeController;
use App\Http\Controllers\DailyEquipmentReportController;
use App\Http\Controllers\DailyPowerHourReportController;
use App\Http\Controllers\PowerDistributionWorkController;
use App\Http\Controllers\PowerEnergyAdjustmentController;
use App\Http\Controllers\PowerPlantDailyReportController;
use App\Http\Controllers\WesternRegionCapacityController;
use App\Http\Controllers\DailyBalanceImportExportController;
use App\Http\Controllers\PowerPlantThermoEquipmentController;



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
    // Тусдаа нууц үг солих route-ууд
    Route::get('users/{user}/password', [UserController::class, 'editPassword'])
        ->name('users.edit-password');

    Route::put('users/{user}/password', [UserController::class, 'updatePassword'])
        ->name('users.update-password');

    // Өөрийн профайлын нууц үг солих
    Route::get('profile/password', [UserController::class, 'editOwnPassword'])
        ->name('profile.edit-password');

    Route::put('profile/password', [UserController::class, 'updateOwnPassword'])
        ->name('profile.update-password');

    Route::resource('tnews', TnewsController::class);

    Route::resource('order-journals', OrderJournalController::class);
    Route::post('order-journals/{orderJournal}/forward', [OrderJournalController::class, 'forward'])->name('order-journals.forward');
    // Route::post('order-journal-approvals/{approval}/approve', [OrderJournalController::class, 'approve'])->name('order-journal-approvals.approve');
    Route::post('order-journals/approve-opinion/{approval}', [OrderJournalController::class, 'approveOpinion'])
        ->name('order-journals.approveOpinion');
    Route::post('order-journals/{orderJournal}/approve', [OrderJournalController::class, 'approve'])
        ->name('order-journals.approve');
    Route::put('/order-journals/{orderJournal}/approvers', [OrderJournalController::class, 'updateApprovers'])
        ->name('order-journals.updateApprovers');


    Route::post('/order-journals/{id}/open', [OrderJournalController::class, 'open'])->name('order-journals.open');
    Route::post('/order-journals/{id}/close', [OrderJournalController::class, 'close'])->name('order-journals.close');



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



    Route::get(
        '/daily-power-hour-reports/edit/{powerPlantId}/{time}',
        [DailyPowerHourReportController::class, 'edit']
    )
        ->name('daily_power_hour_reports.editByPlantAndTime');

    Route::resource('daily-balance-batteries', DailyBalanceBatteryController::class);
    Route::resource('daily-balance-import-exports', DailyBalanceImportExportController::class);
    Route::resource('altai-region-capacity', AltaiRegionCapacityController::class);

    Route::post(
        '/daily-power-hour-reports/update/{powerPlantId}/{time}',
        [DailyPowerHourReportController::class, 'update']
    )
        ->name('daily_power_hour_reports.updateByPlantAndTime');

    Route::get('/zenon-hourly-power', [ZenonHourlyPowerController::class, 'index'])
        ->name('zenon.hourly-power');
    Route::get('/zenon/peak-hour-power', [ZenonHourlyPowerController::class, 'peakHour'])
        ->name('zenon.peak-hour-power');



    Route::resource('western_region_capacities', WesternRegionCapacityController::class);

    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
    Route::get('/sms/messages', [SmsController::class, 'messages'])->name('sms.messages');


    Route::get('/reports/regional', [RegionalReportController::class, 'index'])
        ->name('reports.Regional');

    Route::resource('power-energy-adjustments', PowerEnergyAdjustmentController::class);
    Route::resource('power-plant-thermo-equipments', PowerPlantThermoEquipmentController::class);


    Route::prefix('power-plant-readings')->group(function () {
        // Үндсэн хуудас - жагсаалт
        Route::get('/', [PowerPlantReadingController::class, 'index'])
            ->name('power-plant-readings.index');

        // Өдрийн нэгтгэл - ШИНЭ
        Route::get('/daily-overview', [PowerPlantReadingController::class, 'dailyOverview'])
            ->name('power-plant-readings.daily-overview');

        Route::get('/temperature-charts', [PowerPlantReadingController::class, 'temperatureCharts'])
            ->name('power-plant-readings.temperature-charts');

        // API endpoints
        Route::post('fetch', [PowerPlantReadingController::class, 'fetch'])
            ->name('power-plant-readings.fetch');

        Route::get('show', [PowerPlantReadingController::class, 'show'])
            ->name('power-plant-readings.show');

        Route::get('statistics', [PowerPlantReadingController::class, 'statistics'])
            ->name('power-plant-readings.statistics');

        Route::get('latest', [PowerPlantReadingController::class, 'latest'])
            ->name('power-plant-readings.latest');
    });
    // Гараар бүртгэх
    Route::get('/power-plant-readings/manual-entry', [PowerPlantReadingController::class, 'create'])
        ->name('power-plant-readings.create');
    Route::post('/power-plant-readings/manual-entry', [PowerPlantReadingController::class, 'storeBulk'])
        ->name('power-plant-readings.storeBulk');

    Route::get('/power-plant-readings/edit', [PowerPlantReadingController::class, 'edit'])
        ->name('power-plant-readings.edit');
    Route::put('/power-plant-readings/update', [PowerPlantReadingController::class, 'updateBulk'])
        ->name('power-plant-readings.updateBulk');
    // bulk delete
    Route::delete('/power-plant-readings/delete', [PowerPlantReadingController::class, 'destroyBulk'])
        ->name('power-plant-readings.destroyBulk');


    Route::get('power-plant-readings/hour-data', [PowerPlantReadingController::class, 'getHourData'])->name('power-plant-readings.getHourData');

    Route::get('/bufvint/today', [\App\Http\Controllers\BufVIntController::class, 'todayData'])->name('bufvint.today');
    Route::post('/ru-xml/import', [BufVIntController::class, 'importRussianXml'])
        ->name('ru-xml.import');

    Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
        Route::get('{user}/permissions', [UserController::class, 'editPermissions'])->name('edit-permissions');
        Route::post('{user}/permissions', [UserController::class, 'updatePermissions'])->name('update-permissions');
    });

    Route::prefix('permissions')->name('permissions.')->middleware('auth')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
    });
});

Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
Route::get('/api/weather', [WeatherController::class, 'getWeather'])->name('weather.api');

require __DIR__ . '/auth.php';