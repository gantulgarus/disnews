@extends('layouts.admin')

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Цахилгаан горимын өдөр тутмын тайлан</h2>
                    </div>
                </div>
            </div>

            <!-- Filter form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('electric_daily_regimes.report') }}" method="GET"
                        class="row g-3 align-items-center">
                        <div class="col-auto">
                            <input type="date" name="date" value="{{ $date }}" class="form-control">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Хайх</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report table -->
            <div class="card mb-4">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover border small-table">
                        <thead class="table-light text-center align-middle border">
                            <tr>
                                <th rowspan="2" class="border">Станц</th>
                                <th colspan="2" class="border">Техникийн max, min</th>
                                <th colspan="2" class="border">Горимоор өгсөн</th>
                                @for ($i = 1; $i <= 24; $i++)
                                    <th rowspan="2" class="border">{{ $i }}</th>
                                @endfor
                                <th rowspan="2" class="border">Нийт</th>
                            </tr>
                            <tr>
                                <th class="border">TPmax</th>
                                <th class="border">TPmin</th>
                                <th class="border">Pmax</th>
                                <th class="border">Pmin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($regimes as $regime)
                                <tr>
                                    <td class="border">{{ $regime->powerPlant->name }}</td>
                                    <td class="border">{{ number_format($regime->technical_pmax ?? 0, 0) }}</td>
                                    <td class="border">{{ number_format($regime->technical_pmin ?? 0, 0) }}</td>
                                    <td class="border">{{ number_format($regime->pmax ?? 0, 0) }}</td>
                                    <td class="border">{{ number_format($regime->pmin ?? 0, 0) }}</td>
                                    @for ($i = 1; $i <= 24; $i++)
                                        <td class="border">{{ number_format($regime->{'hour_' . $i} ?? 0, 0) }}</td>
                                    @endfor
                                    <td class="border">
                                        {{ $regime->total ?? array_sum(array_map(fn($h) => $regime->{'hour_' . $h} ?? 0, range(1, 24))) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Chart section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Станц тус бүрийн 24 цагийн ачааллын график</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($regimes as $index => $regime)
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-header text-center fw-bold">
                                        {{ $regime->powerPlant->name }}
                                    </div>
                                    <div class="card-body">
                                        <canvas id="chart-{{ $index }}" height="180"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Laravel-аас Regime өгөгдлийг JSON хэлбэрээр авна
        const regimes = @json($regimes);

        const charts = {};

        document.querySelectorAll('canvas[id^="chart-"]').forEach((canvas) => {
            const ctx = canvas.getContext('2d');
            const id = canvas.id;

            // Хэрэв өмнө нь chart байгаа бол устгана
            if (Chart.getChart(id)) {
                Chart.getChart(id).destroy();
            }

            // ID-аас индексийг гаргаж авна
            const index = id.replace('chart-', '');
            const regime = regimes[index];

            // 24 цагийн өгөгдөл
            const data = Array.from({
                length: 24
            }, (_, i) => parseFloat(regime['hour_' + (i + 1)]) || 0);

            charts[id] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array.from({
                        length: 24
                    }, (_, i) => (i + 1) + ':00'),
                    datasets: [{
                        label: regime.power_plant?.name || 'Тодорхойгүй',
                        data,
                        borderWidth: 2,
                        tension: 0.3,
                        borderColor: 'rgba(54, 162, 235, 0.8)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: `${regime.power_plant?.name || 'Тодорхойгүй'} станцын 24 цагийн ачаалал`
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Ачаалал (МВт)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Цаг'
                            }
                        }
                    }
                }
            });
        });
    </script>
    <style>
        .small-table th,
        .small-table td {
            font-size: 10px !important;
            padding: 2px 4px !important;
            white-space: nowrap;
            text-align: center;
        }
    </style>
@endsection
