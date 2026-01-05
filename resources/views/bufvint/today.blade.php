@extends('layouts.admin')

@section('content')
    <div class="container-fluid mt-2">
        <h3>Импорт/Экспорт Гүйцэтгэл</h3>

        <!-- ОГНООНЫ FILTER FORM -->
        <form method="GET" action="{{ route('bufvint.today') }}" class="row mb-3">
            @csrf
            <div class="col-auto">
                <input type="date" name="date" id="date" class="form-control"
                    value="{{ $moscowDate->toDateString() }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Хайх</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('bufvint.today', ['date' => Carbon\Carbon::now()->timezone('Europe/Moscow')->toDateString()]) }}"
                    class="btn btn-secondary">Өнөөдөр (Москва)</a>
            </div>
            <div class="col-auto">
                <a href="{{ route('bufvint.today', ['date' => Carbon\Carbon::now()->timezone('Europe/Moscow')->subDay()->toDateString()]) }}"
                    class="btn btn-secondary">Өчигдөр (Москва)</a>
            </div>
        </form>

        <!-- XML IMPORT товч -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#xmlImportModal">
            📥 XML импорт
        </button>

        <!-- Сонгосон огноо харуулах -->
        <div class="alert alert-info">
            <strong>Сонгосон Москвагийн огноо:</strong> {{ $moscowDate->format('Y-m-d (l)') }}
            <br>
            {{-- <small>
                Монголын бичлэг: {{ $debug['total_records'] }} |
                Оросын бичлэг: {{ $debug['russian_records'] }} |
                Оросын маргаашийн: {{ $debug['russian_tomorrow_records'] }}
            </small> --}}
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- ХҮСНЭГТ -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="2" colspan="2">Цаг</th>
                        @foreach ([257, 258, 110] as $fider)
                            <th colspan="6" class="text-center">
                                {{ $fider == 110 ? 'Тойт ' . $fider : 'АШ ' . $fider }}
                            </th>
                        @endforeach
                        <th colspan="4" class="text-center">НИЙТ</th>
                    </tr>
                    <tr>
                        @foreach ([257, 258, 110] as $fider)
                            <th colspan="3" class="text-center">Импорт (кВт)</th>
                            <th colspan="3" class="text-center">Экспорт (кВт)</th>
                        @endforeach
                        <th colspan="2" class="text-center">Импорт</th>
                        <th colspan="2" class="text-center">Экспорт</th>
                    </tr>
                    <tr>
                        <th>УБ</th>
                        <th>Москва</th>
                        @foreach ([257, 258, 110] as $fider)
                            <th>Монгол</th>
                            <th>Орос</th>
                            <th>Зөрүү</th>
                            <th>Монгол</th>
                            <th>Орос</th>
                            <th>Зөрүү</th>
                        @endforeach
                        <th>Монгол</th>
                        <th>Орос</th>
                        <th>Монгол</th>
                        <th>Орос</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totals = [
                            'mn_import_257' => 0,
                            'ru_import_257' => 0,
                            'diff_import_257' => 0,
                            'mn_export_257' => 0,
                            'ru_export_257' => 0,
                            'diff_export_257' => 0,
                            'mn_import_258' => 0,
                            'ru_import_258' => 0,
                            'diff_import_258' => 0,
                            'mn_export_258' => 0,
                            'ru_export_258' => 0,
                            'diff_export_258' => 0,
                            'mn_import_110' => 0,
                            'ru_import_110' => 0,
                            'diff_import_110' => 0,
                            'mn_export_110' => 0,
                            'ru_export_110' => 0,
                            'diff_export_110' => 0,
                        ];
                        $prevMoscowDate = null;
                    @endphp

                    @foreach ($pivot as $moscowTime => $timeData)
                        @php
                            $ubTime = $timeData['ub_time'];
                            $ubDate = $timeData['ub_date'];
                            $moscowDateStr = $timeData['moscow_date'];
                            $fidData = $timeData['data'];

                            // Огноо солигдсон эсэхийг тэмдэглэх
                            $dateChanged = $prevMoscowDate === null || $prevMoscowDate !== $moscowDateStr;
                            $prevMoscowDate = $moscowDateStr;

                            // Фидэр бүрийн утгыг тооцоолох
                            $values = [];
                            foreach ([257, 258, 110] as $fider) {
                                $mnImport = $fidData[$fider]['IMPORT'] ?? 0;
                                $mnExport = $fidData[$fider]['EXPORT'] ?? 0;

                                $ruImport = $russianData[$moscowTime][$fider][0]->import_kwt ?? 0;
                                $ruExport = $russianData[$moscowTime][$fider][0]->export_kwt ?? 0;

                                $values[$fider] = [
                                    'mn_import' => $mnImport,
                                    'ru_import' => $ruImport,
                                    'diff_import' => $mnImport - $ruImport,
                                    'mn_export' => $mnExport,
                                    'ru_export' => $ruExport,
                                    'diff_export' => $mnExport - $ruExport,
                                ];

                                // Нийт дүн бодох
                                $totals['mn_import_' . $fider] += $mnImport;
                                $totals['ru_import_' . $fider] += $ruImport;
                                $totals['diff_import_' . $fider] += $mnImport - $ruImport;
                                $totals['mn_export_' . $fider] += $mnExport;
                                $totals['ru_export_' . $fider] += $ruExport;
                                $totals['diff_export_' . $fider] += $mnExport - $ruExport;
                            }

                            // Нийт утгууд
                            $totalMnImport =
                                $values[257]['mn_import'] + $values[258]['mn_import'] + $values[110]['mn_import'];
                            $totalRuImport =
                                $values[257]['ru_import'] + $values[258]['ru_import'] + $values[110]['ru_import'];
                            $totalMnExport =
                                $values[257]['mn_export'] + $values[258]['mn_export'] + $values[110]['mn_export'];
                            $totalRuExport =
                                $values[257]['ru_export'] + $values[258]['ru_export'] + $values[110]['ru_export'];
                        @endphp

                        @if ($dateChanged)
                            <tr class="table-info">
                                <td colspan="24" class="text-center font-weight-bold py-3">
                                    📅 УБ: {{ $ubDate }} | Москва: {{ $moscowDateStr }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td><strong>{{ $ubTime }}</strong></td>
                            <td><strong>{{ $moscowTime }}</strong></td>

                            @foreach ([257, 258, 110] as $fider)
                                <!-- Импорт -->
                                <td class="text-right">{{ number_format($values[$fider]['mn_import'], 2) }}</td>
                                <td class="text-right">{{ number_format($values[$fider]['ru_import'], 2) }}</td>
                                <td class="text-right {{ $values[$fider]['diff_import'] != 0 ? 'bg-warning' : '' }}">
                                    {{ number_format($values[$fider]['diff_import'], 2) }}
                                </td>

                                <!-- Экспорт -->
                                <td class="text-right">{{ number_format($values[$fider]['mn_export'], 2) }}</td>
                                <td class="text-right">{{ number_format($values[$fider]['ru_export'], 2) }}</td>
                                <td class="text-right {{ $values[$fider]['diff_export'] != 0 ? 'bg-warning' : '' }}">
                                    {{ number_format($values[$fider]['diff_export'], 2) }}
                                </td>
                            @endforeach

                            <!-- НИЙТ -->
                            <td class="text-right font-weight-bold">{{ number_format($totalMnImport, 2) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($totalRuImport, 2) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($totalMnExport, 2) }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($totalRuExport, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <!-- НИЙТ ДҮН -->
                <tfoot class="font-weight-bold bg-light">
                    @php
                        $grandMnImport = $totals['mn_import_257'] + $totals['mn_import_258'] + $totals['mn_import_110'];
                        $grandRuImport = $totals['ru_import_257'] + $totals['ru_import_258'] + $totals['ru_import_110'];
                        $grandMnExport = $totals['mn_export_257'] + $totals['mn_export_258'] + $totals['mn_export_110'];
                        $grandRuExport = $totals['ru_export_257'] + $totals['ru_export_258'] + $totals['ru_export_110'];
                    @endphp
                    <tr>
                        <td colspan="2">НИЙТ ДҮН:</td>

                        @foreach ([257, 258, 110] as $fider)
                            <td class="text-right">{{ number_format($totals['mn_import_' . $fider], 2) }}</td>
                            <td class="text-right">{{ number_format($totals['ru_import_' . $fider], 2) }}</td>
                            <td class="text-right {{ $totals['diff_import_' . $fider] != 0 ? 'bg-warning' : '' }}">
                                {{ number_format($totals['diff_import_' . $fider], 2) }}
                            </td>
                            <td class="text-right">{{ number_format($totals['mn_export_' . $fider], 2) }}</td>
                            <td class="text-right">{{ number_format($totals['ru_export_' . $fider], 2) }}</td>
                            <td class="text-right {{ $totals['diff_export_' . $fider] != 0 ? 'bg-warning' : '' }}">
                                {{ number_format($totals['diff_export_' . $fider], 2) }}
                            </td>
                        @endforeach

                        <td class="text-right text-primary">{{ number_format($grandMnImport, 2) }}</td>
                        <td class="text-right text-primary">{{ number_format($grandRuImport, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($grandMnExport, 2) }}</td>
                        <td class="text-right text-danger">{{ number_format($grandRuExport, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



    <!-- XML IMPORT MODAL -->
    <div class="modal fade" id="xmlImportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('ru-xml.import') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Орос талын XML импорт</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>XML файл</label>
                        <input type="file" name="xml_file" class="form-control" accept=".xml" required>
                    </div>
                    <div class="alert alert-info mb-0">
                        ⚠️ Сонгосон XML файл нь тухайн өдрийн (24 цаг, 30 мин интервал) файл байх ёстой
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Импорт хийх</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Болих</button>
                </div>
            </form>
        </div>
    </div>
@endsection
