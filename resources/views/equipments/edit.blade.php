@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-3">Тоноглол засах</h4>

        <form method="POST" action="{{ route('equipments.update', $equipment->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Станц</label>
                <select name="power_plant_id" class="form-control" required>
                    @foreach ($powerPlants as $plant)
                        <option value="{{ $plant->id }}" {{ $equipment->power_plant_id == $plant->id ? 'selected' : '' }}>
                            {{ $plant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ангилал</label>
                <select name="equipment_type_id" class="form-control" required>
                    @foreach ($equipmentTypes as $type)
                        <option value="{{ $type->id }}"
                            {{ $equipment->equipment_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Тоноглолын нэр</label>
                <input type="text" name="name" value="{{ $equipment->name }}" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Шинэчлэх</button>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
