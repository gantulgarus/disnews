<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ForecastData;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    /**
     * Өнөөдрийн бүх forecast өгөгдөл авах
     */
    public function getTodayForecast()
    {
        $today = Carbon::today();

        // Өдрийн таамаглал (24 цаг)
        $dailyForecast = ForecastData::whereDate('time', $today)
            ->where('forecast_type', 'daily')
            ->orderBy('time')
            ->get();

        // Цагийн таамаглал (сүүлийн + 3 цаг)
        $hourlyForecast = ForecastData::where('forecast_type', 'hourly')
            ->where('time', '>=', Carbon::now()->subHour())
            ->orderBy('time')
            ->get();

        // Бодит хэрэглээ (өнөөдөр)
        $actualData = ForecastData::whereDate('time', $today)
            ->whereNotNull('actual_load')
            ->orderBy('time')
            ->get();

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

    /**
     * Python скриптээс датаг хадгалах
     */
    public function storeForecast(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:daily,hourly,actual',
            'data' => 'required|array',
            'data.*.time' => 'required|date',
            'data.*.value' => 'required|numeric',
        ]);

        foreach ($validated['data'] as $item) {
            ForecastData::updateOrCreate(
                [
                    'time' => $item['time'],
                    'forecast_type' => $validated['type'],
                ],
                [
                    'actual_load' => $validated['type'] === 'actual' ? $item['value'] : null,
                    'daily_forecast' => $validated['type'] === 'daily' ? $item['value'] : null,
                    'hourly_forecast' => $validated['type'] === 'hourly' ? $item['value'] : null,
                    'is_actual' => isset($item['is_actual']) ? $item['is_actual'] : false,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Forecast data stored successfully'
        ]);
    }

    /**
     * График харуулах view
     */
    public function showDashboard()
    {
        return view('forecast.dashboard');
    }
}
