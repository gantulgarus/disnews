@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-3">Шинэ тоноглол нэмэх</h4>

        <form method="POST" action="{{ route('equipments.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Станц</label>
                <select name="power_plant_id" class="form-control" required>
                    <option value="">-- Сонгох --</option>
                    @foreach ($powerPlants as $plant)
                        <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Ангилал</label>
                <select name="equipment_type_id" class="form-control" required>
                    <option value="">-- Сонгох --</option>
                    @foreach ($equipmentTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Тоноглолын нэр</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Хадгалах</button>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
