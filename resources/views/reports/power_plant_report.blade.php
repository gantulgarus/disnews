@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
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
                    'SALKHIT_WPP_TOTAL_P' => 'Салхит',
                    'TSETSII_WPP_TOTAL_P' => 'Цэцийн салхи',
                    'SHAND_WPP_TOTAL_P' => 'Шанд',
                    'DARKHAN_SPP_TOTAL_P' => 'Дархан',
                    'MONNAR_SPP_TOTAL_P' => 'Моннартех',
                    'GEGEEN_SPP_TOTAL_P' => 'Гэгээн',
                    'SUMBER_SPP_TOTAL_P' => 'Сүмбэр',
                    'BUHUG_SPP_TOTAL_P' => 'Бөхөг',
                    'GOVI_SPP_TOTAL_P' => 'Говь',
                    'ERDENE_SPP_TOTAL_P' => 'Эрдэнэ',
                ];
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Огноо</th>
                            @foreach ($stationNames as $key => $name)
                                <th>{{ $name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grouped as $datetime => $items)
                            @php
                                $values = $items->keyBy('VAR');
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($datetime)->format('Y-m-d H:i') }}</td>
                                @foreach ($stationNames as $var => $name)
                                    <td class="text-end">
                                        {{ number_format(optional($values->get($var))->VALUE ?? 0, 2) }}
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
