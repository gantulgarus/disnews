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
                                <th rowspan="2" class="border">Статус</th>
                                <th rowspan="2" class="border">Үйлдэл</th>
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
                                    <td class="border">
                                        @if ($regime->id)
                                            @if ($regime->status == 'approved')
                                                <span class="badge bg-success text-white">Батлагдсан</span>
                                            @elseif($regime->status == 'rejected')
                                                <span class="badge bg-danger text-white">Буцаагдсан</span>
                                            @else
                                                <span class="badge bg-warning text-white">Хүлээгдэж буй</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary text-white">Мэдээлэлгүй</span>
                                        @endif
                                    </td>
                                    <td class="border">
                                        @if ($regime->id)
                                            <div class="btn-group btn-group-sm">
                                                @php
                                                    $isRegimeLead = auth()->user()->permissionLevel?->code === 'REGIME_LEAD';
                                                @endphp
                                                {{-- Зөвхөн REGIME_LEAD батлах/буцаах эрхтэй --}}
                                                @if ($isRegimeLead)
                                                    @if ($regime->status != 'approved')
                                                        <form
                                                            action="{{ route('electric_daily_regimes.approve', $regime->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                title="Батлах">
                                                                <i class="ti ti-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if ($regime->status != 'rejected')
                                                        <form
                                                            action="{{ route('electric_daily_regimes.reject', $regime->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Буцаах">
                                                                <i class="ti ti-x"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                                {{-- Батлагдсан горимыг зөвхөн REGIME_LEAD засах эрхтэй --}}
                                                @if ($regime->status !== 'approved' || $isRegimeLead)
                                                    <a href="{{ route('electric_daily_regimes.edit', $regime->id) }}"
                                                        class="btn btn-primary btn-sm" title="Засах">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
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
        // Бодит гүйцэтгэлийн өгөгдөл
        const actualByPlant = @json($actualByPlant ?? []);

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
            const plantId = regime.power_plant_id || regime.power_plant?.id;

            // 24 цагийн төлөвлөгөөт горим (hour_1 = 00:00, hour_2 = 01:00, ... hour_24 = 23:00)
            const plannedData = Array.from({
                length: 24
            }, (_, i) => parseFloat(regime['hour_' + (i + 1)]) || 0);

            // 24 цагийн бодит гүйцэтгэл (null утгыг хадгалах - өгөгдөл байхгүй гэсэн үг)
            // 0 индекс = 00:00, 23 индекс = 23:00
            const actualDataObj = actualByPlant[plantId] || {};
            const actualData = Array.from({
                length: 24
            }, (_, i) => {
                const val = actualDataObj[i];
                return val !== null && val !== undefined ? parseFloat(val) : null;
            });

            charts[id] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array.from({
                        length: 24
                    }, (_, i) => String(i).padStart(2, '0') + ':00'),
                    datasets: [{
                        label: 'Горим',
                        data: plannedData,
                        borderWidth: 2,
                        tension: 0.3,
                        borderColor: 'rgba(54, 162, 235, 0.8)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: false,
                        pointRadius: 3
                    }, {
                        label: 'Гүйцэтгэл',
                        data: actualData,
                        borderWidth: 2,
                        tension: 0.3,
                        borderColor: 'rgba(255, 99, 132, 0.8)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        fill: false,
                        pointRadius: 3,
                        spanGaps: false
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
                            display: true,
                            position: 'bottom'
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
