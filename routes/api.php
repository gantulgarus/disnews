<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForecastController;

Route::get('/forecast/today', [ForecastController::class, 'getTodayForecast']);
Route::post('/forecast/store', [ForecastController::class, 'storeForecast']);
