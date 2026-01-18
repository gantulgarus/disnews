@extends('layouts.admin')

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <!-- Page header -->
            <div class="page-header d-print-none mb-3">
                <div class="row align-items-center justify-content-between">

                    <div class="col-auto d-flex align-items-center gap-2">

                        <!-- Сараар хайх форм -->
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <input type="month" name="month" class="form-control"
                                value="{{ request('month', now()->format('Y-m')) }}" style="min-width: 150px;">

                            <button class="btn btn-info" type="submit">Хайх</button>

                        </form>

                        <!-- Нэмэх товч -->
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
                <div class="card-header">
                    <h4 class="card-title">Хоногийн горимын төлөвлөлт</h4>


                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-striped table-hover card-table table-sm compact-table">
                            <thead class="table-light">
                                <tr>
                                    <th>№</th>
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
                                    <th>Статус</th>
                                    <th>Үйлдэл</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($regimes as $regime)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $regime->date->format('Y-m-d') }}</td>
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
                                            @if ($regime->status == 'approved')
                                                <span class="badge bg-success text-white">Батлагдсан</span>
                                            @elseif($regime->status == 'rejected')
                                                <span class="badge bg-danger text-white">Буцаагдсан</span>
                                            @else
                                                <span class="badge bg-warning text-white">Хүлээгдэж буй</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $isRegimeLead = auth()->user()->permissionLevel?->code === 'REGIME_LEAD';
                                            @endphp
                                            {{-- Батлагдсан горимыг зөвхөн REGIME_LEAD засах/устгах эрхтэй --}}
                                            @if ($regime->status !== 'approved' || $isRegimeLead)
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
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .compact-table th,
        .compact-table td {
            font-size: 11px !important;
            padding: 4px 6px !important;
            white-space: nowrap;
            text-align: center;
        }

        .compact-table .badge {
            font-size: 10px !important;
            padding: 2px 6px !important;
        }

        .compact-table .btn-sm {
            padding: 2px 4px !important;
            font-size: 10px !important;
        }

        .compact-table .btn-list {
            gap: 2px !important;
        }
    </style>
@endsection
