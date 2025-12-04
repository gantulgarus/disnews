<?php
// app/Http/View/Composers/HeaderComposer.php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Services\WeatherService;

class HeaderComposer
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $weather = $this->weatherService->getCurrentWeather('Ulaanbaatar');
        $view->with('headerWeather', $weather);
    }
}
