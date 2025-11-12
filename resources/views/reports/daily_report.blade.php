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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journals as $journal)
                                <tr>
                                    <td>Хоногт</td>
                                    <td></td>
                                    <td>{{ number_format($journal->total_processed, 2) }}</td>
                                    <td>{{ number_format($journal->total_distribution, 2) }}</td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td>—</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>Сарын эхнээс</td>
                                <td class="fw-bold fs-3">
                                    {{ number_format($system_data->max_value) }}/{{ number_format($system_data->min_value) }}
                                </td>
                                <td>—</td>
                                <td></td>
                                <td></td>
                                <td>—</td>
                                <td class="fw-bold fs-3">{{ $import_data->max_value }}</td>
                                <td>—</td>
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
                                <th colspan="3" class="text-center">Зуух</th>
                                <th colspan="3" class="text-center">Турбогенератор</th>
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
                            @forelse ($powerPlants as $plant)
                                @php
                                    $boilers = $plant->equipments->where('equipment_type_id', 1);
                                    $turbos = $plant->equipments->where('equipment_type_id', 2);
                                    $statuses = $plant->equipmentStatuses->keyBy('equipment_id');
                                    $info = $plant->powerInfos->first();
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $plant->name }}</td>

                                    {{-- Зуух --}}
                                    <td>{{ $boilers->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $boilers->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $boilers->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                                    </td>

                                    {{-- Турбогенератор --}}
                                    <td>{{ $turbos->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $turbos->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $turbos->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
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
                                <td>{{ number_format($total_p, 2) }}</td>
                                <td>{{ number_format($total_pmax, 2) }}</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- СЭХ --}}
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Станцууд</th>
                                <th colspan="3" class="text-center">Багц</th>
                                <th colspan="3" class="text-center">Инвертер</th>
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
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                                    </td>

                                    {{-- Инвертер --}}
                                    <td>{{ $inverters->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $inverters->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                                    </td>
                                    <td>{{ $inverters->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
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
                                <td>{{ number_format($sun_wind_total_p, 2) }}</td>
                                <td>{{ number_format($sun_wind_total_pmax, 2) }}</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- Battery --}}
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mt-2">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Станцууд</th>
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
                                <td colspan="5">Нийт дүн</td>
                                <td>{{ number_format($battery_total_p, 2) }}</td>
                                <td>{{ number_format($battery_total_pmax, 2) }}</td>
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
    @endsection
