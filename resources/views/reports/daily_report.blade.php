@extends('layouts.admin')

@section('style')
    <style>
        .table thead th {
            background-color: #4299e1;
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">Диспетчерийн хоногийн мэдээ</h3>

        <form method="GET" class="mb-4 row g-2 align-items-end">
            <div class="col-auto">
                {{-- <label for="date" class="form-label">Огноо:</label> --}}
                <input type="date" name="date" id="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Харах</button>
            </div>
        </form>

        {{-- Total System --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="overflow-x:auto;">
                    <table class="table table-bordered table-striped table-hover text-center">
                        <thead class="table-primary">
                            <tr>
                                <th></th>
                                <th>Pmax/min (МВт)</th>
                                <th class="text-wrap">Э.боловсруулалт (мян кВт.цаг)</th>
                                <th class="text-wrap">Э.түгээлт (мян кВт.цаг)</th>
                                <th class="text-wrap">Э.импорт (мян кВт.цаг)</th>
                                <th class="text-wrap">Э.экспорт (мян кВт.цаг)</th>
                                <th class="text-wrap">Pимп.max (МВт)</th>
                                <th class="text-wrap">Э.хязгаарлалт (кВт.цаг)</th>
                                <th class="text-wrap">Э.Хөнгөлөлт (кВт.цаг)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Хоногт</td>
                                <td rowspan="2">
                                    {{ number_format($system_data->max_value) }}/{{ number_format($system_data->min_value) }}
                                </td>
                                <td>{{ number_format($journals->first()->total_processed ?? 0, 2) }}</td>
                                <td>{{ number_format($journals->first()->total_distribution ?? 0, 2) }}</td>
                                <td>
                                    {{ number_format($dailyImportExport->total_import ?? 0, 2) }}
                                </td>
                                <td>{{ number_format($dailyImportExport->total_export ?? 0, 2) }}</td>
                                <td rowspan="2">{{ $import_data->max_value }}</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Сарын эхнээс</td>
                                <td>{{ number_format($monthToDate->total_processed ?? 0, 2) }}</td>
                                <td>{{ number_format($monthToDate->total_distribution ?? 0, 2) }}</td>
                                <td>{{ number_format($monthToDateImportExport->total_import ?? 0, 2) }}</td>
                                <td>{{ number_format($monthToDateImportExport->total_export ?? 0, 2) }}</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        {{-- ДЦС --}}

        @php
            function equipmentIcon($type)
            {
                return match ($type) {
                    'Зуух' => asset('images/k.svg'),
                    'Турбогенератор' => asset('images/tg.svg'),
                    'НЦС' => asset('images/solar-power.svg'),
                    'СЦС' => asset('images/wind-power.svg'),
                    'Баттерэй' => asset('images/battery-bolt.svg'),
                    default => asset('images/power-plant.svg'),
                };
            }

            function statusIconsWithLabel($equipments, $statuses, $iconType = null)
            {
                if ($equipments->isEmpty()) {
                    return '<span class="text-muted">—</span>';
                }

                $html = '<div style="display: flex; gap: 5px; flex-wrap: wrap; align-items: flex-start;">';

                foreach ($equipments as $e) {
                    $status = $statuses[$e->id]->status ?? null;
                    if (!$status) {
                        continue;
                    }

                    $iconPath = $iconType ? equipmentIcon($iconType) : asset('images/power-plant.svg');

                    // Төлвөөс хамааруулж өнгө сонгох
                    [$color, $bgColor] = match ($status) {
                        'Ажилд' => ['#dc2626', 'rgba(220, 38, 38, 0.15)'],
                        'Бэлтгэлд' => ['#16a34a', 'rgba(22, 163, 74, 0.15)'],
                        'Засварт' => ['#6b7280', 'rgba(107, 114, 128, 0.15)'],
                        default => ['#9ca3af', 'rgba(156, 163, 175, 0.15)'],
                    };

                    $html .=
                        '<div style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 3px;
                    transition: all 0.3s ease;
                    cursor: pointer;
                "
                onmouseover="this.style.transform=\'translateY(-2px)\';"
                onmouseout="this.style.transform=\'translateY(0)\';"
                title="' .
                        $e->name .
                        ' - ' .
                        $status .
                        '">

                    <div style="
                        position: relative;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        width: 32px;
                        height: 32px;
                        border-radius: 6px;
                        background: ' .
                        $bgColor .
                        ';
                        transition: all 0.3s ease;
                    "

                    onmouseout="this.style.background=\'' .
                        $bgColor .
                        '\';">
                        <img src="' .
                        $iconPath .
                        '"
                             style="width: 16px; height: 16px; transition: all 0.3s ease;"
                             alt="' .
                        $status .
                        '"
                             onmouseover="this.style.transform=\'scale(1.1)\';"
                             onmouseout="this.style.transform=\'scale(1)\';">

                        <!-- Төлвийн индикатор -->
                        <div style="
                            position: absolute;
                            top: -2px;
                            right: -2px;
                            width: 8px;
                            height: 8px;
                            background: ' .
                        $color .
                        ';
                            border: 1.5px solid white;
                            border-radius: 50%;
                            animation: pulse-' .
                        md5($status) .
                        ' 2s ease-in-out infinite;
                        "></div>
                    </div>

                    <div style="
                        font-size: 9px;
                        color: #374151;
                        font-weight: 600;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        max-width: 45px;
                    ">' .
                        $e->name .
                        '</div>
                </div>';
                }

                $html .= '</div>';

                // Animation нэмэх
                $html .=
                    '
        <style>
            @keyframes pulse-' .
                    md5('Ажилд') .
                    ' {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.7; transform: scale(1.1); }
            }
            @keyframes pulse-' .
                    md5('Бэлтгэлд') .
                    ' {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.7; transform: scale(1.1); }
            }
            @keyframes pulse-' .
                    md5('Засварт') .
                    ' {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
        </style>';

                return $html;
            }
        @endphp



        {{-- ДЦС --}}
        <div class="card mt-4">
            <div class="card-body">
                <!-- Гоё legend -->
                <div class="alert alert-light border mb-3 py-2">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <strong class="text-muted">Тоноглолын төлөв:</strong>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(220, 38, 38, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Ажилд</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(22, 163, 74, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Бэлтгэлд</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(107, 114, 128, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Засварт</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Станцууд</th>
                                <th class="text-center">Зуух</th>
                                <th class="text-center">Турбогенератор</th>
                                <th>P (МВт)</th>
                                <th>P max (МВт)</th>
                                <th style="width: 300px;">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($powerPlants as $plant)
                                @php
                                    $boilers = $plant->equipments->where('equipment_type_id', 1);
                                    $turbos = $plant->equipments->where('equipment_type_id', 2);
                                    $statuses = $plant->equipmentStatuses->keyBy('equipment_id');
                                    $info = $plant->powerInfos->first(); // хамгийн сүүлийн PowerInfo
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $plant->name }}</td>

                                    {{-- Зуух --}}
                                    <td>
                                        {!! statusIconsWithLabel($boilers, $statuses, 'Зуух') !!}
                                    </td>

                                    {{-- Турбогенератор --}}
                                    <td>
                                        {!! statusIconsWithLabel($turbos, $statuses, 'Турбогенератор') !!}
                                    </td>

                                    <td>{{ $info?->p }}</td>
                                    <td>{{ $info?->p_max }}</td>
                                    <td>{{ $info?->remark }}</td>

                                    <td>
                                        <a
                                            href="{{ route('daily-equipment-report.create', ['powerPlant' => $plant->id]) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path
                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Мэдээлэл байхгүй</td>
                                </tr>
                            @endforelse

                            <tr class="fw-bold">
                                <td colspan="4">Нийт дүн</td>
                                <td>{{ number_format($total_p, 2) }}</td>
                                <td>{{ number_format($total_pmax, 2) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- СЭХ --}}
        <div class="card mt-4">
            <div class="card-body">
                <!-- Гоё legend -->
                <div class="alert alert-light border mb-3 py-2">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <strong class="text-muted">Тоноглолын төлөв:</strong>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(220, 38, 38, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Ажилд</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(22, 163, 74, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Бэлтгэлд</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(107, 114, 128, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Засварт</span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Станцууд</th>
                                <th class="text-center">Багц</th>
                                <th class="text-center">Инвертер</th>
                                <th>P (МВт)</th>
                                <th>P max (МВт)</th>
                                <th style="width: 300px;">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sunWindPlants as $plant)
                                @php
                                    $batches = $plant->equipments->where('equipment_type_id', 3);
                                    $inverters = $plant->equipments->where('equipment_type_id', 4);
                                    $statuses = $plant->equipmentStatuses->keyBy('equipment_id');
                                    $info = $plant->powerInfos->first();
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $plant->name }}</td>

                                    {{-- Багц --}}
                                    <td>{!! statusIconsWithLabel($batches, $statuses, $plant->powerPlantType?->name) !!}</td>

                                    {{-- Инвертер --}}
                                    <td>{!! statusIconsWithLabel($inverters, $statuses, $plant->type) !!}</td>


                                    <td>{{ $info?->p }}</td>
                                    <td>{{ $info?->p_max }}</td>
                                    <td>{{ $info?->remark }}</td>
                                    <td>
                                        <a
                                            href="{{ route('daily-equipment-report.create', ['powerPlant' => $plant->id]) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path
                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Мэдээлэл байхгүй</td>
                                </tr>
                            @endforelse
                            <tr class="fw-bold">
                                <td colspan="4">Нийт дүн</td>
                                <td>{{ number_format($sun_wind_total_p, 2) }}</td>
                                <td>{{ number_format($sun_wind_total_pmax, 2) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- Battery --}}
        <div class="card mt-4">
            <div class="card-body">
                <!-- Гоё legend -->
                <div class="alert alert-light border mb-3 py-2">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <strong class="text-muted">Тоноглолын төлөв:</strong>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(220, 38, 38, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Ажилд</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(22, 163, 74, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Бэлтгэлд</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                width: 16px;
                height: 16px;
                min-width: 16px;
                border-radius: 4px;
                background: rgba(107, 114, 128, 0.15);
            ">
                            </div>
                            <span class="text-dark" style="font-size: 13px;">Засварт</span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Станцууд</th>
                                <th class="text-center">Багц</th>
                                <th>P (МВт)</th>
                                <th>P max (МВт)</th>
                                <th style="width: 300px;">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($battery_powers as $plant)
                                @php
                                    $batches = $plant->equipments->where('equipment_type_id', 3);
                                    $statuses = $plant->equipmentStatuses->keyBy('equipment_id');
                                    $info = $plant->powerInfos->first();
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $plant->name }}</td>

                                    {{-- Багц --}}
                                    <td>{!! statusIconsWithLabel($batches, $statuses, $plant->powerPlantType?->name) !!}</td>

                                    <td>{{ $info?->p }}</td>
                                    <td>{{ $info?->p_max }}</td>
                                    <td>{{ $info?->remark }}</td>
                                    <td>
                                        <a
                                            href="{{ route('daily-equipment-report.create', ['powerPlant' => $plant->id]) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path
                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Мэдээлэл байхгүй</td>
                                </tr>
                            @endforelse
                            <tr class="fw-bold">
                                <td colspan="3">Нийт дүн</td>
                                <td>{{ number_format($battery_total_p, 2) }}</td>
                                <td>{{ number_format($battery_total_pmax, 2) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- Дулааны мэдээ --}}
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Дулаан дамжуулах сүлжээний усны горим барилтын мэдээ 06:00 цаг</h5>
                <div class="table-responsive">
                    @if ($station_thermo_data)
                        <table class="table table-bordered table-striped">
                            <thead class="text-wrap">
                                <tr>
                                    <th rowspan="2">Станцууд</th>
                                    <th rowspan="2" class="text-wrap">Сүлжээний усны зарцуулалт (Т/ц)</th>
                                    <th rowspan="2" class="text-wrap">Нэмэлт усны зарцуулалт (Т/ц)</th>
                                    <th colspan="2" class="text-wrap">Сүлжээний шууд усны даралт P1 (ата)</th>
                                    <th colspan="2" class="text-wrap">Сүлжээний буцах усны даралт P2 (ата)</th>
                                    <th colspan="2" class="text-wrap">Сүлжээний шууд усны халуун T1 (°C)</th>
                                    <th colspan="2" class="text-wrap">Сүлжээний буцах усны халуун T2 (°C)</th>
                                </tr>
                                <tr>
                                    <th>Байвал зохих</th>
                                    <th>Байгаа нь</th>
                                    <th>Байвал зохих</th>
                                    <th>Байгаа нь</th>
                                    <th>Байвал зохих</th>
                                    <th>Байгаа нь</th>
                                    <th>Байвал зохих</th>
                                    <th>Байгаа нь</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ДЦС-2</td>
                                    <td>{{ $station_thermo_data->pp2g1 }}</td>
                                    <td>{{ $station_thermo_data->pp2gn }}</td>
                                    <td>7</td>
                                    <td>{{ $station_thermo_data->pp2p1 }}</td>
                                    <td>2.5</td>
                                    <td>{{ $station_thermo_data->pp2p2 }}</td>
                                    <td>88</td>
                                    <td>{{ $station_thermo_data->pp2t1 }}</td>
                                    <td>49</td>
                                    <td>{{ $station_thermo_data->pp2t2 }}</td>
                                </tr>
                                <tr>
                                    <td>ДЦС-3 (ДДХ)</td>
                                    <td>{{ $station_thermo_data->pp3lg1 }}</td>
                                    <td>{{ $station_thermo_data->pp3lgn }}</td>
                                    <td>10</td>
                                    <td>{{ $station_thermo_data->pp3lp1 }}</td>
                                    <td>1.8</td>
                                    <td>{{ $station_thermo_data->pp3lp2 }}</td>
                                    <td>88</td>
                                    <td>{{ $station_thermo_data->pp3lt1 }}</td>
                                    <td>49</td>
                                    <td>{{ $station_thermo_data->pp3lt2 }}</td>
                                </tr>
                                <tr>
                                    <td>ДЦС-3 (ӨДХ)</td>
                                    <td>{{ $station_thermo_data->pp3hg1 }}</td>
                                    <td>{{ $station_thermo_data->pp3hgn }}</td>
                                    <td>10</td>
                                    <td>{{ $station_thermo_data->pp3hp1 }}</td>
                                    <td>1.8</td>
                                    <td>{{ $station_thermo_data->pp3hp2 }}</td>
                                    <td>88</td>
                                    <td>{{ $station_thermo_data->pp3ht1 }}</td>
                                    <td>49</td>
                                    <td>{{ $station_thermo_data->pp3ht2 }}</td>
                                </tr>
                                <tr>
                                    <td>ДЦС-4</td>
                                    <td>{{ $station_thermo_data->pp4g }}</td>
                                    <td>{{ $station_thermo_data->pp4gn }}</td>
                                    <td>11.5</td>
                                    <td>{{ $station_thermo_data->pp4p1 }}</td>
                                    <td>2</td>
                                    <td>{{ $station_thermo_data->pp4p2 }}</td>
                                    <td>88</td>
                                    <td>{{ $station_thermo_data->pp4t1 }}</td>
                                    <td>49</td>
                                    <td>{{ $station_thermo_data->pp4t2 }}</td>
                                </tr>
                                <tr>
                                    <td>Амгалан ДС</td>
                                    <td>{{ $station_thermo_data->amg1 }}</td>
                                    <td>{{ $station_thermo_data->amgn }}</td>
                                    <td>7</td>
                                    <td>{{ $station_thermo_data->amp1 }}</td>
                                    <td>2</td>
                                    <td>{{ $station_thermo_data->amp2 }}</td>
                                    <td>88</td>
                                    <td>{{ $station_thermo_data->amt1 }}</td>
                                    <td>49</td>
                                    <td>{{ $station_thermo_data->amt2 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning text-center">
                            Өглөөний 6 цагийн мэдээ олдсонгүй.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Түлшний мэдээ --}}
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="2">Станц</th>
                                <th colspan="3" class="text-center">Вагон буулгалт</th>
                                <th colspan="6" class="text-center">Нүүрс /тонн/</th>
                                <th colspan="3" class="text-center">Мазут /тонн/</th>
                            </tr>
                            <tr>

                                <th>Ирсэн</th>
                                <th>Буусан</th>
                                <th>Үлдсэн</th>

                                <th>Орлого</th>
                                <th>Зарлага</th>
                                <th>Вагоны <br>тоо</th>
                                <th>Үлдэгдэл</th>
                                <th>Хоногийн <br>нөөц</th>
                                <th>Өвлийн их <br>ачааллын<br>нөөц</th>

                                <th>Орлого</th>
                                <th>Зарлага</th>
                                <th>Үлдэгдэл</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($disCoals as $disCoal)
                                <tr>

                                    <td class="text-center">{{ $disCoal->ORG_NAME }}</td>
                                    <td class="text-center">{{ $disCoal->CAME_TRAIN }}</td>
                                    <td class="text-center table-secondary">{{ $disCoal->UNLOADING_TRAIN }}</td>
                                    <td class="text-center">{{ $disCoal->ULDSEIN_TRAIN }}</td>
                                    <td class="text-center table-secondary">{{ $disCoal->COAL_INCOME }}</td>
                                    <td class="text-center table-secondary">{{ $disCoal->COAL_OUTCOME }}</td>
                                    <td class="text-center">{{ $disCoal->COAL_TRAIN_QUANTITY }}</td>
                                    <td class="text-center table-secondary">{{ $disCoal->COAL_REMAIN }}</td>
                                    <td class="text-center table-secondary">{{ $disCoal->COAL_REMAIN_BYDAY }}</td>
                                    <td class="text-center">{{ $disCoal->COAL_REMAIN_BYWINTERDAY }}</td>
                                    <td class="text-center">{{ $disCoal->MAZUT_INCOME }}</td>
                                    <td class="text-center">{{ $disCoal->MAZUT_OUTCOME }}</td>
                                    <td class="text-center table-secondary">{{ $disCoal->MAZUT_REMAIN }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Тасралт --}}
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Цахилгаан, дулаан дамжуулах, түгээх сүлжээнд гарсан тасралт, авсан арга хэмжээ</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">№</th>
                                <th>Огноо</th>
                                <th>Цаг</th>
                                <th>ТЗЭ</th>
                                <th>Тасралт</th>
                                <th>Тайлбар</th>
                                <th>Дутуу түгээсэн ЦЭХ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasralts as $tasralt)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $tasralt->date }}</td>
                                    <td>{{ $tasralt->time }}</td>
                                    <td>{{ $tasralt->TZE }}</td>
                                    <td>{{ $tasralt->tasralt }}</td>
                                    <td>{{ $tasralt->ArgaHemjee }}</td>
                                    <td>{{ $tasralt->HyzErchim }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Захиалгат ажил --}}
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Цахилгаан, дулаан дамжуулах, түгээх сүлжээнд хийгдсэн захиалгат ажил</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>ТЗЭ</th>
                                <th>Засварын ажлын утга</th>
                                <th>Тайлбар</th>
                                <th>Хязгаарласан эрчим хүч</th>
                                <th>Огноо</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($power_distribution_works as $work)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $work->tze }}</td>
                                    <td>{{ $work->repair_work }}</td>
                                    <td>{{ $work->description }}</td>
                                    <td>{{ $work->restricted_energy }}</td>
                                    <td>{{ $work->date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
