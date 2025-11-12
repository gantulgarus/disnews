@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-3">Тоноглолын тайлан засах</h4>

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

        <form action="{{ route('daily-equipment-report.update', $powerPlant->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Станц сонгох</label>
                <select id="power_plant" name="power_plant_id" class="form-select" required>
                    <option value="">-- Сонгох --</option>
                    @foreach ($powerPlants as $plant)
                        <option value="{{ $plant->id }}" {{ $plant->id == $powerPlant->id ? 'selected' : '' }}>
                            {{ $plant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Огноо</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                @error('date')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <table class="table table-bordered mt-3" id="equipment-table">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Төрөл</th>
                        <th>Тоноглолын нэр</th>
                        <th>Төлөв</th>
                        <th>Тайлбар</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($powerPlant->equipmentStatuses as $index => $eqStatus)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $eqStatus->equipment->type->name ?? '-' }}</td>
                            <td>{{ $eqStatus->equipment->name }}</td>
                            <td>
                                <select name="equipments[{{ $index }}][status]" class="form-select" required>
                                    <option value="Ажилд" {{ $eqStatus->status === 'Ажилд' ? 'selected' : '' }}>Ажилд
                                    </option>
                                    <option value="Бэлтгэлд" {{ $eqStatus->status === 'Бэлтгэлд' ? 'selected' : '' }}>
                                        Бэлтгэлд</option>
                                    <option value="Засварт" {{ $eqStatus->status === 'Засварт' ? 'selected' : '' }}>Засварт
                                    </option>
                                </select>
                                <input type="hidden" name="equipments[{{ $index }}][equipment_id]"
                                    value="{{ $eqStatus->equipment_id }}">
                            </td>
                            <td>
                                <input type="text" name="equipments[{{ $index }}][remark]"
                                    value="{{ $eqStatus->remark }}" class="form-control">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="power-section" class="mt-4">
                <h5>Станцын чадлын мэдээлэл</h5>
                @php $powerInfo = $powerPlant->powerInfos->first(); @endphp
                <div class="row">
                    <div class="col-md-4">
                        <label>Р (МВт)</label>
                        <input type="number" step="0.01" name="p" class="form-control"
                            value="{{ $powerInfo->p ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label>Р max (МВт)</label>
                        <input type="number" step="0.01" name="p_max" class="form-control"
                            value="{{ $powerInfo->p_max ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label>Р min (МВт)</label>
                        <input type="number" step="0.01" name="p_min" class="form-control"
                            value="{{ $powerInfo->p_min ?? '' }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label>Үндсэн тоноглолын засвар, гарсан доголдлын талаар</label>
                    <textarea name="main_equipment_remark" class="form-control" rows="3">{{ $powerInfo->remark ?? '' }}</textarea>
                </div>
            </div>

            <button class="btn btn-primary mt-4">Шинэчлэх</button>
        </form>
    </div>

    <script>
        document.getElementById('power_plant').addEventListener('change', function() {
            const powerPlantId = this.value;
            const table = document.getElementById('equipment-table').querySelector('tbody');

            if (!powerPlantId) {
                table.innerHTML = '';
                return;
            }

            fetch(`/get-equipments/${powerPlantId}`)
                .then(res => res.json())
                .then(data => {
                    table.innerHTML = '';
                    data.forEach((eq, index) => {
                        table.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${eq.type?.name ?? '-'}</td>
                    <td>${eq.name}</td>
                    <td>
                        <select name="equipments[${index}][status]" class="form-select" required>
                            <option value="Ажилд">Ажилд</option>
                            <option value="Бэлтгэлд">Бэлтгэлд</option>
                            <option value="Засварт">Засварт</option>
                        </select>
                        <input type="hidden" name="equipments[${index}][equipment_id]" value="${eq.id}">
                    </td>
                    <td><input type="text" name="equipments[${index}][remark]" class="form-control"></td>
                </tr>
                `;
                    });
                });
        });
    </script>
@endsection
