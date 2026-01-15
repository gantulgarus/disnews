<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('fetch:station-thermo')->hourly();
Schedule::command('powerplant:fetch')->hourly();
// Schedule::command('sync:zconclusion')->everyTenMinutes();

// цаг тутмын хэрэглээний таамаглалыг python-оос авах
$schedule->command('forecast:update')
    ->hourly()                      // Цаг тутам
    ->withoutOverlapping()          // Давхцахгүй байх
    ->runInBackground()             // Background-д ажиллана
    ->emailOutputOnFailure('gantulgarus@gmail.com');  // Алдаа гарвал имэйл илгээх