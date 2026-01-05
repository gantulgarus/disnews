@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Импорт/Экспорт Гүйцэтгэл</h3>

        <!-- ОГНООНЫ FILTER FORM -->
        <div class="row mb-3">

            <div class="col-auto">
                <input type="date" name="date" id="date" class="form-control"
                    value="{{ $carbonDate->toDateString() }}">
            </div>

            <div class="col-auto">
                <button class="btn btn-primary">Хайх</button>
            </div>

            <div class="col-auto">
                <button type="button" class="btn btn-secondary">Өнөөдөр</button>
            </div>

            <div class="col-auto">
                <button type="button" class="btn btn-secondary">Өчигдөр</button>
            </div>

            <div class="col-auto">
                <button type="button" class="btn btn-secondary">7 хоног</button>
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
                        <th>Цаг</th>
                        <th colspan="2" class="text-center">АШ 257</th>
                        <th colspan="2" class="text-center">АШ 258</th>
                        <th colspan="2" class="text-center">Тойт 110</th>
                        <th colspan="2" class="text-center">НИЙТ</th> {{-- ШИНЭ --}}
                    </tr>
                    <tr>
                        <th></th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Импорт</th> {{-- ШИНЭ --}}
                        <th>Экспорт</th> {{-- ШИНЭ --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pivot as $time => $fidData)
                        @php
                            $totalImport =
                                ($fidData[257]['IMPORT'] ?? 0) +
                                ($fidData[258]['IMPORT'] ?? 0) +
                                ($fidData[110]['IMPORT'] ?? 0);

                            $totalExport =
                                ($fidData[257]['EXPORT'] ?? 0) +
                                ($fidData[258]['EXPORT'] ?? 0) +
                                ($fidData[110]['EXPORT'] ?? 0);
                        @endphp
                        <tr>
                            <td><strong>{{ $time }}</strong></td>
                            <td class="text-right">
                                {{ number_format($fidData[257]['IMPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[257]['EXPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[258]['IMPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[258]['EXPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[110]['IMPORT'] ?? 0, 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format($fidData[110]['EXPORT'] ?? 0, 2) }}
                            </td>

                            {{-- НИЙТ --}}
                            <td class="text-right font-weight-bold bg-light">
                                {{ number_format($totalImport, 2) }}
                            </td>
                            <td class="text-right font-weight-bold bg-light">
                                {{ number_format($totalExport, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Сонгосон огноонд өгөгдөл олдсонгүй
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- НИЙТ ДҮНГ ХАРУУЛАХ -->
                @if (count($pivot) > 0)
                    <tfoot class="font-weight-bold bg-light">
                        @php
                            $sumImport257 = collect($pivot)->sum(fn($f) => $f[257]['IMPORT'] ?? 0);
                            $sumExport257 = collect($pivot)->sum(fn($f) => $f[257]['EXPORT'] ?? 0);

                            $sumImport258 = collect($pivot)->sum(fn($f) => $f[258]['IMPORT'] ?? 0);
                            $sumExport258 = collect($pivot)->sum(fn($f) => $f[258]['EXPORT'] ?? 0);

                            $sumImport110 = collect($pivot)->sum(fn($f) => $f[110]['IMPORT'] ?? 0);
                            $sumExport110 = collect($pivot)->sum(fn($f) => $f[110]['EXPORT'] ?? 0);

                            $grandImport = $sumImport257 + $sumImport258 + $sumImport110;
                            $grandExport = $sumExport257 + $sumExport258 + $sumExport110;
                        @endphp
                        <tr>
                            <td>НИЙТ ДҮН:</td>
                            <td class="text-right">
                                {{ number_format(collect($pivot)->sum(fn($f) => $f[257]['IMPORT'] ?? 0), 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format(collect($pivot)->sum(fn($f) => $f[257]['EXPORT'] ?? 0), 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format(collect($pivot)->sum(fn($f) => $f[258]['IMPORT'] ?? 0), 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format(collect($pivot)->sum(fn($f) => $f[258]['EXPORT'] ?? 0), 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format(collect($pivot)->sum(fn($f) => $f[110]['IMPORT'] ?? 0), 2) }}
                            </td>
                            <td class="text-right">
                                {{ number_format(collect($pivot)->sum(fn($f) => $f[110]['EXPORT'] ?? 0), 2) }}
                            </td>

                            {{-- НИЙТ --}}
                            <td class="text-right text-primary">
                                {{ number_format($grandImport, 2) }}
                            </td>
                            <td class="text-right text-danger">
                                {{ number_format($grandExport, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
