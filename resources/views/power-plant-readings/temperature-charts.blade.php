@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-2">
        <div class="page-header d-print-none mb-2">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title mb-0">Температурын график</h2>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-3">
                <!-- Огноо сонгох -->
                <form method="GET" action="{{ route('power-plant-readings.temperature-charts') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">Огноо сонгох</label>
                            <input type="date" name="date" id="date" value="{{ $date }}"
                                max="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">Харуулах</button>
                        </div>
                        <div class="col-auto">
                            <button type="button"
                                onclick="window.location.href='{{ route('power-plant-readings.temperature-charts', ['date' => now()->format('Y-m-d')]) }}'"
                                class="btn btn-secondary btn-sm">
                                Өнөөдөр
                            </button>
                        </div>
                    </div>
                </form>

                @if (!empty($chartData))
                    <div class="row">
                        @foreach ($chartData as $slug => $data)
                            <div class="col-lg-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">{{ $data['plant_name'] }}</div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="chart-{{ $slug }}"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty py-5">
                        <p class="empty-title">Мэдээлэл олдсонгүй</p>
                        <p class="empty-subtitle text-muted">{{ $date }} өдрийн температурын мэдээлэл байхгүй байна.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Chart.js-г зөв load хийсэн эсэхийг шалгана -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData ?? []);

            // Хэрэв chartData хоосон бол дуусга
            if (Object.keys(chartData).length === 0) {
                return;
            }

            const colors = {
                t1: {
                    border: '#206bc4',
                    background: 'rgba(32, 107, 196, 0.1)'
                },
                t2: {
                    border: '#d63939',
                    background: 'rgba(214, 57, 57, 0.1)'
                }
            };

            Object.keys(chartData).forEach(slug => {
                const ctx = document.getElementById(`chart-${slug}`);
                if (!ctx) {
                    console.error(`Canvas элемент олдсонгүй: chart-${slug}`);
                    return;
                }

                const plant = chartData[slug];

                // Хэрэв өгөгдөл хоосон бол график үүсгэхгүй
                const hasData = (plant.t1_data && plant.t1_data.some(v => v !== null)) ||
                    (plant.t2_data && plant.t2_data.some(v => v !== null));

                if (!hasData) {
                    console.warn(`${plant.plant_name}-д өгөгдөл байхгүй`);
                    return;
                }

                const datasets = [];

                if (plant.t1_data && plant.t1_data.some(v => v !== null)) {
                    datasets.push({
                        label: plant.t1_label,
                        data: plant.t1_data,
                        borderColor: colors.t1.border,
                        backgroundColor: colors.t1.background,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    });
                }

                if (plant.t2_data && plant.t2_data.some(v => v !== null)) {
                    datasets.push({
                        label: plant.t2_label,
                        data: plant.t2_data,
                        borderColor: colors.t2.border,
                        backgroundColor: colors.t2.background,
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    });
                }

                try {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: plant.hours,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // Энэ нь чухал!
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Цаг'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Температур (°C)'
                                    },
                                    beginAtZero: false
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error(`График үүсгэхэд алдаа гарлаа (${plant.plant_name}):`, error);
                }
            });
        });
    </script>

    <style>
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }

        @media print {

            .page-header,
            form {
                display: none !important;
            }

            .col-lg-6 {
                width: 50%;
                float: left;
            }

            canvas {
                max-height: 200px !important;
            }
        }
    </style>
@endsection
