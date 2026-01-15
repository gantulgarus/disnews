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
                <span
                    style="width:24px;height:4px;background:#9333ea;display:inline-block;border-radius:2px;opacity:0.7;"></span>
                <span>Системийн нийт (батарей орсон)</span>
            </div>
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
                <span>Цагийн таамаглал</span>
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
                document.getElementById('status').textContent = 'АЛДАА';
                document.getElementById('status').className = 'badge bg-danger';
            }
        }

        function updateChart(data) {
            const datasets = [];

            // 1️⃣ Системийн нийт хэрэглээ (ягаан цэг)
            if (data.actual_data && data.actual_data.length) {
                datasets.push({
                    label: 'Системийн нийт (батарей орсон)',
                    data: data.actual_data.map(d => ({
                        x: new Date(d.time),
                        y: d.system_load // ⚠️ Backend-ээс system_load илгээх хэрэгтэй
                    })),
                    borderColor: '#9333ea',
                    backgroundColor: '#9333ea',
                    borderWidth: 2.5,
                    borderDash: [2, 2],
                    pointRadius: 0,
                    tension: 0.1,
                    order: 5
                });
            }

            // 2️⃣ Бодит хэрэглээ (улаан)
            if (data.actual_data && data.actual_data.length) {
                datasets.push({
                    label: 'Бодит хэрэглээ (батарей хассан)',
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

            // 3️⃣ Өдрийн таамаглал (цэнхэр)
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

            // 4️⃣ Цагийн таамаглал (ногоон)
            if (data.hourly_forecast && data.hourly_forecast.length) {
                datasets.push({
                    label: 'Цагийн таамаглал',
                    data: data.hourly_forecast.map(d => ({
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
