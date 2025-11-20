<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Цаг агаарын мэдээ - {{ $city }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .search-box {
            text-align: center;
            margin-bottom: 30px;
        }

        .search-box input {
            padding: 15px 25px;
            font-size: 18px;
            border: none;
            border-radius: 50px;
            width: 300px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .search-box button {
            padding: 15px 30px;
            font-size: 18px;
            margin-left: 10px;
            border: none;
            border-radius: 50px;
            background: #fff;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .search-box button:hover {
            transform: translateY(-2px);
        }

        .current-weather {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }

        .city-name {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .temperature {
            font-size: 72px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
        }

        .description {
            font-size: 24px;
            color: #666;
            text-transform: capitalize;
            margin-bottom: 30px;
        }

        .weather-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .detail-label {
            font-size: 14px;
            color: #888;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .forecast {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .forecast-item {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .forecast-date {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .forecast-temp {
            font-size: 28px;
            color: #667eea;
            margin: 10px 0;
        }

        .error {
            background: #ff6b6b;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .weather-icon {
            font-size: 100px;
            margin: 20px 0;
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.2));
        }

        .forecast-icon {
            font-size: 60px;
            margin: 10px 0;
        }

        /* Animated icons */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .weather-icon {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-box">
            <form action="{{ route('weather.index') }}" method="GET">
                <input type="text" name="city" placeholder="Хотын нэр оруулах..." value="{{ $city }}">
                <button type="submit">Хайх</button>
            </form>
        </div>

        @if ($currentWeather)
            <div class="current-weather">
                <div class="city-name">{{ $currentWeather['city'] }}, {{ $currentWeather['country'] }}</div>

                <div class="weather-icon">
                    {!! getWeatherEmoji($currentWeather['icon']) !!}
                </div>

                <div class="temperature">{{ $currentWeather['temperature'] }}°C</div>
                <div class="description">{{ $currentWeather['description'] }}</div>

                <div class="weather-details">
                    <div class="detail-item">
                        <div class="detail-label">Мэдрэгдэх температур</div>
                        <div class="detail-value">{{ $currentWeather['feels_like'] }}°C</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Чийгшил</div>
                        <div class="detail-value">{{ $currentWeather['humidity'] }}%</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Салхины хурд</div>
                        <div class="detail-value">{{ $currentWeather['wind_speed'] }} м/с</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Даралт</div>
                        <div class="detail-value">{{ $currentWeather['pressure'] }} hPa</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Нар мандах</div>
                        <div class="detail-value">{{ $currentWeather['sunrise'] }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Нар жаргах</div>
                        <div class="detail-value">{{ $currentWeather['sunset'] }}</div>
                    </div>
                </div>
            </div>

            @if ($forecast)
                <h2 style="color: white; text-align: center; margin-bottom: 20px;">5 хоногийн таамаглал</h2>
                <div class="forecast">
                    @foreach ($forecast as $day)
                        <div class="forecast-item">
                            <div class="forecast-date">{{ date('m/d', strtotime($day['date'])) }}</div>
                            <div class="forecast-date">{{ $day['day'] }}</div>
                            <div class="forecast-icon">
                                {!! getWeatherEmoji($day['icon']) !!}
                            </div>
                            <div class="forecast-temp">
                                {{ round($day['temp_max']) }}° / {{ round($day['temp_min']) }}°
                            </div>
                            <div style="color: #666; margin-top: 10px;">{{ $day['description'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="error">
                Цаг агаарын мэдээ авахад алдаа гарлаа. Хотын нэрээ шалгана уу.
            </div>
        @endif
    </div>
</body>

</html>

@php
    function getWeatherEmoji($iconCode)
    {
        $emojiMap = [
            '01d' => '☀️', // clear sky day
            '01n' => '🌙', // clear sky night
            '02d' => '⛅', // few clouds day
            '02n' => '☁️', // few clouds night
            '03d' => '☁️', // scattered clouds
            '03n' => '☁️',
            '04d' => '☁️', // broken clouds
            '04n' => '☁️',
            '09d' => '🌧️', // shower rain
            '09n' => '🌧️',
            '10d' => '🌦️', // rain day
            '10n' => '🌧️', // rain night
            '11d' => '⛈️', // thunderstorm
            '11n' => '⛈️',
            '13d' => '❄️', // snow
            '13n' => '❄️',
            '50d' => '🌫️', // mist
            '50n' => '🌫️',
        ];

        return $emojiMap[$iconCode] ?? '🌡️';
    }
@endphp
