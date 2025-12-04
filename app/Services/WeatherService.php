<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    private $apiKey;
    private $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
    }

    /**
     * Хотын нэрээр цаг агаар авах
     */
    public function getCurrentWeather($city = 'Ulaanbaatar')
    {
        // Cache ашиглан 10 минут хадгалах
        return Cache::remember("weather_{$city}", 600, function () use ($city) {
            try {
                $response = Http::get("{$this->baseUrl}/weather", [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'mn'
                ]);

                if ($response->successful()) {
                    return $this->formatWeatherData($response->json());
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Weather API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * 5 хоногийн таамаглал авах
     */
    public function getForecast($city = 'Ulaanbaatar')
    {
        return Cache::remember("forecast_{$city}", 600, function () use ($city) {
            try {
                $response = Http::get("{$this->baseUrl}/forecast", [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'mn'
                ]);

                if ($response->successful()) {
                    return $this->formatForecastData($response->json());
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Forecast API error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Цаг агаарын өгөгдлийг форматлах
     */
    private function formatWeatherData($data)
    {
        return [
            'temperature' => round($data['main']['temp']),
            'feels_like' => round($data['main']['feels_like']),
            'humidity' => $data['main']['humidity'],
            'pressure' => $data['main']['pressure'],
            'description' => $data['weather'][0]['description'],
            'icon' => $data['weather'][0]['icon'],
            'wind_speed' => $data['wind']['speed'],
            'city' => $data['name'],
            'country' => $data['sys']['country'],
            'sunrise' => date('H:i', $data['sys']['sunrise']),
            'sunset' => date('H:i', $data['sys']['sunset']),
        ];
    }

    /**
     * Таамаглалын өгөгдлийг форматлах
     */
    private function formatForecastData($data)
    {
        $forecast = [];

        foreach ($data['list'] as $item) {
            $date = date('Y-m-d', $item['dt']);

            if (!isset($forecast[$date])) {
                $forecast[$date] = [
                    'date' => $date,
                    'day' => date('l', $item['dt']),
                    'temp_min' => $item['main']['temp_min'],
                    'temp_max' => $item['main']['temp_max'],
                    'description' => $item['weather'][0]['description'],
                    'icon' => $item['weather'][0]['icon'],
                ];
            } else {
                $forecast[$date]['temp_min'] = min($forecast[$date]['temp_min'], $item['main']['temp_min']);
                $forecast[$date]['temp_max'] = max($forecast[$date]['temp_max'], $item['main']['temp_max']);
            }
        }

        return array_values(array_slice($forecast, 0, 5));
    }

    /**
     * Дүрсний URL авах
     */
    public function getIconUrl($icon)
    {
        return "https://openweathermap.org/img/wn/{$icon}@2x.png";
    }
}
