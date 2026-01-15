@extends('layouts.admin')
@section('title', 'Системийн хэрэглээний таамаглал')

@section('content')
    <div class="container-xl my-4">
        <h1 class="h3 mb-4">🔌 Системийн хэрэглээний таамаглал</h1>

        {{-- Info bar --}}
        <div class="row mb-4 gx-2">
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>Огноо:</div>
                        <div class="fw-bold" id="current-date">{{ now()->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>Сүүлийн шинэчлэл:</div>
                        <div class="fw-bold" id="last-update">--:--</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>Статус:</div>
                        <span class="badge bg-success" id="status">LIVE</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart --}}
        <div class="card">
            <div class="card-body">
                <canvas id="forecastChart" style="height: 400px;"></canvas>
            </div>
        </div>

        {{-- Legend --}}
        <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
            <div class="d-flex align-items-center gap-1">
                <span style="width:20px;height:3px;background:#ef4444;display:inline-block;"></span> Бодит хэрэглээ
            </div>
            <div class="d-flex align-items-center gap-1">
                <span style="width:20px;height:3px;background:#3b82f6;display:inline-block;"></span> Өдрийн таамаглал (24
                цаг)
            </div>
            <div class="d-flex align-items-center gap-1">
                <span style="width:20px;height:3px;background:#10b981;display:inline-block;"></span> Цагийн таамаглал (3
                цаг)
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
                        }
                    },
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'hour',
                                tooltipFormat: 'HH:mm'
                            }
                        },
                        y: {
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
                const res = await fetch('/api/forecast/today');
                const result = await res.json();
                if (result.success) {
                    updateChart(result.data);
                    updateInfo(result.data);
                }
            } catch (e) {
                console.error('Өгөгдөл татахад алдаа:', e);
                const statusEl = document.getElementById('status');
                statusEl.textContent = 'АЛДАА';
                statusEl.className = 'badge bg-danger';
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
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.2
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
                pointRadius: 2,
                tension: 0.3
            });

            if (data.hourly_forecast.length) datasets.push({
                label: 'Цагийн таамаглал',
                data: data.hourly_forecast.map(d => ({
                    x: new Date(d.time),
                    y: d.hourly_forecast
                })),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.2)',
                borderWidth: 2,
                pointRadius: 2,
                tension: 0.1
            });

            chart.data.datasets = datasets;
            chart.update('none');
        }

        function updateInfo(data) {
            const now = new Date();
            document.getElementById('last-update').textContent =
                now.toLocaleTimeString('mn-MN', {
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
