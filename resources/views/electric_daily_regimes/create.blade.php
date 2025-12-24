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
                        <label>Горимоор өгсөн Pmax <small class="text-muted">(авто)</small></label>
                        <input type="number" step="0.01" name="pmax" id="pmax" class="form-control" readonly
                            style="background-color: #f0f0f0;">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Горимоор өгсөн Pmin <small class="text-muted">(авто)</small></label>
                        <input type="number" step="0.01" name="pmin" id="pmin" class="form-control" readonly
                            style="background-color: #f0f0f0;">
                    </div>
                </div>

                <h5 class="mt-4">24 цагийн ачаалал (мВт)</h5>

                <!-- 1-8 цаг -->
                <div class="row">
                    @for ($i = 1; $i <= 8; $i++)
                        <div class="col col-lg-15 mb-2">
                            <label>Цаг {{ $i }}</label>
                            <input type="number" step="0.01" name="hour_{{ $i }}"
                                class="form-control hour-input" data-hour="{{ $i }}">
                        </div>
                    @endfor
                </div>

                <!-- 9-16 цаг -->
                <div class="row">
                    @for ($i = 9; $i <= 16; $i++)
                        <div class="col col-lg-15 mb-2">
                            <label>Цаг {{ $i }}</label>
                            <input type="number" step="0.01" name="hour_{{ $i }}"
                                class="form-control hour-input" data-hour="{{ $i }}">
                        </div>
                    @endfor
                </div>

                <!-- 17-24 цаг -->
                <div class="row">
                    @for ($i = 17; $i <= 24; $i++)
                        <div class="col col-lg-15 mb-2">
                            <label>Цаг {{ $i }}</label>
                            <input type="number" step="0.01" name="hour_{{ $i }}"
                                class="form-control hour-input" data-hour="{{ $i }}">
                        </div>
                    @endfor
                </div>

                <div class="mb-3 mt-3">
                    <label>Нийт үйлдвэрлэл (мян.кВт.ц) <small class="text-muted">(авто)</small></label>
                    <input type="number" step="0.001" name="total_mwh" id="total_mwh" class="form-control" readonly
                        style="background-color: #f0f0f0;">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hourInputs = document.querySelectorAll('.hour-input');
            const totalMwhInput = document.getElementById('total_mwh');
            const pmaxInput = document.getElementById('pmax');
            const pminInput = document.getElementById('pmin');

            function calculateValues() {
                let total = 0;
                let max = null;
                let min = null;
                let hasValues = false;

                hourInputs.forEach(input => {
                    const value = parseFloat(input.value);

                    if (!isNaN(value) && input.value !== '') {
                        hasValues = true;
                        total += value;

                        if (max === null || value > max) {
                            max = value;
                        }
                        if (min === null || value < min) {
                            min = value;
                        }
                    }
                });

                // Нийт үйлдвэрлэл тооцоолох
                if (hasValues) {
                    totalMwhInput.value = total.toFixed(3);
                } else {
                    totalMwhInput.value = '';
                }

                // Max болон Min тооцоолох
                if (max !== null) {
                    pmaxInput.value = max.toFixed(2);
                } else {
                    pmaxInput.value = '';
                }

                if (min !== null) {
                    pminInput.value = min.toFixed(2);
                } else {
                    pminInput.value = '';
                }
            }

            // Бүх цагийн оруулах талбар дээрListener нэмэх
            hourInputs.forEach(input => {
                input.addEventListener('input', calculateValues);
                input.addEventListener('change', calculateValues);
            });
        });
    </script>
@endsection
