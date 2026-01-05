@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Өгөгдлийн харагдац</h3>

        <!-- DEBUG МЭДЭЭЛЭЛ - ЗАСВАРЛАСАН -->
        @if (isset($debug))
            <div class="alert alert-info">
                <strong>Debug:</strong>
                Нийт бичлэг: {{ $debug['total_records'] ?? 0 }} |
                Фидерүүд: {{ implode(', ', $debug['fiders_in_db'] ?? []) }}
            </div>
        @endif

        <!-- ОГНООНЫ FILTER FORM -->
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('bufvint.today') }}" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="date" class="mr-2">Огноо сонгох:</label>
                        <input type="date" name="date" id="date" class="form-control"
                            value="{{ $carbonDate->toDateString() }}" max="{{ \Carbon\Carbon::today()->toDateString() }}">
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Хайх
                    </button>

                    <button type="button" class="btn btn-secondary mr-2"
                        onclick="document.getElementById('date').value='{{ \Carbon\Carbon::today()->toDateString() }}'; this.form.submit();">
                        Өнөөдөр
                    </button>

                    <button type="button" class="btn btn-secondary mr-2"
                        onclick="document.getElementById('date').value='{{ \Carbon\Carbon::yesterday()->toDateString() }}'; this.form.submit();">
                        Өчигдөр
                    </button>

                    <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('date').value='{{ \Carbon\Carbon::today()->subDays(7)->toDateString() }}'; this.form.submit();">
                        7 хоногийн өмнө
                    </button>
                </form>
            </div>
        </div>

        <!-- Сонгосон огноо харуулах -->
        <div class="alert alert-info">
            <strong>Сонгосон огноо:</strong> {{ $carbonDate->format('Y-m-d (l)') }}
        </div>

        <!-- ХҮСНЭГТ -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="2">Цаг</th>
                        <th colspan="3" class="text-center">АШ 257</th>
                        <th colspan="3" class="text-center">АШ 258</th>
                        <th colspan="3" class="text-center">Тойт 110</th>
                    </tr>
                    <tr>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Нийлбэр (кВт)</th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Нийлбэр (кВт)</th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Нийлбэр (кВт)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pivot as $time => $fidData)
                        @php
                            // Нийлбэр тооцоолох
                            $net257 = ($fidData[257]['IMPORT'] ?? 0) - ($fidData[257]['EXPORT'] ?? 0);
                            $net258 = ($fidData[258]['IMPORT'] ?? 0) - ($fidData[258]['EXPORT'] ?? 0);
                            $net110 = ($fidData[110]['IMPORT'] ?? 0) - ($fidData[110]['EXPORT'] ?? 0);
                        @endphp
                        <tr>
                            <td><strong>{{ $time }}</strong></td>

                            <!-- АШ 257 -->
                            <td class="text-right">
                                {{ number_format($fidData[257]['IMPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[257]['EXPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right {{ $net257 < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($net257, 2) }}
                            </td>

                            <!-- АШ 258 -->
                            <td class="text-right">
                                {{ number_format($fidData[258]['IMPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[258]['EXPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right {{ $net258 < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($net258, 2) }}
                            </td>

                            <!-- Тойт 110 -->
                            <td class="text-right">
                                {{ number_format($fidData[110]['IMPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[110]['EXPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right {{ $net110 < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($net110, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                Сонгосон огноонд өгөгдөл олдсонгүй
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- НИЙТ ДҮНГ ХАРУУЛАХ -->
                @if (count($pivot) > 0)
                    @php
                        // Нийт дүнгүүд
                        $totalImport257 = collect($pivot)->sum(fn($f) => $f[257]['IMPORT'] ?? 0);
                        $totalExport257 = collect($pivot)->sum(fn($f) => $f[257]['EXPORT'] ?? 0);
                        $totalNet257 = $totalImport257 - $totalExport257;

                        $totalImport258 = collect($pivot)->sum(fn($f) => $f[258]['IMPORT'] ?? 0);
                        $totalExport258 = collect($pivot)->sum(fn($f) => $f[258]['EXPORT'] ?? 0);
                        $totalNet258 = $totalImport258 - $totalExport258;

                        $totalImport110 = collect($pivot)->sum(fn($f) => $f[110]['IMPORT'] ?? 0);
                        $totalExport110 = collect($pivot)->sum(fn($f) => $f[110]['EXPORT'] ?? 0);
                        $totalNet110 = $totalImport110 - $totalExport110;
                    @endphp
                    <tfoot class="font-weight-bold bg-light">
                        <tr>
                            <td>НИЙТ ДҮН:</td>

                            <!-- АШ 257 -->
                            <td class="text-right">{{ number_format($totalImport257, 2) }}</td>
                            <td class="text-right">{{ number_format($totalExport257, 2) }}</td>
                            <td class="text-right {{ $totalNet257 < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($totalNet257, 2) }}
                            </td>

                            <!-- АШ 258 -->
                            <td class="text-right">{{ number_format($totalImport258, 2) }}</td>
                            <td class="text-right">{{ number_format($totalExport258, 2) }}</td>
                            <td class="text-right {{ $totalNet258 < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($totalNet258, 2) }}
                            </td>

                            <!-- Тойт 110 -->
                            <td class="text-right">{{ number_format($totalImport110, 2) }}</td>
                            <td class="text-right">{{ number_format($totalExport110, 2) }}</td>
                            <td class="text-right {{ $totalNet110 < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($totalNet110, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
