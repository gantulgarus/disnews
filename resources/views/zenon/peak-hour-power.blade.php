@extends('layouts.admin')

@section('title', 'Системийн оргил цагийн ачаалал')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-crown text-warning"></i> Системийн оргил цагийн ачаалал
            </h1>

            <!-- Date Filter -->
            <form method="GET" action="{{ route('zenon.peak-hour-power') }}" class="form-inline">
                <div class="input-group">
                    <input type="date" name="date" class="form-control" value="{{ $date }}"
                        max="{{ now()->format('Y-m-d') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Харах
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($peakHourData === null || $systemPeakInfo === null)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Системийн оргил цагийн мэдээлэл олдсонгүй.
            </div>
        @else
            <!-- Peak Hour Info Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-warning shadow" style="border-left: 5px solid #ffc107;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bolt fa-3x text-warning mr-3"></i>
                            <div>
                                <h4 class="alert-heading mb-2">
                                    <i class="fas fa-crown"></i>
                                    Системийн оргил ачаалал:
                                    <strong>{{ $peakHour }}:00 цагт</strong>
                                </h4>
                                <p class="mb-0">
                                    Өдрийн хамгийн их ачаалал
                                    <strong class="text-danger">{{ number_format($systemTotal, 2) }} МВт</strong>
                                    байлаа.
                                    @if (count($systemPeakInfo['peak_hours']) > 1)
                                        <small class="text-muted">
                                            (Мөн:
                                            {{ implode(', ',array_map(function ($h) {return $h + 1 . ':00';}, array_slice($systemPeakInfo['peak_hours'], 1))) }})
                                        </small>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards by Station Type -->
            @php
                // Бүлэг бүрийн нийлбэрийг тооцоолох
                $groupTotals = [];
                foreach ($peakHourData as $groupKey => $groupData) {
                    $groupTotal = array_sum(array_column($groupData['stations'], 'value'));
                    $groupTotals[$groupKey] = $groupTotal;
                }
            @endphp

            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Системийн нийт
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($systemTotal, 2) }} МВт
                                    </div>
                                    <small class="text-muted">{{ $peakHour }}:00 цагт</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-crown fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        ДЦС нийлбэр
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($groupTotals['thermal'] ?? 0, 2) }} МВт
                                    </div>
                                    <small class="text-muted">Дулааны цахилгаан станц</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-fire fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        СЦС нийлбэр
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($groupTotals['wind'] ?? 0, 2) }} МВт
                                    </div>
                                    <small class="text-muted">Салхины станц</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wind fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        НЦС нийлбэр
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($groupTotals['solar'] ?? 0, 2) }} МВт
                                    </div>
                                    <small class="text-muted">Нарны станц</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-sun fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        БХ нийлбэр
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($groupTotals['battery'] ?? 0, 2) }} МВт
                                    </div>
                                    <small class="text-muted">Батарей</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-battery-full fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card border-left-secondary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        Импорт
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($groupTotals['import'] ?? 0, 2) }} МВт
                                    </div>
                                    <small class="text-muted">ОХУ</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-plug fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Station Groups Tables -->
            @php
                // Импортгүй нийт тооцоолох (хувь тооцоход ашиглана)
                $totalWithoutImport =
                    ($groupTotals['thermal'] ?? 0) +
                    ($groupTotals['wind'] ?? 0) +
                    ($groupTotals['solar'] ?? 0) +
                    ($groupTotals['battery'] ?? 0);
            @endphp

            @foreach ($peakHourData as $groupKey => $groupData)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-warning">
                        <h6 class="m-0 font-weight-bold text-dark">
                            <i
                                class="fas fa-{{ $groupKey === 'thermal' ? 'fire' : ($groupKey === 'wind' ? 'wind' : ($groupKey === 'solar' ? 'sun' : ($groupKey === 'battery' ? 'battery-full' : 'plug'))) }}"></i>
                            {{ $groupData['name'] }} - {{ $peakHour }}:00 цаг (ОРГИЛ ҮЕ)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" style="width: 10%;">#</th>
                                        <th style="width: 50%;">Станцын нэр</th>
                                        <th class="text-center" style="width: 20%;">Ачаалал (МВт)</th>
                                        <th class="text-center" style="width: 20%;">
                                            @if ($groupKey === 'import')
                                                Эзлэх хувь (%)
                                            @else
                                                Үйлдвэрлэлийн хувь (%)
                                            @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $groupTotal = array_sum(array_column($groupData['stations'], 'value'));
                                    @endphp
                                    @foreach ($groupData['stations'] as $index => $station)
                                        @php
                                            $isActive = $station['value'] > 0;

                                            // Хувь тооцоолох: Импорт бол системд эзлэх хувь, бусад бол үйлдвэрлэлд эзлэх хувь
                                            if ($groupKey === 'import') {
                                                $percentage =
                                                    $systemTotal > 0 ? ($station['value'] / $systemTotal) * 100 : 0;
                                            } else {
                                                $percentage =
                                                    $totalWithoutImport > 0
                                                        ? ($station['value'] / $totalWithoutImport) * 100
                                                        : 0;
                                            }
                                        @endphp
                                        <tr class="{{ $isActive ? '' : 'table-secondary' }}">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $station['name'] }}</strong>
                                                @if (!$isActive)
                                                    <span class="badge badge-secondary ml-2">Унтарсан</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-{{ $isActive ? 'success' : 'secondary' }} badge-pill"
                                                    style="font-size: 14px; padding: 8px 12px;">
                                                    {{ number_format($station['value'], 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar {{ $isActive ? ($groupKey === 'import' ? 'bg-secondary' : 'bg-warning') : 'bg-secondary' }}"
                                                        role="progressbar" style="width: {{ $percentage }}%"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        {{ number_format($percentage, 2) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <!-- Group Total -->
                                    <tr class="table-info font-weight-bold">
                                        <td colspan="2" class="text-right">Бүлгийн нийт:</td>
                                        <td class="text-center">
                                            <span class="badge badge-info badge-pill"
                                                style="font-size: 14px; padding: 8px 12px;">
                                                {{ number_format($groupTotal, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                if ($groupKey === 'import') {
                                                    $groupPercentage =
                                                        $systemTotal > 0 ? ($groupTotal / $systemTotal) * 100 : 0;
                                                } else {
                                                    $groupPercentage =
                                                        $totalWithoutImport > 0
                                                            ? ($groupTotal / $totalWithoutImport) * 100
                                                            : 0;
                                                }
                                            @endphp
                                            {{ number_format($groupPercentage, 2) }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Summary Info -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Тайлбар:</strong> Хувь тооцоолох: Үйлдвэрлэлийн станцууд (ДЦС, СЦС, НЦС, БХ) нь нийт
                        үйлдвэрлэлийн
                        <strong>{{ number_format($totalWithoutImport, 2) }} МВт</strong>-ийг 100% гэж тооцсон.
                        Импорт нь системийн нийт ачааллын <strong>{{ number_format($systemTotal, 2) }} МВт</strong>-д эзлэх
                        хувийг харуулна.
                    </div>
                </div>
            </div>

            <!-- Export Button -->
            <div class="row mb-4">
                <div class="col-12 text-right">
                    <button class="btn btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Excel татах
                    </button>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Хэвлэх
                    </button>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .progress {
                box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
            }

            .alert-warning {
                border-radius: 8px;
            }

            .bg-warning {
                background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
            }

            .table-secondary {
                background-color: #e9ecef !important;
                opacity: 0.7;
            }

            @media print {

                .btn,
                .input-group,
                form {
                    display: none !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function exportToExcel() {
                const tables = document.querySelectorAll('.table');
                let html =
                    '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%;margin-bottom:20px;}th,td{border:1px solid #ddd;padding:8px;text-align:center;}th{background-color:#343a40;color:white;}.table-info{background-color:#d1ecf1;}</style></head><body>';
                html += '<h2>Системийн оргил цагийн ачаалал - {{ $date }}</h2>';
                html += '<p>Оргил цаг: {{ $peakHour }}:00</p>';
                html += '<p>Системийн нийт: {{ number_format($systemTotal, 2) }} МВт</p>';
                html += '<p>Үйлдвэрлэлийн нийт: {{ number_format($totalWithoutImport, 2) }} МВт</p>';

                tables.forEach(table => {
                    html += table.outerHTML;
                });

                html += '</body></html>';

                const blob = new Blob(['\ufeff' + html], {
                    type: 'application/vnd.ms-excel'
                });

                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'peak_hour_power_{{ $date }}.xls';
                link.click();
            }
        </script>
    @endpush

@endsection
