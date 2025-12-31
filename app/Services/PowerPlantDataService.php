<?php

namespace App\Services;

use App\Models\PowerPlantReading;
use App\Models\PowerPlantThermoEquipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PowerPlantDataService
{
    private string $apiUrl = 'http://portal.dulaan.mn:90/api/stations';

    /**
     * Тодорхой өдрийн өгөгдлийг татах
     */
    public function fetchAndStore(string $date): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->apiUrl}/{$date}");

            if (!$response->successful()) {
                throw new \Exception("API хариу буруу: " . $response->status());
            }

            $data = $response->json();

            if (empty($data)) {
                return ['success' => false, 'message' => 'Өгөгдөл олдсонгүй'];
            }

            $result = $this->storeData($data);

            return [
                'success' => true,
                'message' => 'Амжилттай хадгалагдлаа',
                'stats' => $result
            ];
        } catch (\Exception $e) {
            Log::error('PowerPlant API татахад алдаа: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Алдаа гарлаа: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Өгөгдлийг database-д хадгалах
     */
    private function storeData(array $data): array
    {
        $saved = 0;
        $skipped = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($data as $hourData) {
                $result = $this->storeHourlyData($hourData);
                $saved += $result['saved'];
                $skipped += $result['skipped'];
                $errors += $result['errors'];
            }

            DB::commit();

            return [
                'saved' => $saved,
                'skipped' => $skipped,
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Нэг цагийн өгөгдлийг хадгалах
     */
    private function storeHourlyData(array $hourData): array
    {
        $saved = 0;
        $skipped = 0;
        $errors = 0;

        $date = $hourData['infodate'];
        $hour = $hourData['infotime'];

        // Equipment-уудыг нэг удаа татах (Performance)
        $equipments = PowerPlantThermoEquipment::pluck('id', 'code')->toArray();

        foreach ($hourData as $code => $value) {
            // Огноо, цаг алгасах
            if (in_array($code, ['infodate', 'infotime'])) {
                continue;
            }

            // Equipment олох
            if (!isset($equipments[$code])) {
                $errors++;
                Log::warning("Equipment олдсонгүй: {$code}");
                continue;
            }

            try {
                // updateOrCreate ашиглан давхардуулахгүй
                PowerPlantReading::updateOrCreate(
                    [
                        'power_plant_thermo_equipment_id' => $equipments[$code],
                        'reading_date' => $date,
                        'reading_hour' => $hour,
                    ],
                    [
                        'value' => $value,
                    ]
                );
                $saved++;
            } catch (\Exception $e) {
                $errors++;
                Log::error("Хадгалахад алдаа ({$code}): " . $e->getMessage());
            }
        }

        return [
            'saved' => $saved,
            'skipped' => $skipped,
            'errors' => $errors
        ];
    }

    /**
     * Хугацааны интервалаар өгөгдөл татах
     */
    public function fetchDateRange(string $startDate, string $endDate): array
    {
        $results = [];
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            $results[$date] = $this->fetchAndStore($date);
            $start->modify('+1 day');
        }

        return $results;
    }
}
