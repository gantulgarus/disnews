<?php

namespace App\Console\Commands;

use App\Services\PowerPlantDataService;
use Illuminate\Console\Command;

class FetchPowerPlantData extends Command
{
    protected $signature = 'powerplant:fetch {date?} {--range=}';

    protected $description = 'ДЦС-ийн өгөгдлийг УБДС-ээс API-аар татах';

    public function handle(PowerPlantDataService $service)
    {
        if ($this->option('range')) {
            // Хугацааны интервал
            [$startDate, $endDate] = explode(',', $this->option('range'));
            $this->info("Өгөгдөл татаж байна: {$startDate} - {$endDate}");

            $results = $service->fetchDateRange($startDate, $endDate);

            foreach ($results as $date => $result) {
                if ($result['success']) {
                    $this->info("✓ {$date}: Хадгалагдсан: {$result['stats']['saved']}");
                } else {
                    $this->error("✗ {$date}: {$result['message']}");
                }
            }
        } else {
            // Нэг өдөр
            $date = $this->argument('date') ?? now()->format('Y-m-d');
            $this->info("Өгөгдөл татаж байна: {$date}");

            $result = $service->fetchAndStore($date);

            if ($result['success']) {
                $this->info("✓ Амжилттай!");
                $this->table(
                    ['Статус', 'Тоо'],
                    [
                        ['Хадгалагдсан', $result['stats']['saved']],
                        ['Алгассан', $result['stats']['skipped']],
                        ['Алдаа', $result['stats']['errors']],
                    ]
                );
            } else {
                $this->error("✗ Алдаа: {$result['message']}");
            }
        }

        return 0;
    }
}
