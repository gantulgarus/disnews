@extends('layouts.admin') {{-- Таны админ layout нэртэй --}}
@section('title', 'Системийн хэрэглээний таамаглал')

@section('content')
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


    <style>
        /* Чиний CSS кодыг энд хуулж болно, эсвэл admin layout-д байгаа tailwind, bootstrap ашиглаж болно */
        /* body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .chart-container {
                height: 500px;
                margin-top: 20px;
            } */

        /* ... бусад css ... */
    </style>
@endsection

@section('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js">
    </script>

    <script>
        let chart = null;

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
                                    return context.dataset.label + ': ' + Math.round(context.parsed.y)
                                        .toLocaleString() + ' МВт';
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
                                callback: v => Math.round(v).toLocaleString()
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

        async function fetchData() {
            try {
                const response = await fetch('/api/forecast/today');
                const result = await response.json();
                if (result.success) {
                    updateChart(result.data);
                    updateInfo(result.data);
                }
            } catch (e) {
                console.error('Өгөгдөл татахад алдаа:', e);
                const statusEl = document.getElementById('status');
                statusEl.textContent = '● АЛДАА';
                statusEl.className = 'status-badge';
                statusEl.style.background = '#ef4444';
            }
        }

        function updateChart(data) {
            const datasets = [];
            if (data.actual_data.length) datasets.push({
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
            if (data.daily_forecast.length) datasets.push({
                label: 'Өдрийн таамаглал',
                data: data.daily_forecast.map(d => ({
                    x: new Date(d.time),
                    y: d.daily_forecast
                })),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.1)',
                borderWidth: 2,
                borderDash: [5, 5],
                pointRadius: 3,
                tension: 0.3,
                order: 3
            });
            if (data.hourly_forecast.length) datasets.push({
                label: 'Цагийн таамаглал',
                data: data.hourly_forecast.map(d => ({
                    x: new Date(d.time),
                    y: d.hourly_forecast
                })),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.2)',
                borderWidth: 3,
                pointRadius: 5,
                tension: 0.1,
                order: 2
            });
            chart.data.datasets = datasets;
            chart.update('none');
        }

        function updateInfo(data) {
            const now = new Date();
            document.getElementById('last-update').textContent = now.toLocaleTimeString('mn-MN', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initChart();
            fetchData();
            setInterval(fetchData, 5 * 60 * 1000);
        });
    </script>
@endsection
