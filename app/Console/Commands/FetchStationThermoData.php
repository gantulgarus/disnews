<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StationThermoData;
use Illuminate\Support\Facades\Http;

class FetchStationThermoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan fetch:station-thermo
     */
    protected $signature = 'fetch:station-thermo {date?}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch thermo station data from API and save to DB';

    /**
     * Execute the console command.
     */
    // public function handle()
    // {
    //     info("Cron Job running at " . now());

    //     // Date параметр орж ирээгүй бол өнөөдрийн огноо авна
    //     $date = $this->argument('date') ?? now()->format('Y-m-d');

    //     $url = "http://portal.dulaan.mn:90/api/stations/{$date}";

    //     $this->info("Fetching data from: $url");

    //     $response = Http::get($url);

    //     if ($response->successful()) {
    //         $data = $response->json();

    //         foreach ($data as $row) {
    //             StationThermoData::updateOrCreate(
    //                 [
    //                     'infodate' => $row['infodate'],
    //                     'infotime' => $row['infotime'],
    //                 ],
    //                 $row
    //             );
    //         }

    //         $this->info("Амжилттай хадгаллаа! ({$date})");
    //     } else {
    //         $this->error("API-с өгөгдөл татаж чадсангүй!");
    //     }

    //     return 0;
    // }
    public function handle()
    {
        info("Cron Job running at " . now());

        // Хэрвээ хэрэглэгч өдөр заагаагүй бол 3 хоногийн мэдээ татна
        $targetDates = [];

        if ($this->argument('date')) {
            $targetDates[] = $this->argument('date');
        } else {
            // Өнөөдөр, өчигдөр, уржигдар
            for ($i = 0; $i < 3; $i++) {
                $targetDates[] = now()->subDays($i)->format('Y-m-d');
            }
        }

        foreach ($targetDates as $date) {
            $url = "http://portal.dulaan.mn:90/api/stations/{$date}";
            $this->info("Fetching data from: $url");

            $response = Http::timeout(60)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $count = 0;

                foreach ($data as $row) {
                    StationThermoData::updateOrCreate(
                        [
                            'infodate' => $row['infodate'],
                            'infotime' => $row['infotime'],
                        ],
                        $row
                    );
                    $count++;
                }

                $this->info("{$date} - {$count} мөр хадгаллаа.");
            } else {
                $this->error("{$date} - API-с өгөгдөл татаж чадсангүй!");
            }
        }

        $this->info("Бүх мэдээг амжилттай татаж дууслаа.");
        return 0;
    }
}
