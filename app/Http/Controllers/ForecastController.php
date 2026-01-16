<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ForecastData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ForecastController extends Controller
{
    public function getTodayForecast()
    {
        $today = Carbon::today();

        // Өдрийн таамаглал (24 цаг)
        $dailyForecast = ForecastData::whereDate('time', $today)
            ->where('forecast_type', 'daily')
            ->orderBy('time')
            ->get();

        // Цагийн таамаглал (бүгд нэг шугам)
        $hourlyForecast = ForecastData::where('forecast_type', 'hourly')
            ->where('time', '>=', $today)
            ->orderBy('time')
            ->get();

        // Бодит хэрэглээ
        $actualData = ForecastData::whereDate('time', $today)
            ->where('forecast_type', 'actual')
            ->whereNotNull('actual_load')
            ->orderBy('time')
            ->get()
            ->map(function ($item) {
                return [
                    'time' => $item->time,
                    'actual_load' => $item->actual_load,
                    'system_load' => $item->system_load  // 🔴 НЭМ
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'daily_forecast' => $dailyForecast,
                'hourly_forecast' => $hourlyForecast,
                'actual_data' => $actualData,
                'last_update' => Carbon::now()->toIso8601String(),
            ]
        ]);
    }

    public function storeForecast(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:daily,hourly,actual',
            'data' => 'required|array',
            'data.*.time' => 'required|date',
            'data.*.value' => 'required|numeric',
            'data.*.system_load' => 'nullable|numeric',
        ]);

        Log::info('Validated data:', $validated);

        foreach ($validated['data'] as $item) {
            Log::info('Item:', $item);
            ForecastData::updateOrCreate(
                [
                    'time' => $item['time'],
                    'forecast_type' => $validated['type'],
                ],
                [
                    'actual_load' => $validated['type'] === 'actual' ? $item['value'] : null,
                    'system_load' => $item['system_load'] ?? null,
                    'daily_forecast' => $validated['type'] === 'daily' ? $item['value'] : null,
                    'hourly_forecast' => $validated['type'] === 'hourly' ? $item['value'] : null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Forecast data stored successfully',
            'count' => count($validated['data'])
        ]);
    }

    public function showDashboard()
    {
        return view('forecast.dashboard');
    }
}