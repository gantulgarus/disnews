<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Системийн хэрэглээний таамаглал</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }

        h1 {
            font-size: 28px;
            color: #2d3748;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: #f7fafc;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label {
            font-size: 13px;
            color: #718096;
            font-weight: 500;
        }

        .info-value {
            font-size: 18px;
            color: #2d3748;
            font-weight: 700;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-live {
            background: #48bb78;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .chart-container {
            position: relative;
            height: 500px;
            margin-top: 20px;
        }

        .legend-custom {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #f7fafc;
            border-radius: 8px;
        }

        .legend-color {
            width: 20px;
            height: 3px;
            border-radius: 2px;
        }

        .loader {
            text-align: center;
            padding: 60px;
            color: #718096;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔌 Системийн хэрэглээний таамаглал</h1>

        <div class="info-bar">
            <div class="info-item">
                <span class="info-label">Огноо:</span>
                <span class="info-value" id="current-date">{{ now()->format('Y-m-d') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Сүүлийн шинэчлэл:</span>
                <span class="info-value" id="last-update">--:--</span>
            </div>
            <div class="info-item">
                <span class="status-badge status-live" id="status">● LIVE</span>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="forecastChart"></canvas>
        </div>

        <div class="legend-custom">
            <div class="legend-item">
                <div class="legend-color" style="background: #ef4444;"></div>
                <span>Бодит хэрэглээ</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #3b82f6; opacity: 0.7;"></div>
                <span>Өдрийн таамаглал (24 цаг)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #10b981;"></div>
                <span>Цагийн таамаглал (3 цаг)</span>
            </div>
        </div>
    </div>

    <script>
        let chart = null;

        // График үүсгэх
        function initChart() {
            const ctx = document.getElementById('forecastChart').getContext('2d');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' +
                                        Math.round(context.parsed.y).toLocaleString() + ' МВт';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'hour',
                                displayFormats: {
                                    hour: 'HH:mm'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Цаг'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Хэрэглээ (МВт)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return Math.round(value).toLocaleString();
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }

        // Өгөгдөл татах
        async function fetchData() {
            try {
                const response = await fetch('/api/forecast/today');
                const result = await response.json();

                if (result.success) {
                    updateChart(result.data);
                    updateInfo(result.data);
                }
            } catch (error) {
                console.error('Өгөгдөл татахад алдаа:', error);
                document.getElementById('status').textContent = '● АЛДАА';
                document.getElementById('status').className = 'status-badge';
                document.getElementById('status').style.background = '#ef4444';
            }
        }

        // График шинэчлэх
        function updateChart(data) {
            const datasets = [];

            // Бодит хэрэглээ
            if (data.actual_data && data.actual_data.length > 0) {
                datasets.push({
                    label: 'Бодит хэрэглээ',
                    data: data.actual_data.map(d => ({
                        x: new Date(d.time),
                        y: d.actual_load
                    })),
                    borderColor: '#ef4444',
                    backgroundColor: '#ef4444',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.1,
                    order: 1
                });
            }

            // Өдрийн таамаглал
            if (data.daily_forecast && data.daily_forecast.length > 0) {
                datasets.push({
                    label: 'Өдрийн таамаглал',
                    data: data.daily_forecast.map(d => ({
                        x: new Date(d.time),
                        y: d.daily_forecast
                    })),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 3,
                    tension: 0.3,
                    order: 3
                });
            }

            // Цагийн таамаглал
            if (data.hourly_forecast && data.hourly_forecast.length > 0) {
                datasets.push({
                    label: 'Цагийн таамаглал',
                    data: data.hourly_forecast.map(d => ({
                        x: new Date(d.time),
                        y: d.hourly_forecast
                    })),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderWidth: 3,
                    pointRadius: d => d.raw.is_actual ? 8 : 5,
                    pointStyle: d => d.raw.is_actual ? 'circle' : 'triangle',
                    tension: 0.1,
                    order: 2
                });
            }

            chart.data.datasets = datasets;
            chart.update('none');
        }

        // Мэдээлэл шинэчлэх
        function updateInfo(data) {
            const now = new Date();
            document.getElementById('last-update').textContent =
                now.toLocaleTimeString('mn-MN', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }

        // Эхлүүлэх
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
            fetchData();

            // 5 минут тутамд шинэчлэх
            setInterval(fetchData, 5 * 60 * 1000);
        });
    </script>
</body>

</html>
