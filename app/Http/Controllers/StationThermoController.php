<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StationThermoData;
use Illuminate\Support\Facades\Http;

class StationThermoController extends Controller
{
    public function fetchAndSave()
    {
        $response = Http::get('http://portal.dulaan.mn:90/api/stations/2025-09-22');

        if ($response->successful()) {
            $data = $response->json();

            foreach ($data as $row) {
                StationThermoData::updateOrCreate(
                    [
                        'infodate' => $row['infodate'],
                        'infotime' => $row['infotime'],
                    ],
                    $row
                );
            }

            return "Амжилттай хадгаллаа!";
        }

        return "Алдаа гарлаа!";
    }
}
