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
        $startTime = $today->copy()->addHour(); // 01:00
        $endTime = $today->copy()->addDay(); // Маргааш 00:00

        // Өдрийн таамаглал (01:00 - 00:00)
        $dailyForecast = ForecastData::where('time', '>=', $startTime)
            ->where('time', '<=', $endTime)
            ->where('forecast_type', 'daily')
            ->orderBy('time')
            ->get();

        // Цагийн таамаглал (01:00 - 00:00)
        $hourlyForecast = ForecastData::where('forecast_type', 'hourly')
            ->where('time', '>=', $startTime)
            ->where('time', '<=', $endTime)
            ->orderBy('time')
            ->get();

        // Бодит хэрэглээ (01:00 - 00:00)
        $actualData = ForecastData::where('time', '>=', $startTime)
            ->where('time', '<=', $endTime)
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
        $type = $request->input('type');

        // Metrics төрөл бол тусад нь боловсруулах
        if ($type === 'metrics') {
            return $this->storeMetrics($request);
        }

        $validated = $request->validate([
            'type' => 'required|in:daily,hourly,actual,history',
            'data' => 'required|array',
            'data.*.time' => 'required|date',
            'data.*.value' => 'required|numeric',
            'data.*.system_load' => 'nullable|numeric',
            'data.*.forecast_daily' => 'nullable|numeric',
            'data.*.forecast_hourly' => 'nullable|numeric',
        ]);

        Log::info('Validated data:', $validated);

        foreach ($validated['data'] as $item) {
            // History төрлийн хувьд бүх талбарыг нэг мөрөнд хадгалах
            if ($validated['type'] === 'history') {
                ForecastData::updateOrCreate(
                    [
                        'time' => $item['time'],
                        'forecast_type' => 'history',
                    ],
                    [
                        'actual_load' => $item['value'],
                        'system_load' => $item['system_load'] ?? null,
                        'daily_forecast' => $item['forecast_daily'] ?? null,
                        'hourly_forecast' => $item['forecast_hourly'] ?? null,
                    ]
                );
            } else {
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

    // Сүүлийн түүхэн датаны цагийг авах
    public function getLastHistoryTime()
    {
        $lastRecord = ForecastData::where('forecast_type', 'history')
            ->orderBy('time', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'last_time' => $lastRecord ? $lastRecord->time : null
        ]);
    }

    // Огноогоор түүхэн өгөгдөл авах
    public function getHistoryByDate(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($validated['date']);
        $startTime = $date->copy()->addHour(); // 01:00
        $endTime = $date->copy()->addDay(); // Маргааш 00:00

        // History өгөгдлөөс бүх мэдээллийг авах
        $historyData = ForecastData::where('forecast_type', 'history')
            ->where('time', '>=', $startTime)
            ->where('time', '<=', $endTime)
            ->orderBy('time')
            ->get()
            ->map(function ($item) {
                return [
                    'time' => $item->time,
                    'actual_load' => $item->actual_load,
                    'system_load' => $item->system_load,
                    'daily_forecast' => $item->daily_forecast,
                    'hourly_forecast' => $item->hourly_forecast,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'history' => $historyData,
                'date' => $date->toDateString(),
            ]
        ]);
    }

    // Боломжтой огноонуудыг авах
    public function getAvailableDates()
    {
        $dates = ForecastData::where('forecast_type', 'history')
            ->selectRaw('DATE(time) as date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->pluck('date');

        return response()->json([
            'success' => true,
            'dates' => $dates
        ]);
    }

    // Модель үнэлгээний мэдээлэл хадгалах
    private function storeMetrics(Request $request)
    {
        $data = $request->input('data');

        // Cache эсвэл файлд хадгалах (энгийн хувилбар)
        $metricsPath = storage_path('app/forecast_metrics.json');
        file_put_contents($metricsPath, json_encode($data, JSON_PRETTY_PRINT));

        Log::info('Forecast metrics stored:', $data);

        return response()->json([
            'success' => true,
            'message' => 'Metrics stored successfully'
        ]);
    }

    // Модель үнэлгээний мэдээлэл авах
    public function getMetrics()
    {
        $metricsPath = storage_path('app/forecast_metrics.json');

        if (file_exists($metricsPath)) {
            $metrics = json_decode(file_get_contents($metricsPath), true);
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No metrics data found'
        ]);
    }
}
