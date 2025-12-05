@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4 fw-bold">ДЦС-ын горим, гүйцэтгэл</h2>

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
                    'PP4_TOTAL_P' => 'ДЦС-4',
                    'PP3_TOTAL_P' => 'ДЦС-3',
                    'PP2_TOTAL_P' => 'ДЦС-2',
                    'DARKHAN_PP_TOTAL_P' => 'ДДЦС',
                    'ERDENET_PP_TOTAL_P' => 'ЭДЦС',
                    'GOK_PP_TOTAL_P' => 'ЭҮ-н ДЦС',
                    'DALANZADGAD_PP_TOTAL_P' => 'Даланзадгад ДЦС',
                    'UHAAHUDAG_PP_TOTAL_P' => 'УХГ ЦС',
                    'BUURULJUUT_PP_TOTAL_P' => 'Бөөрөлжүүт ЦС I',
                    'TOSON_PP_TOTAL_P' => 'Тосон ДЦС',
                    'IMPORT_TOTAL_P' => 'Импорт/экспорт',
                    'EXPORT_TOTAL_P' => 'Экспорт',
                    'SYSTEM_TOTAL_P' => 'Хэрэглээ',
                ];
            @endphp

            <div class="" style="font-size: 12px;">
                <table class="table table-bordered table-sm
                align-middle">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th rowspan="2">Огноо</th>
                            @foreach ($stationNames as $key => $name)
                                @if ($key === 'SYSTEM_TOTAL_P')
                                    <th colspan="2" class="text-dark" style="background-color: #d1e7dd;">Систем нийлбэр
                                    </th>
                                @else
                                    <th colspan="2">{{ $name }}</th>
                                @endif
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($stationNames as $key => $name)
                                @php $isSystem = $key === 'SYSTEM_TOTAL_P'; @endphp
                                <th {{ $isSystem ? 'style=background-color:#d1e7dd;color:#000;' : '' }}>Горим</th>
                                <th {{ $isSystem ? 'style=background-color:#d1e7dd;color:#000;' : '' }}>Гүйцэтгэл</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($grouped as $datetime => $items)
                            @php
                                $values = $items->keyBy('VAR');
                                $regime = $regimeData
                                    ->where('date', \Carbon\Carbon::parse($datetime)->format('Y-m-d'))
                                    ->keyBy('plant_name');
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($datetime)->format('Y-m-d H:i') }}</td>

                                @foreach ($stationNames as $var => $stationLabel)
                                    @php
                                        $mapping = [
                                            'PP4_TOTAL_P' => 'ДЦС-4',
                                            'PP3_TOTAL_P' => 'ДЦС-3',
                                            'PP2_TOTAL_P' => 'ДЦС-2',
                                            'DARKHAN_PP_TOTAL_P' => 'ДДЦС',
                                            'ERDENET_PP_TOTAL_P' => 'ЭДЦС',
                                            'GOK_PP_TOTAL_P' => 'ЭҮ-н ДЦС',
                                            'DALANZADGAD_PP_TOTAL_P' => 'Даланзадгад ДЦС',
                                            'UHAAHUDAG_PP_TOTAL_P' => 'УХГ ЦС',
                                            'BUURULJUUT_PP_TOTAL_P' => 'Бөөрөлжүүт ЦС I',
                                            'TOSON_PP_TOTAL_P' => 'Тосон ДЦС',
                                            'IMPORT_TOTAL_P' => 'Импорт/экспорт',
                                            'EXPORT_TOTAL_P' => 'Экспорт',
                                            'SYSTEM_TOTAL_P' => 'Систем нийлбэр',
                                        ];

                                        $plantName = $mapping[$var];
                                        $regimeValue = optional($regime->get($plantName))->t1 ?? 0;
                                        $executionValue = optional($values->get($var))->VALUE ?? 0;
                                        $isSystem = $var === 'SYSTEM_TOTAL_P';
                                    @endphp

                                    <td class="text-end"
                                        style="{{ $isSystem ? 'background-color:#d1e7dd;font-weight:600;' : '' }}">
                                        {{ number_format($regimeValue, 2) }}
                                    </td>
                                    <td class="text-end"
                                        style="{{ $isSystem ? 'background-color:#d1e7dd;font-weight:600;' : '' }}">
                                        {{ number_format($executionValue, 2) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
