@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Ачааллын тоноглол нэмэх </h3>
        <a href="{{ route('daily_power_equipments.index') }}" class="btn btn-secondary">← Буцах</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Анхаар!</strong> Алдааг засварлана уу? 
            <ul class="mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm p-4 rounded-3">
        <form action="{{ route('daily_power_equipments.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="power_plant_id" class="form-label fw-bold">Станц сонгох</label>
                <select name="power_plant_id" id="power_plant_id" class="form-select" required>
                    <option value="">сонгох</option>
                    @foreach ($powerPlants as $plant)
                        <option value="{{ $plant->id }}" {{ old('power_plant_id') == $plant->id ? 'selected' : '' }}>
                            {{ $plant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="power_equipment" class="form-label fw-bold">Тоноглол нэр</label>
                <input type="text" name="power_equipment" id="power_equipment" 
                       value="{{ old('power_equipment') }}" class="form-control" placeholder=" " required>
            </div>
             <div class="mb-3">
                <label for="equipment_name" class="form-label fw-bold">Тоноглол нэршил</label>
                <input type="text" name="equipment_name" id="equipment_name" 
                       value="{{ old('equipment_name') }}" class="form-control" placeholder=" " required>
            </div>


            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Хадгалах</button>
            </div>
        </form>
    </div>
</div>

@endsection
