@extends('layouts.admin')

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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th></th>
                                <th>Огноо</th>
                                <th>Pmax/min (МВт)</th>
                                <th>Э.боловсруулалт (мян кВт.цаг)</th>
                                <th>Э.түгээлт (мян кВт.цаг)</th>
                                <th>Э.импорт (мян кВт.цаг)</th>
                                <th>Э.экспорт (мян кВт.цаг)</th>
                                <th>Pимп.max (МВт)</th>
                                <th>Э.хязгаарлалт (кВт.цаг)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($journals as $journal)
                                <tr>
                                    <td>Хоногт</td>
                                    <td>{{ $journal->report_date }}</td>
                                    <td>—</td>
                                    <td>{{ number_format($journal->total_processed, 2) }}</td>
                                    <td>{{ number_format($journal->total_distribution, 2) }}</td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td>—</td>
                                    <td>—</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>Сарын эхнээс</td>
                                <td></td>
                                <td>—</td>
                                <td></td>
                                <td></td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">№</th>
                                <th rowspan="2">Огноо</th>
                                <th rowspan="2">Станц</th>
                                <th colspan="3" class="text-center">Зуух</th>
                                <th colspan="3" class="text-center">Турбин</th>
                                <th rowspan="2">P (МВт)</th>
                                <th rowspan="2">Pmax (МВт)</th>
                                <th rowspan="2">Үндсэн тоноглолын засвар, гарсан доголдол</th>
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
                            @foreach ($powerPlantDailyReports as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $report->report_date }}</td>
                                    <td>{{ $report->powerPlant->name }}</td>

                                    <!-- Boiler Working -->
                                    <td>
                                        {{ implode(
                                            ', ',
                                            collect(json_decode($report->boiler_working))->map(function ($boiler_id) {
                                                    return \App\Models\Boiler::find($boiler_id)->name ?? 'Нэр олдсонгүй';
                                                })->toArray(),
                                        ) }}
                                    </td>


                                    <!-- Boiler Preparation -->
                                    <td>
                                        {{ implode(
                                            ', ',
                                            collect(json_decode($report->boiler_preparation))->map(function ($boiler_id) {
                                                    return \App\Models\Boiler::find($boiler_id)->name ?? 'Нэр олдсонгүй';
                                                })->toArray(),
                                        ) }}
                                    </td>

                                    <!-- Boiler Repair -->
                                    <td>
                                        {{ implode(
                                            ', ',
                                            collect(json_decode($report->boiler_repair))->map(function ($boiler_id) {
                                                    return \App\Models\Boiler::find($boiler_id)->name ?? 'Нэр олдсонгүй';
                                                })->toArray(),
                                        ) }}
                                    </td>

                                    <!-- Turbine Working -->
                                    <td>
                                        {{ implode(
                                            ', ',
                                            collect(json_decode($report->turbine_working))->map(function ($turbine_id) {
                                                    return \App\Models\TurbineGenerator::find($turbine_id)->name ?? 'Нэр олдсонгүй';
                                                })->toArray(),
                                        ) }}
                                    </td>

                                    <!-- Turbine Preparation -->
                                    <td>
                                        {{ implode(
                                            ', ',
                                            collect(json_decode($report->turbine_preparation))->map(function ($turbine_id) {
                                                    return \App\Models\TurbineGenerator::find($turbine_id)->name ?? 'Нэр олдсонгүй';
                                                })->toArray(),
                                        ) }}
                                    </td>

                                    <!-- Turbine Repair -->
                                    <td>
                                        {{ implode(
                                            ', ',
                                            collect(json_decode($report->turbine_repair))->map(function ($turbine_id) {
                                                    return \App\Models\TurbineGenerator::find($turbine_id)->name ?? 'Нэр олдсонгүй';
                                                })->toArray(),
                                        ) }}
                                    </td>
                                    <td>{{ $report->power_max ?? '-' }}</td>
                                    <td>{{ $report->power ?? '-' }}</td>
                                    <td>{{ $report->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
                                <th>Дутуу түгээсэн ЭХ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasralts as $index => $tasralt)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
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

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Цахилгаан, дулаан дамжуулах, түгээх сүлжээнд хийгдсэн захиалгат ажил</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
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
