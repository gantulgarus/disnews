@extends('layouts.admin')

@section('style')
    <style>
        /* Table header styling */
        .table thead th {
            background-color: #4299e1;
            color: #fff;
        }

        /* Two column layout */
        .regions-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        /* Region section styling */
        .region-section {
            position: relative;
            padding: 1.5rem;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .region-section:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        /* Western region - Blue theme */
        .region-western {
            border-top: 4px solid #3b82f6;
            background: linear-gradient(to bottom, rgba(59, 130, 246, 0.02), #ffffff);
        }

        .region-western .region-header {
            color: #3b82f6;
            border-bottom: 2px solid rgba(59, 130, 246, 0.2);
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
        }

        /* Altai region - Green theme */
        .region-altai {
            border-top: 4px solid #10b981;
            background: linear-gradient(to bottom, rgba(16, 185, 129, 0.02), #ffffff);
        }

        .region-altai .region-header {
            color: #10b981;
            border-bottom: 2px solid rgba(16, 185, 129, 0.2);
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
        }

        /* Region badge */
        .region-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
            letter-spacing: 0.5px;
        }

        .region-badge-western {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #ffffff;
        }

        .region-badge-altai {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
        }

        /* Region header */
        .region-header {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .region-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Card enhancements */
        .region-card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .region-card .card-body {
            padding: 1rem;
        }

        /* Summary stats */
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-box {
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-box.western {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .stat-box.altai {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-value.western {
            color: #3b82f6;
        }

        .stat-value.altai {
            color: #10b981;
        }

        /* Icon styling improvements */
        .edit-icon {
            color: #6b7280;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .edit-icon:hover {
            color: #3b82f6;
            transform: scale(1.15);
        }

        /* Compact table styling */
        .compact-table {
            font-size: 0.875rem;
        }

        .compact-table th {
            padding: 0.5rem 0.25rem;
            font-size: 0.75rem;
        }

        .compact-table td {
            padding: 0.5rem 0.25rem;
        }

        /* Scrollable table container */
        .table-scroll {
            max-height: 600px;
            overflow-y: auto;
        }

        /* Responsive design */
        @media (max-width: 1400px) {
            .regions-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .region-section {
                padding: 1rem;
            }

            .region-header {
                font-size: 1rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .summary-stats {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 1.25rem;
            }
        }

        /* Custom scrollbar for table */
        .table-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .table-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Орон нутгийн хоногийн мэдээ</h3>
        </div>

        <!-- Date Filter -->
        <form method="GET" class="mb-4 row g-2 align-items-end">
            <div class="col-auto">
                <input type="date" name="date" id="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Харах</button>
            </div>
        </form>

        <!-- ====================================== -->
        <!-- TWO COLUMN LAYOUT -->
        <!-- ====================================== -->
        <div class="regions-container">

            <!-- ====================================== -->
            <!-- LEFT COLUMN - WESTERN REGION -->
            <!-- ====================================== -->
            <div class="region-section region-western">
                <div class="region-header">
                    <div class="region-title">
                        <span>Баруун бүсийн эрчим хүчний систем</span>
                        <span class="region-badge region-badge-western">ББЭХС</span>
                    </div>
                </div>

                {{-- Summary Stats --}}
                @if (isset($westernRegionCapacities[0]))
                    <div class="summary-stats">
                        <div class="stat-box western">
                            <div class="stat-label">Хамгийн их ачаалал Pmax</div>
                            <div class="stat-value western">
                                {{ number_format($westernRegionCapacities[0]->p_max, 2) }}
                            </div>
                            <div class="stat-label" style="margin-top: 0.25rem;">МВт</div>
                        </div>

                        <div class="stat-box western">
                            <div class="stat-label">Хамгийн бага ачаалал Pmin</div>
                            <div class="stat-value western">
                                {{ number_format($westernRegionCapacities[0]->p_min, 2) }}
                            </div>
                            <div class="stat-label" style="margin-top: 0.25rem;">МВт</div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('western_region_capacities.create') }}" class="btn btn-warning btn-sm m-3">
                        Мэдээ нэмэх
                    </a>
                @endif


                {{-- System Summary --}}
                <div class="region-card mb-3">
                    <div class="card-body">
                        <h6 class="mb-3" style="color: #3b82f6; font-weight: 600;">Системийн мэдээлэл</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm compact-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th></th>
                                        <th>Хэрэглээ</th>
                                        <th>Түгээлт</th>
                                        <th>Pимп.max</th>
                                        <th>Pимп.min</th>
                                        <th>Импортоор авсан</th>
                                        <th>Импортын түгээсэн ЦЭХ</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($westernRegionCapacities as $data)
                                        <tr>
                                            <td class="fw-semibold">Хоногт</td>
                                            <td>{{ $bbehs_consumption }}</td>
                                            <td>{{ $bbehs_distribution }}</td>
                                            <td>{{ $data->p_imp_max }}</td>
                                            <td>{{ $data->p_imp_min }}</td>
                                            <td>{{ $data->import_received }}</td>
                                            <td>{{ $data->import_distributed }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('western_region_capacities.create') }}"
                                                    class="edit-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path
                                                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Power Plants Table --}}
                <div class="region-card">
                    <div class="card-body">
                        <h6 class="mb-3" style="color: #3b82f6; font-weight: 600;">Станцуудын мэдээлэл</h6>
                        <div class="table-scroll">
                            <table class="table table-bordered table-hover table-sm compact-table">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>Станц</th>
                                        <th class="text-center">Генератор</th>
                                        <th class="text-center">Багц</th>
                                        <th>P</th>
                                        <th>P max</th>
                                        <th>Усан түвшин</th>
                                        <th>ЦЭХ үйлд.</th>
                                        <th>ЦЭХ түгээ.</th>
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
                                            <td class="fw-semibold">{{ $plant->name }}</td>
                                            <td>{!! statusIconsWithLabel($hidro, $statuses, $plant->powerPlantType?->name) !!}</td>
                                            <td>{!! statusIconsWithLabel($batches, $statuses, $plant->powerPlantType?->name) !!}</td>
                                            <td>{{ $info?->p }}</td>
                                            <td>{{ $info?->p_max }}</td>
                                            <td>{{ $info?->water_level }}</td>
                                            <td>{{ $info?->produced_energy }}</td>
                                            <td>{{ $info?->distributed_energy }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('daily-equipment-report.create', ['powerPlant' => $plant->id]) }}"
                                                    class="edit-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path
                                                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">Мэдээлэл байхгүй</td>
                                        </tr>
                                    @endforelse
                                    <tr class="fw-bold">
                                        <td colspan="4">Нийт дүн</td>
                                        <td>{{ number_format($bbehs_total_p, 2) }}</td>
                                        <td>{{ number_format($bbehs_total_pmax, 2) }}</td>
                                        <td></td>
                                        <td>{{ number_format($bbehs_total_produced, 2) }}</td>
                                        <td>{{ number_format($bbehs_total_distributed, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====================================== -->
            <!-- RIGHT COLUMN - ALTAI REGION -->
            <!-- ====================================== -->
            <div class="region-section region-altai">
                <div class="region-header">
                    <div class="region-title">
                        <span>Алтай-Улиастайн эрчим хүчний систем</span>
                        <span class="region-badge region-badge-altai">АУЭХС</span>
                    </div>
                </div>

                {{-- Summary Stats --}}
                @if (isset($altaiRegionCapacities[0]))
                    <div class="summary-stats">
                        <div class="stat-box altai">
                            <div class="stat-label">Хамгийн их чадал Pmax</div>
                            <div class="stat-value altai">{{ number_format($altaiRegionCapacities[0]->max_load, 2) }}</div>
                            <div class="stat-label" style="margin-top: 0.25rem;">МВт</div>
                        </div>
                        <div class="stat-box altai">
                            <div class="stat-label">Хамгийн бага чадал Pmin</div>
                            <div class="stat-value altai">{{ number_format($altaiRegionCapacities[0]->min_load, 2) }}</div>
                            <div class="stat-label" style="margin-top: 0.25rem;">МВт</div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('altai-region-capacity.create') }}" class="btn btn-warning btn-sm m-3">
                        Мэдээ нэмэх
                    </a>
                @endif

                {{-- System Summary --}}
                <div class="region-card mb-3">
                    <div class="card-body">
                        <h6 class="mb-3" style="color: #10b981; font-weight: 600;">Системийн мэдээлэл</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm compact-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th></th>
                                        <th>ББЭХС-ээс</th>
                                        <th>ТБНС-ээс</th>
                                        <th>Тайлбар</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($altaiRegionCapacities as $i)
                                        <tr>
                                            <td class="fw-semibold">Хоногт</td>
                                            <td>{{ $i->import_from_bbexs }}</td>
                                            <td>{{ $i->import_from_tbns }}</td>
                                            <td>{{ $i->remark }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('altai-region-capacity.create') }}" class="edit-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path
                                                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Power Plants Table --}}
                <div class="region-card">
                    <div class="card-body">
                        <h6 class="mb-3" style="color: #10b981; font-weight: 600;">Станцуудын мэдээлэл</h6>
                        <div class="table-scroll">
                            <table class="table table-bordered table-hover table-sm compact-table">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>Станц</th>
                                        <th class="text-center">Генератор</th>
                                        <th class="text-center">Багц</th>
                                        <th>P</th>
                                        <th>P max</th>
                                        <th>Усан түвшин</th>
                                        <th>Түлш</th>
                                        <th>ЦЭХ үйлд.</th>
                                        <th>ЦЭХ түгээ.</th>
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
                                            <td class="fw-semibold">{{ $plant->name }}</td>
                                            <td>{!! statusIconsWithLabel($hidro, $statuses, $plant->powerPlantType?->name) !!}</td>
                                            <td>{!! statusIconsWithLabel($batches, $statuses, $plant->powerPlantType?->name) !!}</td>
                                            <td>{{ $info?->p }}</td>
                                            <td>{{ $info?->p_max }}</td>
                                            <td>{{ $info?->water_level }}</td>
                                            <td>{{ $info?->fuel_reserve }}</td>
                                            <td>{{ $info?->produced_energy }}</td>
                                            <td>{{ $info?->distributed_energy }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('daily-equipment-report.create', ['powerPlant' => $plant->id]) }}"
                                                    class="edit-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
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
                                        <td>{{ number_format($altai_total_produced, 2) }}</td>
                                        <td>{{ number_format($altai_total_distributed, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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

                [$color, $bgColor] = match ($status) {
                    'Ажилд' => ['#dc2626', 'rgba(220, 38, 38, 0.15)'],
                    'Бэлтгэлд' => ['#16a34a', 'rgba(22, 163, 74, 0.15)'],
                    'Засварт' => ['#6b7280', 'rgba(107, 114, 128, 0.15)'],
                    default => ['#9ca3af', 'rgba(156, 163, 175, 0.15)'],
                };

                $html .=
                    '<div style="display: flex; flex-direction: column; align-items: center; gap: 2px; transition: all 0.2s ease; cursor: pointer;"
                    onmouseover="this.style.transform=\'translateY(-2px)\';" onmouseout="this.style.transform=\'translateY(0)\';"
                    title="' .
                    $e->name .
                    ' - ' .
                    $status .
                    '">
                    <div style="position: relative; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px;
                        border-radius: 6px; background: ' .
                    $bgColor .
                    '; transition: all 0.2s ease;">
                        <img src="' .
                    $iconPath .
                    '" style="width: 14px; height: 14px;" alt="' .
                    $status .
                    '">
                        <div style="position: absolute; top: -2px; right: -2px; width: 7px; height: 7px;
                            background: ' .
                    $color .
                    '; border: 1px solid white; border-radius: 50%;"></div>
                    </div>
                    <div style="font-size: 8px; color: #374151; font-weight: 600; white-space: nowrap;
                        overflow: hidden; text-overflow: ellipsis; max-width: 40px;">' .
                    $e->name .
                    '</div>
                </div>';
            }

            $html .= '</div>';
            return $html;
        }
    @endphp
@endsection
