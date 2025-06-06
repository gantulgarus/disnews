@php
    $defaultDateTime = old(
        'entry_date_time',
        isset($journal) ? $journal->entry_date_time->format('Y-m-d\TH:i') : \Carbon\Carbon::now()->format('Y-m-d\TH:i'),
    );
@endphp

<div class="mb-3">
    <label>Огноо, цаг</label>
    <input type="datetime-local" name="entry_date_time" class="form-control" value="{{ $defaultDateTime }}" required>
</div>


<div class="mb-3">
    <label>Цагийн хүрээ</label>
    <select name="time_range" class="form-control" required>
        @php
            $ranges = ['00:00 оос 08:00', '08:00 оос 16:00', '16:00 оос 24:00'];
            $selectedTimeRange = old('time_range', $journal->time_range ?? '');
        @endphp
        @foreach ($ranges as $range)
            <option value="{{ $range }}" {{ $selectedTimeRange == $range ? 'selected' : '' }}>
                {{ $range }}
            </option>
        @endforeach
    </select>
</div>


<div class="mb-3">
    <label>Станц</label>
    <select name="power_plant_id" class="form-control" required>
        <option value="">Сонгоно уу</option>
        @foreach ($powerPlants as $plant)
            <option value="{{ $plant->id }}"
                {{ old('power_plant_id', $journal->power_plant_id ?? '') == $plant->id ? 'selected' : '' }}>
                {{ $plant->name }}
            </option>
        @endforeach
    </select>
</div>

@php
    $fields = [
        'processed_amount' => 'Боловсруулалт',
        'distribution_amount' => 'Түгээлт',
        'internal_demand' => 'Д/Хэрэгцээ',
        'percent' => '%',
        'positive_deviation' => '+ зөрчил',
        'negative_deviation_spot' => '- зөрчил (спот)',
        'negative_deviation_import' => '- зөрчил (импорт)',
        'positive_resolution' => '+ шийд',
        'negative_resolution' => '- шийд',
        'deviation_reason' => 'Зөрчлийн шалтгаан',
        'by_consumption_growth' => 'Хэрэглээний өсөлтөөр',
        'by_other_station_issue' => 'Бусад станцын доголдлоор',
        'dispatcher_name' => 'Диспетчер',
    ];
@endphp

@foreach ($fields as $name => $label)
    <div class="mb-3">
        <label>{{ $label }}</label>
        @if (str_contains($name, 'reason') || str_contains($name, 'dispatcher'))
            <input type="text" name="{{ $name }}" class="form-control"
                value="{{ old($name, $journal->$name ?? '') }}">
        @else
            <input type="number" step="any" name="{{ $name }}" class="form-control"
                value="{{ old($name, $journal->$name ?? '') }}">
        @endif
    </div>
@endforeach
