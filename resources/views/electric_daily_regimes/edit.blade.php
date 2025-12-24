@extends('layouts.admin')

@section('content')
    <div class="container card mt-4">

        <form action="{{ route('electric_daily_regimes.update', $electricDailyRegime->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-header">
                <h3>Өдөр тутмын горим засах</h3>
            </div>
            <div class="card-body">
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
                        <label>Горимоор өгсөн Pmax <small class="text-muted">(авто)</small></label>
                        <input type="number" step="0.01" name="pmax" id="pmax"
                            value="{{ $electricDailyRegime->pmax }}" class="form-control" readonly
                            style="background-color: #f0f0f0;">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Горимоор өгсөн Pmin <small class="text-muted">(авто)</small></label>
                        <input type="number" step="0.01" name="pmin" id="pmin"
                            value="{{ $electricDailyRegime->pmin }}" class="form-control" readonly
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
                                value="{{ $electricDailyRegime->{'hour_' . $i} }}" class="form-control hour-input">
                        </div>
                    @endfor
                </div>

                <!-- 9-16 цаг -->
                <div class="row">
                    @for ($i = 9; $i <= 16; $i++)
                        <div class="col col-lg-15 mb-2">
                            <label>Цаг {{ $i }}</label>
                            <input type="number" step="0.01" name="hour_{{ $i }}"
                                value="{{ $electricDailyRegime->{'hour_' . $i} }}" class="form-control hour-input">
                        </div>
                    @endfor
                </div>

                <!-- 17-24 цаг -->
                <div class="row">
                    @for ($i = 17; $i <= 24; $i++)
                        <div class="col col-lg-15 mb-2">
                            <label>Цаг {{ $i }}</label>
                            <input type="number" step="0.01" name="hour_{{ $i }}"
                                value="{{ $electricDailyRegime->{'hour_' . $i} }}" class="form-control hour-input">
                        </div>
                    @endfor
                </div>

                <div class="mb-3 mt-3">
                    <label>Нийт үйлдвэрлэл (мян.кВт.ц) <small class="text-muted">(авто)</small></label>
                    <input type="number" step="0.001" name="total_mwh" id="total_mwh"
                        value="{{ $electricDailyRegime->total_mwh }}" class="form-control" readonly
                        style="background-color: #f0f0f0;">
                </div>

                <div class="mb-3">
                    <label>Тайлбар</label>
                    <textarea name="note" class="form-control" rows="2">{{ $electricDailyRegime->note }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">Шинэчлэх</button>
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

            // Бүх цагийн оруулах талбар дээр Listener нэмэх
            hourInputs.forEach(input => {
                input.addEventListener('input', calculateValues);
                input.addEventListener('change', calculateValues);
            });

            // Хуудас ачааллагдах үед анхны тооцоолол хийх
            calculateValues();
        });
    </script>
@endsection
