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
                <canvas id="forecastChart" style="height: 450px;"></canvas>
            </div>
        </div>

        {{-- Legend --}}
        <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <span style="width:24px;height:4px;background:#ef4444;display:inline-block;border-radius:2px;"></span>
                <span>Бодит хэрэглээ</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span
                    style="width:24px;height:4px;background:#3b82f6;display:inline-block;border-radius:2px;opacity:0.7;"></span>
                <span>Өдрийн таамаглал (24 цаг)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="width:24px;height:4px;background:#10b981;display:inline-block;border-radius:2px;"></span>
                <span>Цагийн таамаглал (өнөөдөр)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span style="width:24px;height:4px;background:#32cd32;display:inline-block;border-radius:2px;"></span>
                <span>Ирээдүйн таамаглал (3 цаг)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span
                    style="width:12px;height:12px;background:#0a8754;display:inline-block;border-radius:50%;border:2px solid #000;"></span>
                <span>Сүүлийн бодит цэг</span>
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

            // 1️⃣ Бодит хэрэглээ
            if (data.actual_data && data.actual_data.length) {
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

            // 2️⃣ Өдрийн таамаглал
            if (data.daily_forecast && data.daily_forecast.length) {
                datasets.push({
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
                    order: 4
                });
            }

            // 3️⃣ Цагийн таамаглал - хоёр хэсэг
            if (data.hourly_forecast && data.hourly_forecast.length) {
                // Өнөөдрийн хэсэг (is_future: false/null)
                const hourlyToday = data.hourly_forecast.filter(d => !d.is_future);

                // Ирээдүйн хэсэг (is_future: true)
                const hourlyFuture = data.hourly_forecast.filter(d => d.is_future);

                // Сүүлийн бодит цэг (is_actual: true)
                const actualPoint = data.hourly_forecast.find(d => d.is_actual);

                // Өнөөдрийн цагийн таамаглал
                if (hourlyToday.length > 0) {
                    datasets.push({
                        label: 'Цагийн таамаглал (өнөөдөр)',
                        data: hourlyToday.map(d => ({
                            x: new Date(d.time),
                            y: d.hourly_forecast
                        })),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.1)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        tension: 0.1,
                        order: 2
                    });
                }

                // Сүүлийн бодит цэг (том дугуй)
                if (actualPoint) {
                    datasets.push({
                        label: 'Сүүлийн бодит цэг',
                        data: [{
                            x: new Date(actualPoint.time),
                            y: actualPoint.hourly_forecast
                        }],
                        borderColor: '#000',
                        backgroundColor: '#0a8754',
                        borderWidth: 2,
                        pointRadius: 10,
                        pointHoverRadius: 12,
                        showLine: false,
                        order: 0
                    });
                }

                // Ирээдүйн 3 цагийн таамаглал
                if (hourlyFuture.length > 0) {
                    // Сүүлийн бодит цэгээс эхлүүлэх
                    let futureData = hourlyFuture.map(d => ({
                        x: new Date(d.time),
                        y: d.hourly_forecast
                    }));

                    // Сүүлийн бодит цэгийг нэмж залгах
                    if (actualPoint) {
                        futureData.unshift({
                            x: new Date(actualPoint.time),
                            y: actualPoint.hourly_forecast
                        });
                    }

                    datasets.push({
                        label: 'Ирээдүйн таамаглал (3 цаг)',
                        data: futureData,
                        borderColor: '#32cd32',
                        backgroundColor: 'rgba(50,205,50,0.15)',
                        borderWidth: 3,
                        borderDash: [8, 4],
                        pointRadius: 6,
                        pointStyle: 'triangle',
                        tension: 0.1,
                        order: 3
                    });
                }
            }

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
            // 5 минут тутамд шинэчлэх
            setInterval(fetchData, 5 * 60 * 1000);
        });
    </script>
@endsection
