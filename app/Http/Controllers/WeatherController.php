<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Цаг агаарын хуудас харуулах
     */
    public function index(Request $request)
    {
        $city = $request->input('city', 'Ulaanbaatar');

        $currentWeather = $this->weatherService->getCurrentWeather($city);
        $forecast = $this->weatherService->getForecast($city);

        return view('weather.index', compact('currentWeather', 'forecast', 'city'));
    }

    /**
     * AJAX хүсэлтээр цаг агаар авах
     */
    public function getWeather(Request $request)
    {
        $city = $request->input('city', 'Ulaanbaatar');

        $currentWeather = $this->weatherService->getCurrentWeather($city);
        $forecast = $this->weatherService->getForecast($city);

        return response()->json([
            'success' => true,
            'current' => $currentWeather,
            'forecast' => $forecast
        ]);
    }
}
