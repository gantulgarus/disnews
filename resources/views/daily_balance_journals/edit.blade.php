@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Тооцооны журнал засах</h2>
            <a href="{{ route('daily-balance-journals.index') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left"></i> Буцах
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-sm py-2">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('daily-balance-journals.update', $dailyBalanceJournal->id) }}" method="POST"
            class="compact-form">
            @csrf
            @method('PUT')

            <div class="row">

                <!-- Үндсэн мэдээлэл -->
                <div class="card card-compact mb-2">
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label-sm">Станц</label>
                                <input type="text" step="0.001" name="power_plant_id" id="power_plant_id"
                                    class="form-control form-control-sm"
                                    value="{{ $dailyBalanceJournal->powerPlant->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-sm">Огноо</label>
                                <input type="date" name="date" class="form-control form-control-sm"
                                    value="{{ old('date', $dailyBalanceJournal->date) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Үндсэн үзүүлэлтүүд -->
                <div class="card card-compact mb-2">
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <label class="form-label-sm">Боловсруулалт</label>
                                <input type="number" step="0.001" name="processed_amount" id="processed_amount"
                                    class="form-control form-control-sm"
                                    value="{{ old('processed_amount', $dailyBalanceJournal->processed_amount) }}">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label-sm">Түгээлт</label>
                                <input type="number" step="0.001" name="distribution_amount" id="distribution_amount"
                                    class="form-control form-control-sm"
                                    value="{{ old('distribution_amount', $dailyBalanceJournal->distribution_amount) }}">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label-sm">Дотоод хэрэгцээ</label>
                                <input type="number" step="0.001" name="internal_demand" id="internal_demand"
                                    class="form-control form-control-sm"
                                    value="{{ old('internal_demand', $dailyBalanceJournal->internal_demand) }}" readonly>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label-sm">Хувь (%)</label>
                                <input type="number" step="0.01" name="percent" id="percent"
                                    class="form-control form-control-sm"
                                    value="{{ old('percent', $dailyBalanceJournal->percent) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Цагийн хуваарь -->
                <div class="card card-compact mb-2">
                    <div class="card-body p-2">
                        @foreach ([['00:00-08:00', '00_08'], ['08:00-16:00', '08_16'], ['16:00-24:00', '16_24']] as $timeSlot)
                            <div class="time-block {{ !$loop->last ? 'mb-2' : '' }}">
                                <div class="time-header">{{ $timeSlot[0] }}</div>
                                <div class="row g-1">
                                    <div class="col-6 col-lg">
                                        <label class="form-label-xs">+ зөрчил</label>
                                        <input type="number" step="0.01" name="positive_deviation_{{ $timeSlot[1] }}"
                                            class="form-control form-control-xs"
                                            value="{{ old('positive_deviation_' . $timeSlot[1], $dailyBalanceJournal->{'positive_deviation_' . $timeSlot[1]}) }}">
                                    </div>
                                    <div class="col-6 col-lg">
                                        <label class="form-label-xs">- зөрчил (Спот)</label>
                                        <input type="number" step="0.01"
                                            name="negative_deviation_spot_{{ $timeSlot[1] }}"
                                            class="form-control form-control-xs"
                                            value="{{ old('negative_deviation_spot_' . $timeSlot[1], $dailyBalanceJournal->{'negative_deviation_spot_' . $timeSlot[1]}) }}">
                                    </div>
                                    <div class="col-6 col-lg">
                                        <label class="form-label-xs">- зөрчил (Импорт)</label>
                                        <input type="number" step="0.01"
                                            name="negative_deviation_import_{{ $timeSlot[1] }}"
                                            class="form-control form-control-xs"
                                            value="{{ old('negative_deviation_import_' . $timeSlot[1], $dailyBalanceJournal->{'negative_deviation_import_' . $timeSlot[1]}) }}">
                                    </div>
                                    <div class="col-6 col-lg">
                                        <label class="form-label-xs">+ шийд</label>
                                        <input type="number" step="0.01" name="positive_resolution_{{ $timeSlot[1] }}"
                                            class="form-control form-control-xs"
                                            value="{{ old('positive_resolution_' . $timeSlot[1], $dailyBalanceJournal->{'positive_resolution_' . $timeSlot[1]}) }}">
                                    </div>
                                    <div class="col-6 col-lg">
                                        <label class="form-label-xs">- шийд</label>
                                        <input type="number" step="0.01"
                                            name="negative_resolution_{{ $timeSlot[1] }}"
                                            class="form-control form-control-xs"
                                            value="{{ old('negative_resolution_' . $timeSlot[1], $dailyBalanceJournal->{'negative_resolution_' . $timeSlot[1]}) }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Нэмэлт мэдээлэл -->
                <div class="card card-compact mb-2">
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <label class="form-label-sm">- зөрчил авсан шалтгаан</label>
                                <input type="text" name="deviation_reason" class="form-control form-control-sm"
                                    value="{{ old('deviation_reason', $dailyBalanceJournal->deviation_reason) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Хэрэглээний өсөлтөөр</label>
                                <input type="number" step="0.01" name="by_consumption_growth"
                                    class="form-control form-control-sm"
                                    value="{{ old('by_consumption_growth', $dailyBalanceJournal->by_consumption_growth) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Бусад станцын доголдлоор</label>
                                <input type="number" step="0.01" name="by_other_station_issue"
                                    class="form-control form-control-sm"
                                    value="{{ old('by_other_station_issue', $dailyBalanceJournal->by_other_station_issue) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Диспетчер</label>
                                <input type="text" name="dispatcher_name" class="form-control form-control-sm"
                                    value="{{ old('dispatcher_name', $dailyBalanceJournal->dispatcher_name) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="ti ti-device-floppy"></i> Хадгалах
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- CSS хэсэг --}}
    <style>
        .compact-form {
            font-size: 0.875rem;
        }

        .card-compact {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-label-sm {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #495057;
        }

        .form-label-xs {
            font-size: 0.7rem;
            font-weight: 500;
            margin-bottom: 0.15rem;
            color: #6c757d;
        }

        .form-control-sm,
        .form-select-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.813rem;
            height: calc(1.5em + 0.5rem + 2px);
        }

        .form-control-xs {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
            height: calc(1.5em + 0.4rem + 2px);
        }

        .time-block {
            background-color: #f8f9fa;
            padding: 0.5rem;
            border-radius: 0.25rem;
        }

        .time-header {
            font-size: 0.75rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid #dee2e6;
        }
    </style>

    {{-- JS тооцоолол --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const processedInput = document.getElementById('processed_amount');
            const distributionInput = document.getElementById('distribution_amount');
            const internalDemandInput = document.getElementById('internal_demand');
            const percentInput = document.getElementById('percent');

            function calculateValues() {
                const processed = parseFloat(processedInput.value) || 0;
                const distribution = parseFloat(distributionInput.value) || 0;

                const internalDemand = processed - distribution;
                const percent = processed > 0 ? (internalDemand / processed * 100) : 0;

                internalDemandInput.value = internalDemand.toFixed(3);
                percentInput.value = percent.toFixed(2);
            }

            processedInput.addEventListener('input', calculateValues);
            distributionInput.addEventListener('input', calculateValues);
        });
    </script>
@endsection
