@extends('layouts.admin')

@section('content')
    <div class="container-xl py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Өдрийн дулааны горим</h3>
                <a href="{{ route('thermo-daily-regimes.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Нэмэх
                </a>
                <a href="{{ route('thermo-daily-regimes.report') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Тайлан
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Цахилгаан станц</th>
                            <th>Огноо</th>
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
                                <td>{{ $r->id }}</td>
                                <td>{{ $r->powerPlant->name ?? '-' }}</td>
                                <td>{{ $r->date }}</td>
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

            <div class="card-footer">
                {{ $regimes->links() }}
            </div>
        </div>
    </div>
@endsection
