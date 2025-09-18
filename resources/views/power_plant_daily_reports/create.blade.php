@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h4 class="mb-0">{{ $powerPlant->name }} ({{ $powerPlant->short_name }}) - Төлвийн мэдээ оруулах</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('power-plant-daily-reports.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="power_plant_id" value="{{ $powerPlant->id }}">

                    <h5 class="mt-3 mb-3">Зуух ба Турбины төлөв</h5>

                    <div class="row">
                        {{-- Зуух --}}
                        <div class="col-md-6">
                            <h6 class="text-danger">🔴 Зуух</h6>

                            @php
                                // $boilerWorking = old(
                                //     'boiler_working',
                                //     $lastReport ? json_decode($lastReport->boiler_working, true) : [],
                                // );
                                // $boilerPreparation = old(
                                //     'boiler_preparation',
                                //     $lastReport ? json_decode($lastReport->boiler_preparation, true) : [],
                                // );
                                // $boilerRepair = old(
                                //     'boiler_repair',
                                //     $lastReport ? json_decode($lastReport->boiler_repair, true) : [],
                                // );

                                $boilerWorking = (array) old(
                                    'boiler_working',
                                    $lastReport && $lastReport->boiler_working
                                        ? json_decode($lastReport->boiler_working, true)
                                        : [],
                                );
                                $boilerPreparation = (array) old(
                                    'boiler_preparation',
                                    $lastReport && $lastReport->boiler_preparation
                                        ? json_decode($lastReport->boiler_preparation, true)
                                        : [],
                                );
                                $boilerRepair = (array) old(
                                    'boiler_repair',
                                    $lastReport && $lastReport->boiler_repair
                                        ? json_decode($lastReport->boiler_repair, true)
                                        : [],
                                );

                            @endphp
                            <label class="form-label mt-2">Ажилд байгаа зуухнууд:</label>
                            <div class="mb-3">
                                @foreach ($powerPlant->boilers as $boiler)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="boiler_working[]"
                                            value="{{ $boiler->id }}" id="boiler_working_{{ $boiler->id }}"
                                            {{ in_array($boiler->id, $boilerWorking) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="boiler_working_{{ $boiler->id }}">{{ $boiler->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <label class="form-label mt-2">Бэлтгэлд байгаа зуухнууд:</label>
                            <div class="mb-3">
                                @foreach ($powerPlant->boilers as $boiler)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="boiler_preparation[]"
                                            value="{{ $boiler->id }}" id="boiler_preparation_{{ $boiler->id }}"
                                            {{ in_array($boiler->id, $boilerPreparation) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="boiler_preparation_{{ $boiler->id }}">{{ $boiler->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <label class="form-label mt-2">Засварт байгаа зуухнууд:</label>
                            <div class="mb-3">
                                @foreach ($powerPlant->boilers as $boiler)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="boiler_repair[]"
                                            value="{{ $boiler->id }}" id="boiler_repair_{{ $boiler->id }}"
                                            {{ in_array($boiler->id, $boilerRepair) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="boiler_repair_{{ $boiler->id }}">{{ $boiler->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Турбин --}}
                        <div class="col-md-6">
                            <h6 class="text-primary">🔵 Турбин</h6>

                            @php
                                $turbineWorking = (array) old(
                                    'turbine_working',
                                    $lastReport && $lastReport->turbine_working
                                        ? json_decode($lastReport->turbine_working, true)
                                        : [],
                                );
                                $turbinePreparation = (array) old(
                                    'turbine_preparation',
                                    $lastReport && $lastReport->turbine_preparation
                                        ? json_decode($lastReport->turbine_preparation, true)
                                        : [],
                                );
                                $turbineRepair = (array) old(
                                    'turbine_repair',
                                    $lastReport && $lastReport->turbine_repair
                                        ? json_decode($lastReport->turbine_repair, true)
                                        : [],
                                );
                            @endphp
                            <label class="form-label mt-2">Ажилд байгаа турбин:</label>
                            <div class="mb-3">
                                @foreach ($powerPlant->turbineGenerators as $turbine)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="turbine_working[]"
                                            value="{{ $turbine->id }}" id="turbine_working_{{ $turbine->id }}"
                                            {{ in_array($turbine->id, $turbineWorking) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="turbine_working_{{ $turbine->id }}">{{ $turbine->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <label class="form-label mt-2">Бэлтгэлд байгаа турбин:</label>
                            <div class="mb-3">
                                @foreach ($powerPlant->turbineGenerators as $turbine)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="turbine_preparation[]"
                                            value="{{ $turbine->id }}" id="turbine_preparation_{{ $turbine->id }}"
                                            {{ in_array($turbine->id, $turbinePreparation) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="turbine_preparation_{{ $turbine->id }}">{{ $turbine->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <label class="form-label mt-2">Засварт байгаа турбин:</label>
                            <div class="mb-3">
                                @foreach ($powerPlant->turbineGenerators as $turbine)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="turbine_repair[]"
                                            value="{{ $turbine->id }}" id="turbine_repair_{{ $turbine->id }}"
                                            {{ in_array($turbine->id, $turbineRepair) ? 'checked' : '' }}>
                                        <label class="form-check-label"
                                            for="turbine_repair_{{ $turbine->id }}">{{ $turbine->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="power">Чадал (МВт):</label>
                        <input type="number" step="0.1" name="power" class="form-control" id="power"
                            value="{{ old('power', $lastReport->power ?? '') }}">
                    </div>

                    <div class="form-group mt-3">
                        <label for="power_max">Чадал max (МВт):</label>
                        <input type="number" step="0.1" name="power_max" class="form-control" id="power_max"
                            value="{{ old('power_max', $lastReport->power_max ?? '') }}">
                    </div>

                    <div class="form-group mt-3">
                        <label for="notes">Үндсэн тоноглолын засвар, гарсан доголдол:</label>
                        <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Тайлбар оруулах...">{{ old('notes', $lastReport->notes ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">💾 Хадгалах</button>
                </form>
            </div>
        </div>
    </div>
@endsection

<style>
    .form-check-inline {
        margin-right: 1rem;
        margin-bottom: 0.5rem;
    }
</style>
