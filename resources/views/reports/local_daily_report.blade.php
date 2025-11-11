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

        {{-- ДЦС --}}
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Станцууд</th>
                                <th colspan="3" class="text-center">Гидрогенератор</th>
                                <th colspan="3" class="text-center">Багц</th>
                                <th rowspan="2">P max (МВт)</th>
                                <th rowspan="2">P min (МВт)</th>
                                <th rowspan="2" class="text-wrap">Үйлдвэрлэсэн ЦЭХ (мян.кВт.цаг)</th>
                                <th rowspan="2" class="text-wrap">Түгээсэн ЦЭХ (мян.кВт.цаг)</th>
                                <th rowspan="2" class="text-wrap">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                                <th rowspan="2"></th>
                            </tr>
                            <tr>
                                <th>Ажилд</th>
                                <th>Бэлтгэлд</th>
                                <th>Засварт</th>
                                <th>Ажилд</th>
                                <th>Бэлтгэлд</th>
                                <th>Засварт</th>
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

                                    {{-- Зуух --}}
                                    <td>{{ $hidro->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $hidro->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $hidro->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                                    </td>

                                    {{-- Турбогенератор --}}
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                                    </td>

                                    <td>{{ $info?->p_max }}</td>
                                    <td>{{ $info?->p_min }}</td>
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
                                <td colspan="8">Нийт дүн</td>
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


        <h3 class="mt-4 mb-0">Алтай улиастайн эрчим хүчний систем</h3>
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Станцууд</th>
                                <th colspan="3" class="text-center">Гидрогенератор</th>
                                <th colspan="3" class="text-center">Багц</th>
                                <th rowspan="2">P (МВт)</th>
                                <th rowspan="2">P max (МВт)</th>
                                <th rowspan="2">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                                <th rowspan="2"></th>
                            </tr>
                            <tr>
                                <th>Ажилд</th>
                                <th>Бэлтгэлд</th>
                                <th>Засварт</th>
                                <th>Ажилд</th>
                                <th>Бэлтгэлд</th>
                                <th>Засварт</th>
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

                                    {{-- Зуух --}}
                                    <td>{{ $hidro->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $hidro->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $hidro->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                                    </td>

                                    {{-- Турбогенератор --}}
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
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
                                <td colspan="8">Нийт дүн</td>
                                <td>{{ number_format($altai_total_p, 2) }}</td>
                                <td>{{ number_format($altai_total_pmax, 2) }}</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    @endsection
