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
                    @php
                        // ЗӨВХӨН ТУХАЙН СТАНЦАД БАЙГАА ӨГӨГДЛИЙН ТӨРЛИЙГ ХАРУУЛАХ
                        $rows = [];

                        // Journal өгөгдөл байвал
                        if (isset($types['has_journal']) && $types['has_journal']) {
                            $rows += [
                                'processed' => 'Боловсруулалт',
                                'distributed' => 'Түгээлт',
                                'internal_demand' => 'Д/Хэрэглээ',
                                'percent' => '%',
                            ];
                        }

                        // Battery өгөгдөл байвал
                        if (isset($types['has_battery']) && $types['has_battery']) {
                            $rows += [
                                'battery_given' => 'ТБНС-д өгсөн',
                                'battery_taken' => 'ТБНС-ээс авсан',
                            ];
                        }

                        // Import/Export өгөгдөл байвал
                        if (isset($types['has_import_export']) && $types['has_import_export']) {
                            $rows += [
                                'import' => 'Импорт',
                                'export' => 'Экспорт',
                            ];
                        }
                    @endphp

                    @foreach ($rows as $key => $label)
                        <tr>
                            @if ($loop->first)
                                <td rowspan="{{ count($rows) }}">
                                    <a
                                        href="{{ route('daily-balance-journals.showPlant', ['plant' => $types['plant_id'], 'month' => $selectedMonth]) }}">
                                        <strong>{{ $plant }}</strong>
                                    </a>
                                </td>
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

            {{-- --------------------------- --}}
            {{--       FOOTER (Нийлбэр)      --}}
            {{-- --------------------------- --}}
            <tfoot>
                @php
                    // Хоног бүрийн нийлбэрийг тооцоолох
                    $totalsByDay = [];

                    foreach ($days as $day) {
                        $totalsByDay[$day] = [
                            'processed' => collect($pivot)->sum(fn($types) => $types['processed'][$day] ?? 0),
                            'distributed' => collect($pivot)->sum(fn($types) => $types['distributed'][$day] ?? 0),
                            'import' => collect($pivot)->sum(fn($types) => $types['import'][$day] ?? 0),
                        ];

                        // Нийт хэрэглээ = Нийт боловсруулалт + Import
                        $totalsByDay[$day]['total_consumption'] =
                            $totalsByDay[$day]['processed'] + $totalsByDay[$day]['import'];

                        // Дотоод хэрэгцээ = Нийт боловсруулалт - Нийт түгээлт
                        $totalsByDay[$day]['internal_need'] =
                            $totalsByDay[$day]['processed'] - $totalsByDay[$day]['distributed'];

                        // Хувь = (Нийт боловсруулалт - Нийт түгээлт) / Нийт боловсруулалт * 100
                        if ($totalsByDay[$day]['processed'] > 0) {
                            $totalsByDay[$day]['percent'] =
                                ($totalsByDay[$day]['internal_need'] / $totalsByDay[$day]['processed']) * 100;
                        } else {
                            $totalsByDay[$day]['percent'] = 0;
                        }
                    }

                    // Сарын нийт дүн
                    $monthlyTotals = [
                        'processed' => collect($totalsByDay)->sum('processed'),
                        'distributed' => collect($totalsByDay)->sum('distributed'),
                        'import' => collect($totalsByDay)->sum('import'),
                        'total_consumption' => collect($totalsByDay)->sum('total_consumption'),
                        'internal_need' => collect($totalsByDay)->sum('internal_need'),
                    ];

                    // Сарын дундаж хувь
                    if ($monthlyTotals['processed'] > 0) {
                        $monthlyTotals['percent'] =
                            ($monthlyTotals['internal_need'] / $monthlyTotals['processed']) * 100;
                    } else {
                        $monthlyTotals['percent'] = 0;
                    }
                @endphp

                {{-- 1. Нийт хэрэглээ --}}
                <tr class="table-info">
                    <td rowspan="5"><strong>Нийлбэр</strong></td>
                    <td><strong>Нийт хэрэглээ</strong></td>
                    @foreach ($days as $day)
                        <td><strong>{{ number_format($totalsByDay[$day]['total_consumption'], 2) }}</strong></td>
                    @endforeach
                    <td><strong>{{ number_format($monthlyTotals['total_consumption'], 2) }}</strong></td>
                </tr>

                {{-- 2. Нийт боловсруулалт --}}
                <tr>
                    <td><strong>Нийт боловсруулалт</strong></td>
                    @foreach ($days as $day)
                        <td><strong>{{ number_format($totalsByDay[$day]['processed'], 2) }}</strong></td>
                    @endforeach
                    <td><strong>{{ number_format($monthlyTotals['processed'], 2) }}</strong></td>
                </tr>

                {{-- 3. Нийт түгээлт --}}
                <tr>
                    <td><strong>Нийт түгээлт</strong></td>
                    @foreach ($days as $day)
                        <td><strong>{{ number_format($totalsByDay[$day]['distributed'], 2) }}</strong></td>
                    @endforeach
                    <td><strong>{{ number_format($monthlyTotals['distributed'], 2) }}</strong></td>
                </tr>

                {{-- 4. Дотоод хэрэгцээ --}}
                <tr class="table-warning">
                    <td><strong>Дотоод хэрэгцээ</strong></td>
                    @foreach ($days as $day)
                        <td><strong>{{ number_format($totalsByDay[$day]['internal_need'], 2) }}</strong></td>
                    @endforeach
                    <td><strong>{{ number_format($monthlyTotals['internal_need'], 2) }}</strong></td>
                </tr>

                {{-- 5. Хувь --}}
                <tr class="table-warning">
                    <td><strong>Хувь (%)</strong></td>
                    @foreach ($days as $day)
                        <td><strong>{{ number_format($totalsByDay[$day]['percent'], 2) }}%</strong></td>
                    @endforeach
                    <td><strong>{{ number_format($monthlyTotals['percent'], 2) }}%</strong></td>
                </tr>
            </tfoot>

        </table>
    </div>
@endsection
