@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

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
                    <a href="{{ route('thermo-daily-regimes.create') }}" class="btn btn-primary">
                        Нэмэх
                    </a>
                </div>

            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success m-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Өдрийн дулааны горим</h3>
                {{-- <a href="{{ route('thermo-daily-regimes.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Нэмэх
                </a>
                <a href="{{ route('thermo-daily-regimes.report') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Тайлан
                </a> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Огноо</th>
                                <th>Станц</th>
                                <th>Цагийн бүс</th>
                                <th>Температур</th>
                                <th>Q</th>
                                <th>Q нийт</th>
                                <th class="w-1">Үйлдэл</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($regimes as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->date }}</td>
                                    <td>{{ $r->powerPlant->name ?? '-' }}</td>
                                    <td><span class="badge bg-blue-lt">{{ $r->time_range }}</span></td>
                                    <td>{{ $r->temperature }}</td>
                                    <td>{{ $r->q }}</td>
                                    <td>{{ $r->q_total }}</td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('thermo-daily-regimes.edit', $r) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('thermo-daily-regimes.destroy', $r) }}" method="POST"
                                                onsubmit="return confirm('Устгах уу?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Мэдээлэл алга.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
