<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TnewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PowerPlantController;
use App\Http\Controllers\OrderJournalController;
use App\Http\Controllers\DailyBalanceJournalController;
use App\Http\Controllers\PowerPlantDailyReportController;




// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('power-plant-daily-reports', PowerPlantDailyReportController::class);
    Route::resource('power-plants', PowerPlantController::class);
    Route::resource('daily-balance-journals', DailyBalanceJournalController::class);
    Route::get('daily-balance-journals/report', [DailyBalanceJournalController::class, 'dailyMatrixReport'])->name('daily-balance-journals.report');

    Route::resource('users', UserController::class);
    Route::resource('tnews', TnewsController::class);

    Route::resource('order-journals', OrderJournalController::class);
});

require __DIR__ . '/auth.php';