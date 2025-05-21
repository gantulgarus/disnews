@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="page-title mb-4">Станцуудын төлөвийн мэдээлэл</h1>
        <a href="{{ route('power-plants.create') }}" class="btn btn-primary mb-3">Нэмэх</a>

        @foreach ($powerPlants as $powerPlant)
            <div class="power-plant-card position-relative">
                <h2 class="power-plant-name">{{ $powerPlant->name }} ({{ $powerPlant->short_name }})</h2>
                <a href="{{ route('power-plants.edit', $powerPlant->id) }}"
                    class="btn btn-danger btn-edit-power-plant">Засах</a>
                <a href="{{ route('power-plant-daily-reports.create', ['power_plant_id' => $powerPlant->id]) }}"
                    class="btn btn-success btn-add-report">
                    Төлвийн мэдээ оруулах
                </a>



                <!-- Зуухны мэдээлэл -->
                <div class="equipment-section">
                    <h3 class="section-title">Зуух</h3>
                    @foreach ($powerPlant->boilers as $boiler)
                        <div class="equipment-item">
                            <p class="equipment-name">Зуух №: <strong>{{ $boiler->name }}</strong></p>

                            <!-- Төлөвийн мэдээлэл -->
                            @php
                                $boilerReports = $dailyReports->where('boiler_id', $boiler->id);
                            @endphp
                            <ul class="status-list">
                                @foreach ($boilerReports as $report)
                                    <li class="status-item">
                                        <strong>{{ $report->report_date }}:</strong>
                                        <span class="status">{{ $report->status }}</span>
                                        @if ($report->notes)
                                            <br><em class="note">Тайлбар: {{ $report->notes }}</em>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                <!-- Турбингенераторын мэдээлэл -->
                <div class="equipment-section">
                    <h3 class="section-title">Турбингенератор</h3>
                    @foreach ($powerPlant->turbineGenerators as $turbine)
                        <div class="equipment-item">
                            <p class="equipment-name">Турбингенератор №: <strong>{{ $turbine->name }}</strong></p>

                            <!-- Төлөвийн мэдээлэл -->
                            @php
                                $turbineReports = $dailyReports->where('turbine_generator_id', $turbine->id);
                            @endphp
                            <ul class="status-list">
                                @foreach ($turbineReports as $report)
                                    <li class="status-item">
                                        <strong>{{ $report->report_date }}:</strong>
                                        <span class="status">{{ $report->status }}</span>
                                        @if ($report->notes)
                                            <br><em class="note">Тайлбар: {{ $report->notes }}</em>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection

<style>
    /* Загварыг сайжруулах */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-title {
        text-align: center;
        font-size: 2rem;
        margin-bottom: 30px;
    }

    .power-plant-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 30px;
    }

    .power-plant-name {
        font-size: 1.5rem;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .equipment-section {
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #2980b9;
        margin-bottom: 15px;
    }

    .equipment-item {
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .equipment-name {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .status-list {
        list-style-type: none;
        padding-left: 0;
    }

    .status-item {
        margin-bottom: 8px;
        font-size: 1rem;
    }

    .status {
        font-weight: bold;
        color: #27ae60;
    }

    .note {
        font-style: italic;
        color: #7f8c8d;
    }

    .power-plant-card {
        position: relative;
        /* Энэ div дотор байрлал тогтооно */
        padding-top: 2rem;
        /* Засах товчийг даруулах зай */
    }

    .edit-button {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    /* Шинэ байршлууд */
    .btn-edit-power-plant {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .btn-add-report {
        position: absolute;
        top: 10px;
        right: 120px;
        /* "Засах" товчоос зүүн талд байрлана */
    }
</style>
