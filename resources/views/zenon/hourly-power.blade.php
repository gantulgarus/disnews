@extends('layouts.admin')

@section('title', 'Цаг тутмын чадал')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Цаг тутмын чадал</h1>

            <!-- Date Filter -->
            <form method="GET" action="{{ route('zenon.hourly-power') }}" class="form-inline">
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

        @if ($hourlyData === null)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Мэдээлэл олдсонгүй.
            </div>
        @else
            <!-- System Summary (if viewing all stations) -->
            @if ($isSystemView)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3 bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">Системийн нийт үзүүлэлт</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    @php
                                        $systemTotal = 0;
                                        $systemAverage = 0;
                                        $stationCount = 0;
                                        foreach ($hourlyData as $groupData) {
                                            foreach ($groupData['stations'] as $station) {
                                                $systemTotal += $station['total'];
                                                $systemAverage += $station['average'];
                                                $stationCount++;
                                            }
                                        }
                                        $systemAverage = $stationCount > 0 ? $systemAverage / $stationCount : 0;
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="border-right">
                                            <h4 class="text-primary">{{ number_format($systemTotal, 2) }} МВт·ц</h4>
                                            <small class="text-muted">Нийт үйлдвэрлэл</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="text-success">{{ number_format($systemAverage, 2) }} МВт</h4>
                                        <small class="text-muted">Дундаж чадал</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Station Groups -->
            @foreach ($hourlyData as $groupKey => $groupData)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i
                                class="fas fa-{{ $groupKey === 'thermal' ? 'fire' : ($groupKey === 'wind' ? 'wind' : ($groupKey === 'solar' ? 'sun' : ($groupKey === 'battery' ? 'battery-full' : 'plug'))) }}"></i>
                            {{ $groupData['name'] }}
                        </h6>
                        <span class="badge badge-primary badge-pill">
                            {{ count($groupData['stations']) }} станц
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="min-width: 150px;">Станц</th>
                                        @for ($hour = 1; $hour <= 24; $hour++)
                                            <th class="text-center" style="min-width: 60px;">{{ $hour }}:00</th>
                                        @endfor
                                        <th class="text-center bg-light" style="min-width: 80px;">Нийт</th>
                                        <th class="text-center bg-light" style="min-width: 80px;">Дундаж</th>
                                        <th class="text-center bg-warning" style="min-width: 80px;">Оргил</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupData['stations'] as $station)
                                        @php
                                            // Оргил ачааллыг олох
                                            $peakValue = max(
                                                array_filter($station['hourly_data'], function ($v) {
                                                    return $v !== null;
                                                }),
                                            );
                                            $peakHours = [];
                                            foreach ($station['hourly_data'] as $hour => $value) {
                                                if ($value !== null && $value == $peakValue) {
                                                    $peakHours[] = $hour;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold">{{ $station['name'] }}</td>
                                            @foreach ($station['hourly_data'] as $hourIndex => $value)
                                                @php
                                                    $isPeak = $value !== null && $value == $peakValue && $value > 0;
                                                    $cellClass = 'text-right ';
                                                    if ($isPeak) {
                                                        $cellClass .= 'bg-warning font-weight-bold';
                                                    } elseif ($value !== null && $value > 0) {
                                                        $cellClass .= 'text-success';
                                                    } else {
                                                        $cellClass .= 'text-muted';
                                                    }
                                                @endphp
                                                <td class="{{ $cellClass }}"
                                                    title="{{ $isPeak ? 'Оргил ачаалал' : '' }}">
                                                    {{ $value !== null ? number_format($value, 2) : '-' }}
                                                    @if ($isPeak)
                                                        <i class="fas fa-crown text-danger ml-1"
                                                            style="font-size: 10px;"></i>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-right font-weight-bold bg-light">
                                                {{ number_format($station['total'], 2) }}
                                            </td>
                                            <td class="text-right font-weight-bold bg-light">
                                                {{ number_format($station['average'], 2) }}
                                            </td>
                                            <td class="text-right font-weight-bold bg-warning">
                                                {{ number_format($peakValue, 2) }}
                                                <small class="d-block text-muted" style="font-size: 10px;">
                                                    {{ implode(', ',array_map(function ($h) {return $h + 1 . ':00';}, $peakHours)) }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Group Total Row -->
                                    @php
                                        $groupHourlyTotals = array_fill(0, 24, 0);
                                        $groupTotal = 0;
                                        foreach ($groupData['stations'] as $station) {
                                            foreach ($station['hourly_data'] as $hour => $value) {
                                                if ($value !== null) {
                                                    $groupHourlyTotals[$hour] += $value;
                                                }
                                            }
                                            $groupTotal += $station['total'];
                                        }
                                        $groupAverage = count($groupData['stations']) > 0 ? $groupTotal / 24 : 0;
                                        $groupPeakValue = max($groupHourlyTotals);
                                        $groupPeakHours = [];
                                        foreach ($groupHourlyTotals as $hour => $value) {
                                            if ($value == $groupPeakValue && $value > 0) {
                                                $groupPeakHours[] = $hour;
                                            }
                                        }
                                    @endphp
                                    <tr class="table-info font-weight-bold">
                                        <td>Нийт</td>
                                        @foreach ($groupHourlyTotals as $hourIndex => $hourTotal)
                                            @php
                                                $isGroupPeak = $hourTotal == $groupPeakValue && $hourTotal > 0;
                                            @endphp
                                            <td class="text-right {{ $isGroupPeak ? 'bg-warning' : '' }}">
                                                {{ number_format($hourTotal, 2) }}
                                                @if ($isGroupPeak)
                                                    <i class="fas fa-crown text-danger ml-1" style="font-size: 10px;"></i>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-right bg-info text-white">
                                            {{ number_format($groupTotal, 2) }}
                                        </td>
                                        <td class="text-right bg-info text-white">
                                            {{ number_format($groupAverage, 2) }}
                                        </td>
                                        <td class="text-right bg-warning">
                                            {{ number_format($groupPeakValue, 2) }}
                                            <small class="d-block text-dark" style="font-size: 10px;">
                                                {{ implode(', ',array_map(function ($h) {return $h + 1 . ':00';}, $groupPeakHours)) }}
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Peak Load Legend -->
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-crown text-danger"></i> Оргил ачаалал -
                                <span class="badge badge-warning">Шар өнгөөр</span> тэмдэглэгдсэн
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Export Button -->
            <div class="row mb-4">
                <div class="col-12 text-right">
                    <button class="btn btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Excel татах
                    </button>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .bg-warning {
                background-color: #ffc107 !important;
            }

            .table td.bg-warning {
                position: relative;
            }

            .table td.bg-warning::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.3) 0%, rgba(255, 193, 7, 0.1) 100%);
                pointer-events: none;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function exportToExcel() {
                const tables = document.querySelectorAll('.table');
                let html =
                    '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;text-align:right;}th{background-color:#4e73df;color:white;}.bg-warning{background-color:#ffc107;}.bg-info{background-color:#17a2b8;color:white;}.text-white{color:white;}</style></head><body>';
                html += '<h2>Цаг тутмын чадал - {{ $date }}</h2>';

                tables.forEach(table => {
                    html += table.outerHTML;
                    html += '<br><br>';
                });

                html += '</body></html>';

                const blob = new Blob(['\ufeff' + html], {
                    type: 'application/vnd.ms-excel'
                });

                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'hourly_power_{{ $date }}.xls';
                link.click();
            }
        </script>
    @endpush

@endsection
