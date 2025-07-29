<?php

use App\Http\Controllers\PowerPlantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PowerPlantDailyReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TnewsController; 




// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
   // Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('power-plant-daily-reports', PowerPlantDailyReportController::class);
    Route::resource('power-plants', PowerPlantController::class);

    Route::resource('users', UserController::class);
    Route::resource('tnews', TnewsController::class);
    


});

require __DIR__ . '/auth.php';

