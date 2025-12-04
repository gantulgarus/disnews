@extends('layouts.admin')

@section('style')
    <style>
        .table thead th {
            background-color: #4299e1;
            /* Tabler primary blue */
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">Орон нутгийн хоногийн мэдээ</h3>

        <form method="GET" class="mb-4 row g-2 align-items-end">
            <div class="col-auto">
                {{-- <label for="date" class="form-label">Огноо:</label> --}}
                <input type="date" name="date" id="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Харах</button>
            </div>
        </form>

        <h3 class="my-2">Баруун бүсийн эрчим хүчний систем</h3>

        {{-- Total System --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="overflow-x:auto;">
                    <table class="table table-bordered table-striped table-hover text-center">
                        <thead class="table-primary">
                            <tr>
                                <th></th>
                                <th>Pmax/min (МВт)</th>
                                <th class="text-wrap">Хэрэглээ (мян кВт.цаг)</th>
                                <th class="text-wrap">Түгээлт (мян кВт.цаг)</th>
                                <th class="text-wrap">Pимп.max (МВт)</th>
                                <th class="text-wrap">Pимп.min (МВт)</th>
                                <th class="text-wrap">Импортоор авсан ЦЭХ (мян кВт.цаг)</th>
                                <th class="text-wrap">Импортоор түгээсэн ЦЭХ (мян кВт.цаг)</th>
                                <th class="text-wrap"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($westernRegionCapacities as $data)
                                <tr>
                                    <td>Хоногт</td>
                                    <td>{{ $data->p_max }} / {{ $data->p_min }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $data->p_imp_max }}</td>
                                    <td>{{ $data->p_imp_min }}</td>
                                    <td>{{ $data->import_received }}</td>
                                    <td>{{ $data->import_distributed }}</td>
                                    <td>
                                        <a href="{{ route('western_region_capacities.create') }}">
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
                            @endforeach
                            <tr>
                                <td>Сарын эхнээс</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @php
            function equipmentIcon($type)
            {
                return match ($type) {
                    'Зуух' => asset('images/k.svg'),
                    'Турбогенератор' => asset('images/tg.svg'),
                    'НЦС' => asset('images/solar-power.svg'),
                    'СЦС' => asset('images/wind-power.svg'),
                    'УЦС' => asset('images/hydro-power.svg'),
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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Станцууд</th>
                                <th class="text-center">Гидрогенератор</th>
                                <th class="text-center">Багц</th>
                                <th>P (МВт)</th>
                                <th>P max (МВт)</th>
                                <th class="text-wrap">Усны түвшин (м)</th>
                                <th class="text-wrap">Үйлдвэрлэсэн ЦЭХ (мян.кВт.цаг)</th>
                                <th class="text-wrap">Түгээсэн ЦЭХ (мян.кВт.цаг)</th>
                                <th class="text-wrap">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($powerPlants as $plant)
                                @php
                                    $hidro = $plant->equipments->where('equipment_type_id', 5);
                                    $batches = $plant->equipments->where('equipment_type_id', 3);
                                    $statuses = $plant->equipmentStatuses->keyBy('equipment_id');
                                    $info = $plant->powerInfos->first();
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $plant->name }}</td>

                                    {{-- Гидрогенератор --}}
                                    <td>{!! statusIconsWithLabel($hidro, $statuses, $plant->powerPlantType?->name) !!}</td>

                                    {{-- Багц --}}
                                    <td>{!! statusIconsWithLabel($batches, $statuses, $plant->powerPlantType?->name) !!}</td>

                                    <td>{{ $info?->p }}</td>
                                    <td>{{ $info?->p_max }}</td>
                                    <td>{{ $info?->water_level }}</td>
                                    <td>{{ $info?->produced_energy }}</td>
                                    <td>{{ $info?->distributed_energy }}</td>
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
                                <td>{{ number_format($bbehs_total_p, 2) }}</td>
                                <td>{{ number_format($bbehs_total_pmax, 2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>


        <h3 class="mt-4 mb-0">Алтай улиастайн эрчим хүчний систем</h3>
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Станцууд</th>
                                <th class="text-center">Гидрогенератор</th>
                                <th class="text-center">Багц</th>
                                <th>P (МВт)</th>
                                <th>P max (МВт)</th>
                                <th class="text-wrap">Усны түвшин (м)</th>
                                <th class="text-wrap">Түлшний нөөц (л)</th>
                                <th class="text-wrap">Үйлдвэрлэсэн ЦЭХ (мян.кВт.цаг)</th>
                                <th class="text-wrap">Түгээсэн ЦЭХ (мян.кВт.цаг)</th>
                                <th>Үндсэн тоноглолын засвар, гарсан доголдол</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($powerAltaiPlants as $plant)
                                @php
                                    $hidro = $plant->equipments->where('equipment_type_id', 5);
                                    $batches = $plant->equipments->where('equipment_type_id', 3);
                                    $statuses = $plant->equipmentStatuses->keyBy('equipment_id');
                                    $info = $plant->powerInfos->first();
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $plant->name }}</td>

                                    {{-- Гидрогенератор --}}
                                    <td>{!! statusIconsWithLabel($hidro, $statuses, $plant->powerPlantType?->name) !!}</td>

                                    {{-- Багц --}}
                                    <td>{!! statusIconsWithLabel($batches, $statuses, $plant->powerPlantType?->name) !!}</td>

                                    <td>{{ $info?->p }}</td>
                                    <td>{{ $info?->p_max }}</td>
                                    <td>{{ $info?->water_level }}</td>
                                    <td>{{ $info?->fuel_reserve }}</td>
                                    <td>{{ $info?->produced_energy }}</td>
                                    <td>{{ $info?->distributed_energy }}</td>
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
                                <td>{{ number_format($altai_total_p, 2) }}</td>
                                <td>{{ number_format($altai_total_pmax, 2) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    @endsection
