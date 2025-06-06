@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">Хоногийн тооцооны журнал</h3>

        <form method="GET" action="{{ route('daily-balance-journals.report') }}" class="row g-3 align-items-center mb-4">
            <div class="col-auto">
                <label for="month" class="col-form-label">Сар:</label>
            </div>
            <div class="col-auto">
                <input type="month" id="month" name="month" value="{{ $selectedMonth }}"
                    class="form-control form-control-sm" max="{{ now()->format('Y-m') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Харах</button>
            </div>
        </form>




        <table class="table table-bordered table-sm text-center" style="font-size: 12px; white-space: nowrap">
            <thead>
                <tr>
                    <th rowspan="2">Станц</th>
                    <th rowspan="2">Төрөл</th>
                    @foreach ($days as $day)
                        <th>{{ str_pad($day, 2, '0', STR_PAD_LEFT) }}</th>
                    @endforeach
                    <th>Нийт</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pivot as $plant => $types)
                    @foreach (['processed' => 'Боловсруулалт', 'distributed' => 'Түгээлт', 'internal_demand' => 'Д/Хэрэглээ', 'percent' => '%'] as $key => $label)
                        <tr>
                            @if ($loop->first)
                                <td rowspan="4">{{ $plant }}</td>
                            @endif
                            <td>{{ $label }}</td>
                            @foreach ($days as $day)
                                <td>{{ number_format($types[$key][$day] ?? 0, 2) }}</td>
                            @endforeach
                            <td><strong>{{ number_format(collect($types[$key])->sum(), 2) }}</strong></td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>

            <tfoot>
                @foreach (['processed' => 'Нийт Боловсруулалт', 'distributed' => 'Нийт Түгээлт', 'internal_demand' => 'Нийт Д/Хэрэглээ', 'percent' => 'Дундаж %'] as $key => $label)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="4"><strong>Нийлбэр</strong></td>
                        @endif
                        <td><strong>{{ $label }}</strong></td>
                        @foreach ($days as $day)
                            <td>
                                <strong>
                                    {{ number_format(
                                        collect($pivot)->sum(function ($types) use ($key, $day) {
                                            return $types[$key][$day] ?? 0;
                                        }),
                                        2,
                                    ) }}
                                </strong>
                            </td>
                        @endforeach
                        <td>
                            <strong>
                                {{ number_format(
                                    collect($pivot)->reduce(function ($carry, $types) use ($key) {
                                        return $carry + collect($types[$key] ?? [])->sum();
                                    }, 0),
                                    2,
                                ) }}
                            </strong>
                        </td>
                    </tr>
                @endforeach
            </tfoot>


        </table>
    </div>
@endsection
