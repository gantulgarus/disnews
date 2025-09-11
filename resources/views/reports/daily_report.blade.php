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
    </div>
@endsection
