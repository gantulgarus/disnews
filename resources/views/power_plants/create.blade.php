@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <h1 class="page-title">Шинэ станц үүсгэх</h1>

        <form action="{{ route('power-plants.store') }}" method="POST" class="card">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Станцын нэр</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Товч нэр</label>
                    <input type="text" name="short_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Станцын төрөл</label>
                    <select name="power_plant_type_id" class="form-select" required>
                        <option value="" disabled selected>Сонгох</option>
                        @foreach ($powerPlantTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
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
                    <input type="text" name="Order" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="ti ti-device-floppy"></i> Хадгалах
                </button>
            </div>
        </form>
    </div>
@endsection
