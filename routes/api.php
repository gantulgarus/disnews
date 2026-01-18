<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\DashboardController;

Route::get('/forecast/today', [ForecastController::class, 'getTodayForecast']);
Route::post('/forecast/store', [ForecastController::class, 'storeForecast']);
Route::get('/forecast/last-history-time', [ForecastController::class, 'getLastHistoryTime']);
Route::get('/forecast/history', [ForecastController::class, 'getHistoryByDate']);
Route::get('/forecast/available-dates', [ForecastController::class, 'getAvailableDates']);
Route::get('/forecast/metrics', [ForecastController::class, 'getMetrics']);

// Dashboard API - Realtime power data
Route::get('/dashboard/realtime', [DashboardController::class, 'apiRealtimeData']);
