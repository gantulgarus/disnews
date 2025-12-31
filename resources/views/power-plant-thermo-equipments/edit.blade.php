@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Термо тоног төхөөрөмж засах</h2>
                    <a href="{{ route('power-plant-thermo-equipments.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Буцах
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('power-plant-thermo-equipments.update', $powerPlantThermoEquipment) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="power_plant_id" class="form-label">Цахилгаан станц <span
                                        class="text-danger">*</span></label>
                                <select name="power_plant_id" id="power_plant_id"
                                    class="form-select @error('power_plant_id') is-invalid @enderror" required>
                                    <option value="">-- Сонгох --</option>
                                    @foreach ($powerPlants as $powerPlant)
                                        <option value="{{ $powerPlant->id }}"
                                            {{ old('power_plant_id', $powerPlantThermoEquipment->power_plant_id) == $powerPlant->id ? 'selected' : '' }}>
                                            {{ $powerPlant->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('power_plant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="code" class="form-label">Код <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code"
                                    class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code', $powerPlantThermoEquipment->code) }}"
                                    placeholder="P1, P2, T1, T2, GSUL гэх мэт" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Жишээ: P1, P2, T1, T2, GSUL, GNU, GUR</small>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Нэр <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $powerPlantThermoEquipment->name) }}"
                                    placeholder="Даралт P1, Температур T1 гэх мэт" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Жишээ: Даралт P1, Температур T1, Сүлжээний усны
                                    зарцуулалт</small>
                            </div>

                            <div class="mb-3">
                                <label for="unit" class="form-label">Хэмжих нэгж</label>
                                <input type="text" name="unit" id="unit"
                                    class="form-control @error('unit') is-invalid @enderror"
                                    value="{{ old('unit', $powerPlantThermoEquipment->unit) }}"
                                    placeholder="МПа, °C, т/ц гэх мэт">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Жишээ: МПа, °C, т/ц, кВт</small>
                            </div>

                            <div class="alert alert-info">
                                <small>
                                    <strong>Үүсгэсэн:</strong>
                                    {{ $powerPlantThermoEquipment->created_at->format('Y-m-d H:i') }}<br>
                                    <strong>Сүүлд засагдсан:</strong>
                                    {{ $powerPlantThermoEquipment->updated_at->format('Y-m-d H:i') }}
                                </small>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('power-plant-thermo-equipments.index') }}" class="btn btn-secondary">
                                    Цуцлах
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Шинэчлэх
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
