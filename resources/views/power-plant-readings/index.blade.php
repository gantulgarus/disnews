@extends('layouts.admin')

@section('content')
    <div class="page-wrapper">
        <div class="container-fluid py-4">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="page-title">Cүлжээний усны горим барилтын мэдээ</h2>
                <div class="btn-list">
                    <a href="{{ route('power-plant-readings.create') }}" class="btn btn-primary">
                        <i class="fas fa-keyboard"></i> Мэдээ бүртгэх
                    </a>
                </div>
            </div>

            {{-- Фильтер --}}
            <form method="GET" action="{{ route('power-plant-readings.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Огноо</label>
                    <input type="date" class="form-control" name="date" value="{{ $date }}"
                        onchange="this.form.submit()">
                </div>

                @if (!auth()->user()->mainPowerPlant)
                    <div class="col-md-4">
                        <label class="form-label">Станц</label>
                        <select class="form-select" name="power_plant_id" onchange="this.form.submit()">
                            <option value="">-- Бүгд --</option>
                            @foreach ($powerPlants as $plant)
                                <option value="{{ $plant->id }}" {{ $powerPlantId == $plant->id ? 'selected' : '' }}>
                                    {{ $plant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="col-md-4">
                        <label class="form-label">Станц</label>
                        <input type="text" class="form-control" value="{{ $powerPlants->first()->name }}" readonly>
                    </div>
                @endif

                <div class="col-md-2 align-self-end">
                    <a href="{{ route('power-plant-readings.temperature-charts') }}?date={{ $date }}"
                        class="btn btn-info w-100">
                        <i class="fas fa-chart-line"></i> График
                    </a>
                </div>
            </form>

            {{-- Мэдээлэл --}}
            @if ($readings && $readings->count() > 0)
                <div class="alert alert-success">
                    <strong>{{ $date }}</strong> өдрийн мэдээлэл: нийт <strong>{{ $readings->total() }}</strong>
                    утга
                </div>

                @foreach ($readings as $hour => $hourReadings)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-end align-items-center gap-2">
                            <strong>{{ $hour }} цагийн мэдээ</strong>
                            <a href="{{ route('power-plant-readings.edit') }}?power_plant_id={{ $powerPlantId }}&date={{ $date }}&hour={{ $hour }}"
                                class="btn btn-sm btn-warning">
                                <i class="icon icon-pencil"></i> Засах
                            </a>

                            {{-- Bulk Delete --}}
                            <form action="{{ route('power-plant-readings.destroyBulk') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Энэ цагийн бүх мэдээллийг устгах уу?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="power_plant_id" value="{{ $powerPlantId }}">
                                <input type="hidden" name="date" value="{{ $date }}">
                                <input type="hidden" name="hour" value="{{ $hour }}">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Устгах
                                </button>
                            </form>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @if (!auth()->user()->mainPowerPlant)
                                            <th>Станц</th>
                                        @endif
                                        <th>Код</th>
                                        <th>Тоног төхөөрөмж</th>
                                        <th class="text-end">Утга</th>
                                        <th>Нэгж</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $counter = 1; @endphp
                                    @foreach ($hourReadings as $reading)
                                        <tr>
                                            <td>{{ $counter++ }}</td>
                                            @if (!auth()->user()->mainPowerPlant)
                                                <td>{{ $reading->equipment->powerPlant->name ?? '-' }}</td>
                                            @endif
                                            <td><code>{{ $reading->equipment->code }}</code></td>
                                            <td>{{ $reading->equipment->name }}</td>
                                            <td class="text-end">{{ number_format($reading->value, 2) }}</td>
                                            <td>{{ $reading->equipment->unit ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $readings->appends(request()->except('page'))->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Мэдээлэл олдсонгүй. Өөр огноо сонгоно уу.
                </div>
            @endif

        </div>
    </div>
@endsection
