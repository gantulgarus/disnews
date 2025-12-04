@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="page-header d-print-none mb-3">
            <div class="row align-items-center">
                <div class="col-auto ms-auto">
                    <a href="{{ route('power-distribution-works.create') }}"
                        class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i>
                        Шинэ ажил нэмэх
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h2 class="page-title">⚡ Захиалгат ажлууд</h2>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('power-distribution-works.index') }}" class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">ТЗЭ</label>
                        <input type="text" name="tze" value="{{ request('tze') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Засварын ажил</label>
                        <input type="text" name="repair_work" value="{{ request('repair_work') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Огноо</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary me-2">
                            <i class="ti ti-search me-1"></i> Хайх
                        </button>

                        <a href="{{ route('power-distribution-works.index') }}" class="btn btn-secondary">
                            Цэвэрлэх
                        </a>
                    </div>

                </form>
                <table class="table table-vcenter card-table table-striped mt-4">
                    <thead class="text-muted">
                        <tr>
                            <th class="w-1">№</th>
                            <th>ТЗЭ</th>
                            <th>Засварын ажлын утга</th>
                            <th>Тайлбар</th>
                            <th>Хязгаарласан эрчим хүч (кВт)</th>
                            <th>Огноо</th>
                            <th>Телеграм</th>
                            <th class="text-center">Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($works as $index => $work)
                            <tr>
                                {{-- Хуудасны дугаарлалт хадгалах --}}
                                <td>{{ $works->firstItem() + $index }}</td>
                                <td>{{ $work->tze }}</td>
                                <td>{{ $work->repair_work }}</td>
                                <td class="text-muted">{{ $work->description ?: '—' }}</td>
                                <td>{{ $work->restricted_energy ?: '—' }}</td>
                                <td>{{ \Carbon\Carbon::parse($work->date)->format('Y.m.d') }}</td>
                                <td>
                                    @if ($work->send_telegram)
                                        <span class="badge bg-success text-white">Илгээгдсэн</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Илгээгдээгүй</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-list justify-content-center">
                                        <a href="{{ route('power-distribution-works.edit', $work) }}"
                                            class="btn btn-sm btn-warning d-inline-flex align-items-center">
                                            <i class="ti ti-edit me-1"></i>
                                        </a>

                                        <form action="{{ route('power-distribution-works.destroy', $work) }}"
                                            method="POST" onsubmit="return confirm('Устгах уу?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger d-inline-flex align-items-center">
                                                <i class="ti ti-trash me-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Одоогоор бүртгэл байхгүй байна.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-start">
                {{ $works->links() }}
            </div>
        </div>
    </div>
    </div>
@endsection
