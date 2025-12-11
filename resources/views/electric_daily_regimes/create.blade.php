@extends('layouts.admin')

@section('content')
    <div class="container card mt-4">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>⚠️ Алдаа:</strong>
                <ul class="mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('electric_daily_regimes.store') }}" method="POST">
            @csrf

            <div class="card-header">
                <h3>Өдөр тутмын горимын мэдээ нэмэх</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Станц</label>
                    <select name="power_plant_id" class="form-select">
                        @foreach ($powerPlants as $plant)
                            <option value="{{ $plant->id }}">{{ $plant->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Огноо</label>
                    <input type="date" name="date" class="form-control"
                        value="{{ old('date', now()->format('Y-m-d')) }}">
                </div>


                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Техникийн Pmax</label>
                        <input type="number" step="0.01" name="technical_pmax" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Техникийн Pmin</label>
                        <input type="number" step="0.01" name="technical_pmin" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Горимоор өгсөн Pmax</label>
                        <input type="number" step="0.01" name="pmax" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Горимоор өгсөн Pmin</label>
                        <input type="number" step="0.01" name="pmin" class="form-control">
                    </div>
                </div>

                <h5 class="mt-4">24 цагийн ачаалал (мВт)</h5>
                <div class="row">
                    @for ($i = 1; $i <= 24; $i++)
                        <div class="col-md-2 mb-2">
                            <label>Цаг {{ $i }}</label>
                            <input type="number" step="0.01" name="hour_{{ $i }}" class="form-control">
                        </div>
                    @endfor
                </div>

                <div class="mb-3 mt-3">
                    <label>Нийт үйлдвэрлэл (мян.кВт.ц)</label>
                    <input type="number" step="0.001" name="total_mwh" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Тайлбар</label>
                    <textarea name="note" class="form-control" rows="2"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Хадгалах</button>
                <a href="{{ route('electric_daily_regimes.index') }}" class="btn btn-secondary">Буцах</a>
            </div>
        </form>
    </div>
@endsection
