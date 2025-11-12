@extends('layouts.admin')

@section('title', 'Засах')

@section('content')
    <div class="page-header">
        <h2 class="page-title">Засах</h2>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('western_region_capacities.update', $westernRegionCapacity->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">P Max</label>
                    <input type="number" step="0.001" name="p_max" class="form-control"
                        value="{{ old('p_max', $westernRegionCapacity->p_max) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">P Min</label>
                    <input type="number" step="0.001" name="p_min" class="form-control"
                        value="{{ old('p_min', $westernRegionCapacity->p_min) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Импортын хамгийн их</label>
                    <input type="number" step="0.001" name="p_imp_max" class="form-control"
                        value="{{ old('p_imp_max', $westernRegionCapacity->p_imp_max) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Импортын хамгийн бага</label>
                    <input type="number" step="0.001" name="p_imp_min" class="form-control"
                        value="{{ old('p_imp_min', $westernRegionCapacity->p_imp_min) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Импорт авсан</label>
                    <input type="number" step="0.001" name="import_received" class="form-control"
                        value="{{ old('import_received', $westernRegionCapacity->import_received) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Импорт түгээсэн</label>
                    <input type="number" step="0.001" name="import_distributed" class="form-control"
                        value="{{ old('import_distributed', $westernRegionCapacity->import_distributed) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Огноо</label>
                    <input type="date" name="date" class="form-control"
                        value="{{ old('date', $westernRegionCapacity->date) }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Шинэчлэх</button>
                <a href="{{ route('western_region_capacities.index') }}" class="btn btn-secondary">Буцах</a>
            </form>
        </div>
    </div>
@endsection
