@extends('layouts.admin')

@section('title', '19:00 цагийн ачаалал')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock"></i> 19:00 цагийн ачаалал
            </h1>

            <!-- Date Filter -->
            <form method="GET" action="{{ route('zenon.evening-power') }}" class="form-inline">
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

        @if ($eveningData === null)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 19:00 цагийн мэдээлэл олдсонгүй.
            </div>
        @else
            <!-- Summary Card -->
            <div class="row mb-4">
                <div class="col-md-3">
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
                                    {{-- <small class="text-muted">SYSTEM_TOTAL_P</small> --}}
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Нийт ачаалал
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($totalLoad, 2) }} МВт
                                    </div>
                                    <small class="text-muted">Станцуудын нийлбэр</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bolt fa-2x text-gray-300"></i>
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
                                        Идэвхтэй станц
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $activeStations }} / {{ $totalStations }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                        Дундаж ачаалал
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($averageLoad, 2) }} МВт
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                        Хамгийн их
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($maxLoad, 2) }} МВт
                                    </div>
                                    <small class="text-muted">{{ $maxStationName }}</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-crown fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Station Groups Tables -->
            @foreach ($eveningData as $groupKey => $groupData)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i
                                class="fas fa-{{ $groupKey === 'thermal' ? 'fire' : ($groupKey === 'wind' ? 'wind' : ($groupKey === 'solar' ? 'sun' : ($groupKey === 'battery' ? 'battery-full' : 'plug'))) }}"></i>
                            {{ $groupData['name'] }} - 19:00 цаг
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" style="width: 5%;">#</th>
                                        <th style="width: 40%;">Станцын нэр</th>
                                        <th class="text-center" style="width: 20%;">Ачаалал (МВт)</th>
                                        <th class="text-center" style="width: 20%;">Эзлэх хувь (%)</th>
                                        <th class="text-center" style="width: 15%;">Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $groupTotal = array_sum(array_column($groupData['stations'], 'value'));
                                    @endphp
                                    @foreach ($groupData['stations'] as $index => $station)
                                        @php
                                            $percentage = $groupTotal > 0 ? ($station['value'] / $groupTotal) * 100 : 0;
                                            $isActive = $station['value'] > 0;
                                            $isMax = $station['value'] == $maxLoad && $station['value'] > 0;
                                        @endphp
                                        <tr class="{{ $isMax ? 'table-warning' : '' }}">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $station['name'] }}</strong>
                                                @if ($isMax)
                                                    <i class="fas fa-crown text-warning ml-2"
                                                        title="Хамгийн их ачаалал"></i>
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
                                                    <div class="progress-bar {{ $isActive ? 'bg-primary' : 'bg-secondary' }}"
                                                        role="progressbar" style="width: {{ $percentage }}%"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($isActive)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Идэвхтэй
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-times-circle"></i> Унтарсан
                                                    </span>
                                                @endif
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
                                        <td class="text-center">100%</td>
                                        <td class="text-center">
                                            {{ count(array_filter($groupData['stations'], function ($s) {return $s['value'] > 0;})) }}
                                            / {{ count($groupData['stations']) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Хэвлэх
                    </button>
                </div>
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .table-warning {
                background-color: #fff3cd !important;
            }

            .progress {
                box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
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
                    '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%;margin-bottom:20px;}th,td{border:1px solid #ddd;padding:8px;text-align:center;}th{background-color:#343a40;color:white;}.table-info{background-color:#d1ecf1;}.table-warning{background-color:#fff3cd;}</style></head><body>';
                html += '<h2>19:00 цагийн ачаалал - {{ $date }}</h2>';
                html += '<p>Нийт ачаалал: {{ number_format($totalLoad, 2) }} МВт</p>';

                tables.forEach(table => {
                    html += table.outerHTML;
                });

                html += '</body></html>';

                const blob = new Blob(['\ufeff' + html], {
                    type: 'application/vnd.ms-excel'
                });

                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'evening_19_power_{{ $date }}.xls';
                link.click();
            }
        </script>
    @endpush

@endsection
