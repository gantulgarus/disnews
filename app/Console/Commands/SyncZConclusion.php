<?php

namespace App\Console\Commands;

use App\Models\ZConclusion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncZConclusion extends Command
{
    protected $signature = 'sync:zconclusion';
    protected $description = 'Sync Z_Conclusion table from external DB to local DB';

    public function handle()
    {
        info("Cron Job ZConclusion running at " . now());

        $lastTs = ZConclusion::max('TIMESTAMP_S') ?? 0;

        $this->info("Starting sync after ID: $lastTs");

        DB::connection('second_db')
            ->table('Z_Conclusion')
            ->where('TIMESTAMP_S', '>', $lastTs)
            ->orderBy('TIMESTAMP_S')
            ->chunk(1000, function ($rows) {
                foreach ($rows as $row) {
                    ZConclusion::create((array) $row);
                }

                $this->info("Imported chunk of " . count($rows));
            });

        $this->info("Sync completed.");
        return Command::SUCCESS;
    }
}