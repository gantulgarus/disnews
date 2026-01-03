@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Өгөгдлийн харагдац</h3>

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

                    <!-- ШУУРХАЙ ТОВЧНУУД -->
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
                        <th>Цаг</th>
                        <th colspan="2" class="text-center">АШ 257</th>
                        <th colspan="2" class="text-center">АШ 258</th>
                        <th colspan="2" class="text-center">Тойт 110</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                        <th>Импорт (кВт)</th>
                        <th>Экспорт (кВт)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pivot as $time => $fidData)
                        <tr>
                            <td><strong>{{ $time }}</strong></td>
                            <td class="text-right">{{ number_format($fidData[257]['IMPORT'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($fidData[257]['EXPORT'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($fidData[258]['IMPORT'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($fidData[258]['EXPORT'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($fidData[110]['IMPORT'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($fidData[110]['EXPORT'] ?? 0, 2) }}</td>
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
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
