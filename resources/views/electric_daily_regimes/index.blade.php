@extends('layouts.admin')

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <!-- Page header -->
            <div class="page-header d-print-none mb-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            Хоногийн горим төлөвлөлт
                        </h2>
                    </div>
                    <div class="col-auto ms-auto">
                        <a href="{{ route('electric_daily_regimes.create') }}" class="btn btn-primary">
                            Нэмэх
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success message -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Table -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped table-hover card-table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Огноо</th>
                                <th>Станц</th>
                                <th>TPmax</th>
                                <th>TPmin</th>
                                <th>Pmax</th>
                                <th>Pmin</th>
                                @for ($i = 1; $i <= 24; $i++)
                                    <th>{{ $i }}</th>
                                @endfor
                                <th>Нийт</th>
                                <th>Үйлдэл</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($regimes as $regime)
                                <tr>
                                    <td>{{ $regime->date->format('yy-m-d') }}</td>
                                    <td>{{ $regime->powerPlant->name ?? '' }}</td>
                                    <td>{{ number_format($regime->technical_pmax, 0) }}</td>
                                    <td>{{ number_format($regime->technical_pmin, 0) }}</td>
                                    <td>{{ number_format($regime->pmax, 0) }}</td>
                                    <td>{{ number_format($regime->pmin, 0) }}</td>

                                    @for ($i = 1; $i <= 24; $i++)
                                        <td>{{ number_format($regime->{'hour_' . $i} ?? 0, 0) }}</td>
                                    @endfor
                                    <td>{{ number_format($regime->total_mwh, 0) }}</td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('electric_daily_regimes.edit', $regime) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('electric_daily_regimes.destroy', $regime) }}"
                                                method="POST" onsubmit="return confirm('Устгах уу?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-2">
                {{ $regimes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
