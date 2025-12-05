@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4 fw-bold">СЭХ станцуудын горим, гүйцэтгэл</h2>

        <form method="GET" class="mb-4 row g-3 align-items-center">
            <div class="col-auto">
                <label for="date" class="col-form-label fw-semibold">Огноо:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="date" name="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Шүүлт</button>
            </div>
        </form>

        @if ($results->isEmpty())
            <div class="alert alert-warning">Мэдээлэл олдсонгүй.</div>
        @else
            @php
                $grouped = $results->groupBy('date');
                $stationNames = [
                    'SALKHIT_WPP_TOTAL_P' => 'Салхит СЦС',
                    'TSETSII_WPP_TOTAL_P' => 'Цэций СЦС',
                    'SHAND_WPP_TOTAL_P' => 'Шанд СЦС',
                    'DARKHAN_SPP_TOTAL_P' => 'Дархан НЦС',
                    'MONNAR_SPP_TOTAL_P' => 'Моннаран НЦС',
                    'GEGEEN_SPP_TOTAL_P' => 'Гэгээн НЦС',
                    'SUMBER_SPP_TOTAL_P' => 'Сүмбэр НЦС',
                    'BUHUG_SPP_TOTAL_P' => 'Бөхөг НЦС',
                    'GOVI_SPP_TOTAL_P' => 'Говь НЦС',
                    'ERDENE_SPP_TOTAL_P' => 'Эрдэнэ НЦС',
                    'BAGANUUR_BESS_TOTAL_P_T' => 'Багануур БХС',
                    'SONGINO_BESS_TOTAL_P' => 'Сонгино БХС',
                ];
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th rowspan="2">Огноо</th>
                            @foreach ($stationNames as $key => $name)
                                <th colspan="2">{{ $name }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($stationNames as $key => $name)
                                <th>Горим</th>
                                <th>Гүйцэтгэл</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($grouped as $datetime => $items)
                            @php
                                $values = $items->keyBy('VAR');

                                // Regime мэдээллийг тухайн өдөрөөр авах
                                $regime = $regimeData
                                    ->where('date', \Carbon\Carbon::parse($datetime)->format('Y-m-d'))
                                    ->keyBy('plant_name');
                            @endphp

                            <tr>
                                <td>{{ \Carbon\Carbon::parse($datetime)->format('Y-m-d H:i') }}</td>

                                @foreach ($stationNames as $var => $stationLabel)
                                    @php
                                        // Regime дээрх plant_name тааруулах
                                        $mapping = [
                                            'SALKHIT_WPP_TOTAL_P' => 'Салхит СЦС',
                                            'TSETSII_WPP_TOTAL_P' => 'Цэций СЦС',
                                            'SHAND_WPP_TOTAL_P' => 'Шанд СЦС',
                                            'DARKHAN_SPP_TOTAL_P' => 'Дархан НЦС',
                                            'MONNAR_SPP_TOTAL_P' => 'Моннаран НЦС',
                                            'GEGEEN_SPP_TOTAL_P' => 'Гэгээн НЦС',
                                            'SUMBER_SPP_TOTAL_P' => 'Сүмбэр НЦС',
                                            'BUHUG_SPP_TOTAL_P' => 'Бөхөг НЦС',
                                            'GOVI_SPP_TOTAL_P' => 'Говь НЦС',
                                            'ERDENE_SPP_TOTAL_P' => 'Эрдэнэ НЦС',
                                            'BAGANUUR_BESS_TOTAL_P_T' => 'Багануур БХС',
                                            'SONGINO_BESS_TOTAL_P' => 'Сонгино БХС',
                                        ];

                                        $plantName = $mapping[$var];

                                        // Горим мэдээлэл (Regime)
                                        $regimeValue = optional($regime->get($plantName))->t1 ?? 0;

                                        // Гүйцэтгэл (ZConclusion VALUE)
                                        $executionValue = optional($values->get($var))->VALUE ?? 0;
                                    @endphp

                                    <td class="text-end">{{ number_format($regimeValue, 2) }}</td>
                                    <td class="text-end">{{ number_format($executionValue, 2) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif
    </div>
@endsection
