@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Импорт / Экспорт - Засах</h3>

        <form action="{{ route('daily-balance-import-exports.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Станц</label>
                <select name="power_plant_id" class="form-control">
                    @foreach ($plants as $plant)
                        <option value="{{ $plant->id }}" {{ $item->power_plant_id == $plant->id ? 'selected' : '' }}>
                            {{ $plant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Огноо</label>
                <input type="date" name="date" value="{{ $item->date }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Импорт (MWh)</label>
                <input type="number" step="0.01" name="import" value="{{ $item->import }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Экспорт (MWh)</label>
                <input type="number" step="0.01" name="export" value="{{ $item->export }}" class="form-control">
            </div>

            <button class="btn btn-primary">Хадгалах</button>
        </form>
    </div>
@endsection
