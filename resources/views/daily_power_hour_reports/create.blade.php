@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Хоногийн ачааллын цагийн мэдээ нэмэх</h3>
        <a href="{{ route('daily_power_hour_reports.index') }}" class="btn btn-secondary">← Буцах</a>
    </div>

    <form action="{{ route('daily_power_hour_reports.store') }}" method="POST">
        @csrf

        {{-- Цахилгаан станц --}}
        <div class="mb-3">
            <label class="form-label">Цахилгаан станц</label>
            <input type="text" class="form-control" value="{{ $plant->name }}" readonly>
            <input type="hidden" name="power_plant_id" value="{{ $plant->id }}">
        </div>

        {{-- Огноо ба цаг --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="date" class="form-label">Огноо</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="time" class="form-label">Цаг</label>
                <select name="time" id="time" class="form-select" required>
                    <option value="">-- Сонгоно уу --</option>
                    @for ($h = 1; $h <= 24; $h++)
                        @php
                            $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <option value="{{ $hour }}:00">{{ $hour }}:00</option>
                    @endfor
                </select>
            </div>

        </div>

        {{-- Тоноглол бүрийн ачаалал --}}
        <h5 class="mt-4 mb-2 text-primary">Тоноглол тус бүрийн ачааллын утга (MW)</h5>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>№</th>
                    <th>Тоноглол</th>
                    <th>Ачааллын утга (MW)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipments as $index => $equipment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $equipment->power_equipment }}</td>
                    <td>
                        <input type="hidden" name="equipments[{{ $index }}][id]" value="{{ $equipment->id }}">
                        <input type="number" step="0.01" name="equipments[{{ $index }}][power_value]" class="form-control" placeholder="Жишээ: 26.5">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary">💾 Хадгалах</button>
        </div>
    </form>
</div>
@endsection
