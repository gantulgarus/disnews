@extends('layouts.admin')
@section('title', 'Системийн хэрэглээний таамаглал')

@section('content')
    <div class="container-xl my-4">
        <h1 class="h3 mb-4">Системийн хэрэглээний таамаглал</h1>

        {{-- Info bar --}}
        <div class="row mb-4 gx-2">
            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>Огноо:</div>
                        <input type="date" id="date-picker" class="form-control form-control-sm"
                               style="width: auto;" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>Сүүлийн шинэчлэл:</div>
                        <div class="fw-bold" id="last-update">--:--</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>Статус:</div>
                        <span class="badge bg-success" id="status">LIVE</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body d-flex justify-content-center align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-primary" id="btn-prev-day" title="Өмнөх өдөр">
                            &larr;
                        </button>
                        <button class="btn btn-sm btn-primary" id="btn-today">Өнөөдөр</button>
                        <button class="btn btn-sm btn-outline-primary" id="btn-next-day" title="Дараагийн өдөр">
                            &rarr;
                        </button>
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

        {{-- Модель үнэлгээ --}}
        <div class="row mt-4" id="metrics-section" style="display: none;">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <strong>Өдрийн таамаглал - Модель үнэлгээ</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td>MAE (Дундаж алдаа)</td>
                                    <td class="text-end fw-bold" id="daily-mae">--</td>
                                    <td class="text-muted">МВт</td>
                                </tr>
                                <tr>
                                    <td>RMSE</td>
                                    <td class="text-end fw-bold" id="daily-rmse">--</td>
                                    <td class="text-muted">МВт</td>
                                </tr>
                                <tr>
                                    <td>MAPE</td>
                                    <td class="text-end fw-bold" id="daily-mape">--</td>
                                    <td class="text-muted">%</td>
                                </tr>
                                <tr>
                                    <td>R² (Тодорхойлолт)</td>
                                    <td class="text-end fw-bold" id="daily-r2">--</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <strong>Цагийн таамаглал - Модель үнэлгээ</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td>MAE (Дундаж алдаа)</td>
                                    <td class="text-end fw-bold" id="hourly-mae">--</td>
                                    <td class="text-muted">МВт</td>
                                </tr>
                                <tr>
                                    <td>RMSE</td>
                                    <td class="text-end fw-bold" id="hourly-rmse">--</td>
                                    <td class="text-muted">МВт</td>
                                </tr>
                                <tr>
                                    <td>MAPE</td>
                                    <td class="text-end fw-bold" id="hourly-mape">--</td>
                                    <td class="text-muted">%</td>
                                </tr>
                                <tr>
                                    <td>R² (Тодорхойлолт)</td>
                                    <td class="text-end fw-bold" id="hourly-r2">--</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-2">
                <div class="text-muted small text-center">
                    <span>Сургалтын дата: <strong id="training-size">--</strong></span>
                    <span class="mx-2">|</span>
                    <span>Тест дата: <strong id="test-size">--</strong></span>
                    <span class="mx-2">|</span>
                    <span>Шинэчлэгдсэн: <strong id="metrics-updated">--</strong></span>
                </div>
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
        let currentDate = new Date();
        let autoRefreshInterval = null;

        function getToday() {
            const now = new Date();
            return now.toISOString().split('T')[0];
        }

        function isToday(dateStr) {
            return dateStr === getToday();
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function initChart(selectedDate) {
            const ctx = document.getElementById('forecastChart').getContext('2d');
            const date = new Date(selectedDate);

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

        function updateChartTimeRange(selectedDate) {
            const date = new Date(selectedDate);
            const startTime = new Date(date);
            startTime.setHours(1, 0, 0, 0);

            const endTime = new Date(date);
            endTime.setDate(endTime.getDate() + 1);
            endTime.setHours(0, 0, 0, 0);

            chart.options.scales.x.min = startTime;
            chart.options.scales.x.max = endTime;
        }

        async function fetchTodayData() {
            try {
                const res = await fetch('/api/forecast/today');
                const result = await res.json();
                if (result.success) {
                    updateChartWithTodayData(result.data);
                    updateInfo();
                    updateStatus(true);
                }
            } catch (e) {
                console.error('Өгөгдөл татахад алдаа:', e);
                updateStatus(false);
            }
        }

        async function fetchHistoryData(date) {
            try {
                const res = await fetch(`/api/forecast/history?date=${date}`);
                const result = await res.json();
                if (result.success) {
                    updateChartWithHistoryData(result.data);
                    updateInfo();
                    updateStatus(true, true);
                }
            } catch (e) {
                console.error('Түүхэн өгөгдөл татахад алдаа:', e);
                updateStatus(false);
            }
        }

        function updateChartWithTodayData(data) {
            const datasets = [];

            // Системийн нийт хэрэглээ (ягаан)
            if (data.actual_data && data.actual_data.length) {
                datasets.push({
                    label: 'Системийн нийт (батарей орсон)',
                    data: data.actual_data.map(d => ({
                        x: new Date(d.time),
                        y: d.system_load
                    })),
                    borderColor: '#9333ea',
                    backgroundColor: '#9333ea',
                    borderWidth: 2.5,
                    borderDash: [2, 2],
                    pointRadius: 0,
                    tension: 0.4,
                    order: 5
                });
            }

            // Бодит хэрэглээ (улаан)
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
                    tension: 0.4,
                    order: 1
                });
            }

            // Өдрийн таамаглал (цэнхэр)
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
                    tension: 0.4,
                    order: 4
                });
            }

            // Цагийн таамаглал (ногоон)
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
                    tension: 0.4,
                    order: 2
                });
            }

            updateChartTimeRange(getToday());
            chart.data.datasets = datasets;
            chart.update('none');
        }

        function updateChartWithHistoryData(data) {
            const datasets = [];
            const history = data.history;

            if (history && history.length) {
                // Системийн нийт хэрэглээ (ягаан)
                datasets.push({
                    label: 'Системийн нийт (батарей орсон)',
                    data: history.filter(d => d.system_load !== null).map(d => ({
                        x: new Date(d.time),
                        y: d.system_load
                    })),
                    borderColor: '#9333ea',
                    backgroundColor: '#9333ea',
                    borderWidth: 2.5,
                    borderDash: [2, 2],
                    pointRadius: 0,
                    tension: 0.4,
                    order: 5
                });

                // Бодит хэрэглээ (улаан)
                datasets.push({
                    label: 'Бодит хэрэглээ (батарей хассан)',
                    data: history.filter(d => d.actual_load !== null).map(d => ({
                        x: new Date(d.time),
                        y: d.actual_load
                    })),
                    borderColor: '#ef4444',
                    backgroundColor: '#ef4444',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    order: 1
                });

                // Өдрийн таамаглал (цэнхэр)
                datasets.push({
                    label: 'Өдрийн таамаглал',
                    data: history.filter(d => d.daily_forecast !== null).map(d => ({
                        x: new Date(d.time),
                        y: d.daily_forecast
                    })),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 3,
                    tension: 0.4,
                    order: 4
                });

                // Цагийн таамаглал (ногоон)
                datasets.push({
                    label: 'Цагийн таамаглал',
                    data: history.filter(d => d.hourly_forecast !== null).map(d => ({
                        x: new Date(d.time),
                        y: d.hourly_forecast
                    })),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    tension: 0.4,
                    order: 2
                });
            }

            updateChartTimeRange(data.date);
            chart.data.datasets = datasets;
            chart.update('none');
        }

        function updateInfo() {
            const now = new Date();
            document.getElementById('last-update').textContent =
                now.toLocaleTimeString('mn-MN', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }

        function updateStatus(success, isHistory = false) {
            const statusEl = document.getElementById('status');
            if (success) {
                if (isHistory) {
                    statusEl.textContent = 'ТҮҮХ';
                    statusEl.className = 'badge bg-secondary';
                } else {
                    statusEl.textContent = 'LIVE';
                    statusEl.className = 'badge bg-success';
                }
            } else {
                statusEl.textContent = 'АЛДАА';
                statusEl.className = 'badge bg-danger';
            }
        }

        async function fetchMetrics() {
            try {
                const res = await fetch('/api/forecast/metrics');
                const result = await res.json();
                if (result.success && result.data) {
                    updateMetricsDisplay(result.data);
                }
            } catch (e) {
                console.error('Metrics татахад алдаа:', e);
            }
        }

        function updateMetricsDisplay(data) {
            const metricsSection = document.getElementById('metrics-section');
            metricsSection.style.display = 'flex';

            // Өдрийн таамаглал
            if (data.daily) {
                document.getElementById('daily-mae').textContent = data.daily.mae?.toLocaleString() || '--';
                document.getElementById('daily-rmse').textContent = data.daily.rmse?.toLocaleString() || '--';
                document.getElementById('daily-mape').textContent = data.daily.mape?.toFixed(2) || '--';
                document.getElementById('daily-r2').textContent = data.daily.r2?.toFixed(4) || '--';
            }

            // Цагийн таамаглал
            if (data.hourly) {
                document.getElementById('hourly-mae').textContent = data.hourly.mae?.toLocaleString() || '--';
                document.getElementById('hourly-rmse').textContent = data.hourly.rmse?.toLocaleString() || '--';
                document.getElementById('hourly-mape').textContent = data.hourly.mape?.toFixed(2) || '--';
                document.getElementById('hourly-r2').textContent = data.hourly.r2?.toFixed(4) || '--';
            }

            // Нэмэлт мэдээлэл
            if (data.training_size) {
                document.getElementById('training-size').textContent = data.training_size.toLocaleString();
            }
            if (data.test_size) {
                document.getElementById('test-size').textContent = data.test_size.toLocaleString();
            }
            if (data.updated_at) {
                const date = new Date(data.updated_at);
                document.getElementById('metrics-updated').textContent = date.toLocaleString('mn-MN');
            }
        }

        function loadDataForDate(dateStr) {
            document.getElementById('date-picker').value = dateStr;

            // Auto refresh зогсоох/эхлүүлэх
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }

            if (isToday(dateStr)) {
                fetchTodayData();
                autoRefreshInterval = setInterval(fetchTodayData, 5 * 60 * 1000);
            } else {
                fetchHistoryData(dateStr);
            }
        }

        function changeDate(days) {
            const picker = document.getElementById('date-picker');
            const current = new Date(picker.value);
            current.setDate(current.getDate() + days);
            loadDataForDate(formatDate(current));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const today = getToday();
            initChart(today);

            // Огноо сонгогчийн event
            document.getElementById('date-picker').addEventListener('change', function(e) {
                loadDataForDate(e.target.value);
            });

            // Товчнууд
            document.getElementById('btn-prev-day').addEventListener('click', () => changeDate(-1));
            document.getElementById('btn-next-day').addEventListener('click', () => changeDate(1));
            document.getElementById('btn-today').addEventListener('click', () => loadDataForDate(getToday()));

            // Анхны өгөгдөл татах
            loadDataForDate(today);

            // Модель үнэлгээ татах
            fetchMetrics();
        });
    </script>
@endsection
