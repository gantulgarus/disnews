@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Өдөр тутмын тайлан засах</h1>

        <form action="{{ route('power-plant-daily-reports.update', $powerPlantDailyReport) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="report_date" class="form-label">Тайлангийн огноо</label>
                <input type="date" name="report_date" class="form-control"
                    value="{{ old('report_date', $powerPlantDailyReport->report_date) }}" required>
            </div>

            <div class="mb-3">
                <label for="boiler_id" class="form-label">Зуух</label>
                <select name="boiler_id" class="form-control">
                    <option value="">-- Сонгох --</option>
                    @foreach ($boilers as $boiler)
                        <option value="{{ $boiler->id }}"
                            {{ $boiler->id == $powerPlantDailyReport->boiler_id ? 'selected' : '' }}>
                            {{ $boiler->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="turbine_generator_id" class="form-label">Турбин-генератор</label>
                <select name="turbine_generator_id" class="form-control">
                    <option value="">-- Сонгох --</option>
                    @foreach ($turbines as $turbine)
                        <option value="{{ $turbine->id }}"
                            {{ $turbine->id == $powerPlantDailyReport->turbine_generator_id ? 'selected' : '' }}>
                            {{ $turbine->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Төлөв</label>
                <select name="status" class="form-control" required>
                    <option value="">-- Сонгох --</option>
                    <option value="Ажилд" {{ $powerPlantDailyReport->status == 'Ажилд' ? 'selected' : '' }}>Ажилд</option>
                    <option value="Бэлтгэлд" {{ $powerPlantDailyReport->status == 'Бэлтгэлд' ? 'selected' : '' }}>Бэлтгэлд
                    </option>
                    <option value="Засварт" {{ $powerPlantDailyReport->status == 'Засварт' ? 'selected' : '' }}>Засварт
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Нэмэлт тайлбар</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $powerPlantDailyReport->notes) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Хадгалах</button>
            <a href="{{ route('power-plant-daily-reports.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
