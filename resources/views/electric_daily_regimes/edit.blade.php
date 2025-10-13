@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>Өдөр тутмын горим засах</h3>

        <form action="{{ route('electric_daily_regimes.update', $electricDailyRegime->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Станц</label>
                <select name="power_plant_id" class="form-select">
                    @foreach ($powerPlants as $plant)
                        <option value="{{ $plant->id }}"
                            {{ $electricDailyRegime->power_plant_id == $plant->id ? 'selected' : '' }}>
                            {{ $plant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Огноо</label>
                <input type="date" name="date" value="{{ $electricDailyRegime->date->format('Y-m-d') }}"
                    class="form-control">
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Техникийн Pmax</label>
                    <input type="number" step="0.01" name="technical_pmax"
                        value="{{ $electricDailyRegime->technical_pmax }}" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Техникийн Pmin</label>
                    <input type="number" step="0.01" name="technical_pmin"
                        value="{{ $electricDailyRegime->technical_pmin }}" class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Горимоор өгсөн Pmax</label>
                    <input type="number" step="0.01" name="pmax" value="{{ $electricDailyRegime->pmax }}"
                        class="form-control">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Горимоор өгсөн Pmin</label>
                    <input type="number" step="0.01" name="pmin" value="{{ $electricDailyRegime->pmin }}"
                        class="form-control">
                </div>
            </div>

            <h5 class="mt-4">24 цагийн ачаалал (мВт)</h5>
            <div class="row">
                @for ($i = 1; $i <= 24; $i++)
                    <div class="col-md-2 mb-2">
                        <label>Цаг {{ $i }}</label>
                        <input type="number" step="0.01" name="hour_{{ $i }}"
                            value="{{ $electricDailyRegime->{'hour_' . $i} }}" class="form-control">
                    </div>
                @endfor
            </div>

            <div class="mb-3 mt-3">
                <label>Нийт үйлдвэрлэл (мян.кВт.ц)</label>
                <input type="number" step="0.001" name="total_mwh" value="{{ $electricDailyRegime->total_mwh }}"
                    class="form-control">
            </div>

            <div class="mb-3">
                <label>Тайлбар</label>
                <textarea name="note" class="form-control" rows="2">{{ $electricDailyRegime->note }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Шинэчлэх</button>
            <a href="{{ route('electric_daily_regimes.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
