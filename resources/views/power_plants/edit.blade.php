@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="page-title mb-4">Станцын мэдээлэл засах</h1>

        <form action="{{ route('power-plants.update', $powerPlant->id) }}" method="POST" class="card">
            @csrf
            @method('PUT')

            <div class="card-body">

                <!-- Станцын нэр -->
                <div class="mb-3">
                    <label class="form-label" for="name">Станцын нэр</label>
                    <input type="text" name="name" id="name" class="form-control"
                        value="{{ old('name', $powerPlant->name) }}" required>
                </div>

                <!-- Богино нэр -->
                <div class="mb-3">
                    <label class="form-label" for="short_name">Богино нэр</label>
                    <input type="text" name="short_name" id="short_name" class="form-control"
                        value="{{ old('short_name', $powerPlant->short_name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Станцын төрөл</label>
                    <select name="power_plant_type_id" class="form-select" required>
                        <option value="">Сонгох</option>
                        @foreach ($powerPlantTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('power_plant_type_id', $powerPlant->power_plant_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="region" class="form-label">Бүс</label>
                    <select name="region" id="region" class="form-select">
                        <option value="ТБЭХС" {{ old('region', $powerPlant->region ?? '') == 'ТБЭХС' ? 'selected' : '' }}>
                            ТБЭХС</option>
                        <option value="ДБЭХС" {{ old('region', $powerPlant->region ?? '') == 'ДБЭХС' ? 'selected' : '' }}>
                            ДБЭХС</option>
                        <option value="АУЭХС" {{ old('region', $powerPlant->region ?? '') == 'АУЭХС' ? 'selected' : '' }}>
                            АУЭХС</option>
                        <option value="ББЭХС" {{ old('region', $powerPlant->region ?? '') == 'ББЭХС' ? 'selected' : '' }}>
                            ББЭХС</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Эрэмбэ</label>
                    <input type="text" name="Order" class="form-control"
                        value="{{ old('Order', $powerPlant->Order) }}" required>
                </div>

                <!-- Save Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-device-floppy"></i> Хадгалах
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
