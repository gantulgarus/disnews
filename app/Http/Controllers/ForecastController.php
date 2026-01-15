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
        $now = Carbon::now();

        // Өдрийн таамаглал (24 цаг)
        $dailyForecast = ForecastData::whereDate('time', $today)
            ->where('forecast_type', 'daily')
            ->orderBy('time')
            ->get();

        // Цагийн таамаглал (өнөөдрийн 00:00 + дараагийн 3 цаг)
        $hourlyForecast = ForecastData::where('forecast_type', 'hourly')
            ->where('time', '>=', $today)  // Өнөөдрийн 00:00-өөс
            ->orderBy('time')
            ->get()
            ->map(function ($item) use ($now) {
                // is_future талбарыг нэмж өгөх (JavaScript-д ашиглана)
                $itemTime = Carbon::parse($item->time);
                $item->is_future = $itemTime->gt($now);
                return $item;
            });

        // Бодит хэрэглээ (өнөөдөр)
        $actualData = ForecastData::whereDate('time', $today)
            ->where('forecast_type', 'actual')
            ->whereNotNull('actual_load')
            ->orderBy('time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'daily_forecast' => $dailyForecast,
                'hourly_forecast' => $hourlyForecast,
                'actual_data' => $actualData,
                'last_update' => $now->toIso8601String(),
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
            'data.*.is_actual' => 'sometimes|boolean',
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
                    'is_actual' => $item['is_actual'] ?? false,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Forecast data stored successfully',
            'count' => count($validated['data'])
        ]);
    }

    /**
     * График харуулах view
     */
    public function showDashboard()
    {
        return view('forecast.dashboard');
    }

    /**
     * Өнгөрсөн хоногуудын өгөгдөл устгах (cleanup)
     */
    public function cleanup()
    {
        $keepDays = 7; // 7 хоногийн өгөгдөл хадгална
        $cutoffDate = Carbon::now()->subDays($keepDays);

        $deleted = ForecastData::where('time', '<', $cutoffDate)->delete();

        return response()->json([
            'success' => true,
            'message' => "Хуучин өгөгдөл устгагдлаа",
            'deleted_count' => $deleted
        ]);
    }
}
