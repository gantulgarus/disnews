@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-4">{{ $powerPlant->name }} — Тоноглолын төлөв бүртгэл</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Алдаа гарлаа!</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('daily-equipment-report.store') }}" method="POST">
                    @csrf

                    {{-- Станц --}}
                    <input type="hidden" name="power_plant_id" value="{{ $powerPlant->id }}">

                    <div class="mb-3">
                        <label class="form-label">Станц</label>
                        <input type="text" class="form-control" value="{{ $powerPlant->name }}" readonly>
                    </div>

                    {{-- Огноо --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Огноо</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}"
                            required>
                        @error('date')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Тоноглолын хүснэгт --}}
                    <div id="equipment-section" class="mt-4">
                        <h5 class="mb-3">Тоноглолын жагсаалт</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>№</th>
                                        <th>Төрөл</th>
                                        <th>Тоноглолын нэр</th>
                                        <th>Төлөв</th>
                                        <th>Тайлбар</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($equipments as $index => $equipment)
                                        @php
                                            $lastStatus = $lastEquipmentStatuses[$equipment->id]->status ?? '';
                                            $lastRemark = $lastEquipmentStatuses[$equipment->id]->remark ?? '';
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $equipment->type->name ?? '-' }}</td>
                                            <td>{{ $equipment->name }}</td>
                                            <td>
                                                <select name="equipments[{{ $index }}][status]" class="form-select"
                                                    required>
                                                    <option value="">-- Сонгох --</option>
                                                    <option value="Ажилд" {{ $lastStatus == 'Ажилд' ? 'selected' : '' }}>
                                                        Ажилд</option>
                                                    <option value="Бэлтгэлд"
                                                        {{ $lastStatus == 'Бэлтгэлд' ? 'selected' : '' }}>Бэлтгэлд</option>
                                                    <option value="Засварт"
                                                        {{ $lastStatus == 'Засварт' ? 'selected' : '' }}>Засварт</option>
                                                </select>
                                                <input type="hidden" name="equipments[{{ $index }}][equipment_id]"
                                                    value="{{ $equipment->id }}">
                                            </td>
                                            <td>
                                                <input type="text" name="equipments[{{ $index }}][remark]"
                                                    class="form-control"
                                                    value="{{ old("equipments.$index.remark", $lastRemark) }}"
                                                    placeholder="Тайлбар...">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Тоноглол олдсонгүй</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Станцын чадлын мэдээлэл --}}
                    <div id="power-section" class="mt-4">
                        <h5 class="mb-3">Станцын чадлын мэдээлэл</h5>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Одоогийн чадал (Р, МВт)</label>
                                <input type="number" step="0.01" name="p" class="form-control"
                                    value="{{ old('p', $lastPowerInfo->p ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Хамгийн их чадал (Рmax, МВт)</label>
                                <input type="number" step="0.01" name="p_max" class="form-control"
                                    value="{{ old('p_max', $lastPowerInfo->p_max ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Хамгийн бага чадал (Рmin, МВт)</label>
                                <input type="number" step="0.01" name="p_min" class="form-control"
                                    value="{{ old('p_min', $lastPowerInfo->p_min ?? '') }}">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Үндсэн тоноглолын засвар, гарсан доголдлын талаар</label>
                            <textarea name="main_equipment_remark" class="form-control" rows="3"
                                placeholder="Жишээ нь: Турбин №2 — засварт байна...">{{ old('main_equipment_remark', $lastPowerInfo->remark ?? '') }}</textarea>
                        </div>
                    </div>

                    <button class="btn btn-primary mt-4 px-4">
                        <i class="bi bi-save me-1"></i> Хадгалах
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
