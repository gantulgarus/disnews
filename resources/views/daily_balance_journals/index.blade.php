@extends('layouts.admin')

@section('content')
    <div class="page-header d-print-none mt-3">
        <div class="container-fluid">
            <form method="GET" class="mb-4 row g-2 align-items-end">
                <div class="col-auto">
                    {{-- <label for="date" class="form-label">Огноо:</label> --}}
                    <input type="date" name="date" id="date" value="{{ $date }}" class="form-control">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Харах</button>
                </div>
            </form>
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Хоногийн тооцооны журнал
                    </h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('daily-balance-journals.report') }}" class="btn btn-info">
                        <i class="ti ti-plus me-1"></i> Тайлан
                    </a>
                    <a href="{{ route('daily-balance-journals.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Бүртгэх
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show small py-2">
                    <i class="ti ti-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-sm table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">Станц</th>
                                    <th rowspan="2" class="text-center align-middle">Огноо</th>
                                    <th colspan="3" class="text-center">Үндсэн үзүүлэлт</th>
                                    <th rowspan="2" class="text-center align-middle">%</th>
                                    <th colspan="5" class="text-center bg-light">Диспетчерийн графикийн хазайлт (Нэгдсэн)
                                    </th>
                                    <th colspan="2" class="text-center">Шалтгаан</th>
                                    <th rowspan="2" class="text-center align-middle">Диспетчер</th>
                                    <th rowspan="2" class="text-center align-middle">Үйлдэл</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Боловсруулалт</th>
                                    <th class="text-center">Түгээлт</th>
                                    <th class="text-center">Д/Хэрэгцээ</th>
                                    <th class="text-center">+ зөрчил</th>
                                    <th class="text-center">- зөрчил<br><small>(спот)</small></th>
                                    <th class="text-center">- зөрчил<br><small>(импорт)</small></th>
                                    <th class="text-center">+ шийдвэр</th>
                                    <th class="text-center">- шийдвэр</th>
                                    <th class="text-center">Хэрэглээний өсөлт</th>
                                    <th class="text-center">Бусад станц доголдол</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($journals as $journal)
                                    @php
                                        $totalPosDeviation =
                                            ($journal->positive_deviation_00_08 ?? 0) +
                                            ($journal->positive_deviation_08_16 ?? 0) +
                                            ($journal->positive_deviation_16_24 ?? 0);

                                        $totalNegDeviationSpot =
                                            ($journal->negative_deviation_spot_00_08 ?? 0) +
                                            ($journal->negative_deviation_spot_08_16 ?? 0) +
                                            ($journal->negative_deviation_spot_16_24 ?? 0);

                                        $totalNegDeviationImport =
                                            ($journal->negative_deviation_import_00_08 ?? 0) +
                                            ($journal->negative_deviation_import_08_16 ?? 0) +
                                            ($journal->negative_deviation_import_16_24 ?? 0);

                                        $totalPosResolution =
                                            ($journal->positive_resolution_00_08 ?? 0) +
                                            ($journal->positive_resolution_08_16 ?? 0) +
                                            ($journal->positive_resolution_16_24 ?? 0);

                                        $totalNegResolution =
                                            ($journal->negative_resolution_00_08 ?? 0) +
                                            ($journal->negative_resolution_08_16 ?? 0) +
                                            ($journal->negative_resolution_16_24 ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $journal->powerPlant->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($journal->date)->format('Y-m-d') }}
                                        </td>
                                        <td class="text-end">{{ number_format($journal->processed_amount ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($journal->distribution_amount ?? 0, 2) }}</td>
                                        <td class="text-end">{{ number_format($journal->internal_demand ?? 0, 2) }}</td>
                                        <td class="text-center">{{ number_format($journal->percent ?? 0, 2) }}</td>

                                        <td class="text-end text-success fw-semibold">
                                            {{ number_format($totalPosDeviation, 2) }}</td>
                                        <td class="text-end text-danger fw-semibold">
                                            {{ number_format($totalNegDeviationSpot, 2) }}</td>
                                        <td class="text-end text-danger fw-semibold">
                                            {{ number_format($totalNegDeviationImport, 2) }}</td>
                                        <td class="text-end text-primary fw-semibold">
                                            {{ number_format($totalPosResolution, 2) }}</td>
                                        <td class="text-end text-warning fw-semibold">
                                            {{ number_format($totalNegResolution, 2) }}</td>

                                        <td class="text-end">
                                            {{ $journal->by_consumption_growth ? number_format($journal->by_consumption_growth, 2) : '-' }}
                                        </td>
                                        <td class="text-end">
                                            {{ $journal->by_other_station_issue ? number_format($journal->by_other_station_issue, 2) : '-' }}
                                        </td>
                                        <td>{{ $journal->dispatcher_name ?? '-' }}</td>

                                        <td class="text-center">
                                            <div class="btn-list justify-content-center">
                                                <a href="{{ route('daily-balance-journals.edit', $journal->id) }}"
                                                    class="btn btn-warning btn-icon" title="Засах">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <form action="{{ route('daily-balance-journals.destroy', $journal->id) }}"
                                                    method="POST" class="d-inline" onsubmit="return confirm('Устгах уу?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-icon" title="Устгах">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center text-muted py-4">
                                            <i class="ti ti-inbox ti-2x mb-2"></i>
                                            <p class="mb-0">Мэдээлэл олдсонгүй</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="text-muted small">
                            Нийт: {{ $journals->total() }} бичлэг
                        </div>
                        <div>
                            {{ $journals->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table th,
        .table td {
            font-size: 0.75rem;
        }

        .btn-icon {
            padding: 0.25rem 0.35rem;
        }
    </style>
@endsection
