@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Импорт / Экспорт - Шинэ бичлэг</h3>

        <form action="{{ route('daily-balance-import-exports.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Станц</label>
                <select name="power_plant_id" class="form-control">
                    @foreach ($plants as $plant)
                        <option value="{{ $plant->id }}" {{ old('power_plant_id') == $plant->id ? 'selected' : '' }}>
                            {{ $plant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Огноо</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Импорт (MWh)</label>
                <input type="number" step="0.001" name="import" value="{{ old('import') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Экспорт (MWh)</label>
                <input type="number" step="0.001" name="export" value="{{ old('export') }}" class="form-control">
            </div>

            <button class="btn btn-primary">Хадгалах</button>
        </form>
    </div>
@endsection
