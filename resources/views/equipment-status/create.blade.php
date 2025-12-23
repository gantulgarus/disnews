@extends('layouts.admin')

@section('content')
    <div class="container-fluid mt-3">
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{ $powerPlant->name }} {{ $powerPlant->powerPlantType->name }}— Тоноглолын төлөв</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="date" name="date" form="equipment-form" class="form-control form-control-sm"
                            style="width: 160px;" value="{{ old('date', date('Y-m-d')) }}" required>
                        <button type="submit" form="equipment-form" class="btn btn-primary btn-sm">
                            <i class="bi bi-save"></i> Хадгалах
                        </button>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-sm py-2">
                        <strong>Алдаа:</strong>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form id="equipment-form" action="{{ route('daily-equipment-report.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="power_plant_id" value="{{ $powerPlant->id }}">

                    {{-- Тоноглолын багтаамжтай хувилбар --}}
                    @if ($equipments->isNotEmpty())
                        {{-- Өнгөний тайлбар --}}
                        <div class="alert alert-light border mb-3 py-2">
                            <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size: 12px;">
                                <strong class="text-muted">Тоноглолын төлөв:</strong>

                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        style="width: 16px; height: 16px; min-width: 16px; border-radius: 4px; background: rgba(220, 38, 38, 0.15);">
                                    </div>
                                    <span class="text-dark">Ажилд</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        style="width: 16px; height: 16px; min-width: 16px; border-radius: 4px; background: rgba(22, 163, 74, 0.15);">
                                    </div>
                                    <span class="text-dark">Бэлтгэлд</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        style="width: 16px; height: 16px; min-width: 16px; border-radius: 4px; background: rgba(107, 114, 128, 0.15);">
                                    </div>
                                    <span class="text-dark">Засварт</span>
                                </div>
                            </div>
                        </div>

                        @php
                            $groupedEquipments = $equipments->groupBy(function ($item) {
                                return $item->type->name ?? 'Бусад';
                            });
                        @endphp

                        <div class="equipment-grid">
                            @foreach ($groupedEquipments as $typeName => $typeEquipments)
                                <div class="equipment-type-section">
                                    <div class="type-header">{{ $typeName }}</div>

                                    <div class="equipment-list">
                                        @foreach ($typeEquipments as $index => $equipment)
                                            @php
                                                $globalIndex = $equipments->search($equipment);
                                                $lastStatus = $lastEquipmentStatuses[$equipment->id]->status ?? '';

                                                // Зургийн нэр тодорхойлох
                                                $iconName = match (
                                                    $equipment->type->icon_name ?? $powerPlant->powerPlantType->name
                                                ) {
                                                    'Зуух' => 'k.svg',
                                                    'Турбогенератор' => 'tg.svg',
                                                    'НЦС' => 'solar-power.svg',
                                                    'СЦС' => 'wind-power.svg',
                                                    'Баттерэй' => 'battery-bolt.svg',
                                                    default => 'power-plant.svg',
                                                };
                                            @endphp

                                            <div class="equipment-item">
                                                <div class="eq-name">{{ $equipment->name }}</div>
                                                <div class="eq-status">
                                                    <label class="st-option st-work" title="Ажилд">
                                                        <input type="radio" name="equipments[{{ $globalIndex }}][status]"
                                                            value="Ажилд" {{ $lastStatus == 'Ажилд' ? 'checked' : '' }}
                                                            required>
                                                        <img src="{{ asset('images/' . $iconName) }}" alt="Ажилд"
                                                            style="width: 14px; height: 14px;">
                                                    </label>

                                                    <label class="st-option st-standby" title="Бэлтгэлд">
                                                        <input type="radio"
                                                            name="equipments[{{ $globalIndex }}][status]" value="Бэлтгэлд"
                                                            {{ $lastStatus == 'Бэлтгэлд' ? 'checked' : '' }} required>
                                                        <img src="{{ asset('images/' . $iconName) }}" alt="Бэлтгэлд"
                                                            style="width: 14px; height: 14px;">
                                                    </label>

                                                    <label class="st-option st-maintenance" title="Засварт">
                                                        <input type="radio"
                                                            name="equipments[{{ $globalIndex }}][status]" value="Засварт"
                                                            {{ $lastStatus == 'Засварт' ? 'checked' : '' }} required>
                                                        <img src="{{ asset('images/' . $iconName) }}" alt="Засварт"
                                                            style="width: 14px; height: 14px;">
                                                    </label>
                                                </div>

                                                <input type="hidden" name="equipments[{{ $globalIndex }}][equipment_id]"
                                                    value="{{ $equipment->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info py-2">Тоноглол олдсонгүй</div>
                    @endif

                    {{-- Станцын чадлын мэдээлэл - багтаамжтай --}}
                    <div class="power-section mt-3">
                        <div class="type-header mb-2">Станцын чадлын мэдээлэл</div>

                        <div class="row g-2">
                            <div class="col-md-2 col-sm-4">
                                <label class="form-label-sm">Р (МВт)</label>
                                <input type="number" step="0.01" name="p" class="form-control form-control-sm"
                                    value="{{ old('p', $lastPowerInfo->p ?? '') }}">
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="form-label-sm">Рmax (МВт)</label>
                                <input type="number" step="0.01" name="p_max" class="form-control form-control-sm"
                                    value="{{ old('p_max', $lastPowerInfo->p_max ?? '') }}">
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <label class="form-label-sm">Рmin (МВт)</label>
                                <input type="number" step="0.01" name="p_min" class="form-control form-control-sm"
                                    value="{{ old('p_min', $lastPowerInfo->p_min ?? '') }}">
                            </div>
                            @if ($powerPlant->region == 'ББЭХС' || $powerPlant->region == 'АУЭХС')
                                <div class="col-md-2 col-sm-4">
                                    <label class="form-label-sm">Үйлдвэрлэсэн ЦЭХ</label>
                                    <input type="number" step="0.001" name="produced_energy"
                                        class="form-control form-control-sm"
                                        value="{{ old('produced_energy', $lastPowerInfo->produced_energy ?? '') }}">
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <label class="form-label-sm">Түгээсэн ЦЭХ</label>
                                    <input type="number" step="0.001" name="distributed_energy"
                                        class="form-control form-control-sm"
                                        value="{{ old('distributed_energy', $lastPowerInfo->distributed_energy ?? '') }}">
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <label class="form-label-sm">Түлшний нөөц (л)</label>
                                    <input type="number" step="0.01" name="fuel_amount"
                                        class="form-control form-control-sm"
                                        value="{{ old('fuel_amount', $lastPowerInfo->fuel_amount ?? '') }}">
                                </div>
                            @endif
                            @if ($powerPlant->powerPlantType->name == 'УЦС')
                                <div class="col-md-2 col-sm-4">
                                    <label class="form-label-sm">Усны төвшин (м)</label>
                                    <input type="number" step="0.01" name="water_level"
                                        class="form-control form-control-sm"
                                        value="{{ old('water_level', $lastPowerInfo->water_level ?? '') }}">
                                </div>
                            @endif
                        </div>

                        <div class="mt-2">
                            <label class="form-label-sm">Засвар, доголдол</label>
                            <textarea name="main_equipment_remark" class="form-control form-control-sm" rows="2"
                                placeholder="Турбин №2 — засварт...">{{ old('main_equipment_remark', $lastPowerInfo->remark ?? '') }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Compact layout */
        body {
            font-size: 13px;
        }

        .form-label-sm {
            font-size: 11px;
            margin-bottom: 2px;
            font-weight: 500;
            color: #495057;
        }

        .form-control-sm {
            font-size: 12px;
            padding: 4px 8px;
            height: auto;
        }

        /* Тоноглолын grid */
        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .equipment-type-section {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px;
        }

        .type-header {
            font-size: 12px;
            font-weight: 600;
            color: #0d6efd;
            padding: 4px 8px;
            background: #fff;
            border-radius: 4px;
            margin-bottom: 8px;
            border-left: 3px solid #0d6efd;
        }

        .equipment-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .equipment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 6px 8px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
            transition: all 0.15s;
        }

        .equipment-item:hover {
            border-color: #0d6efd;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .eq-name {
            font-size: 12px;
            font-weight: 500;
            color: #2c3e50;
            min-width: 60px;
        }

        .eq-status {
            display: flex;
            gap: 4px;
        }

        .st-option {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 28px;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s;
            margin: 0;
            background: #fff;
        }

        .st-option:hover {
            border-color: #adb5bd;
            transform: translateY(-1px);
        }

        .st-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .st-option span {
            font-size: 16px;
            line-height: 1;
        }

        .st-option img {
            transition: all 0.15s;
            filter: grayscale(0.3);
        }

        .st-option:hover img {
            filter: grayscale(0);
            transform: scale(1.1);
        }

        /* Сонгогдсон төлөв */
        .st-option:has(input:checked) {
            font-weight: 600;
            transform: scale(1.05);
        }

        /* Ажилд - Улаан */
        .st-work:has(input:checked) {
            background: rgba(220, 38, 38, 0.15);
            border-color: #dc2626;
            color: #7f1d1d;
        }

        /* Бэлтгэлд - Ногоон */
        .st-standby:has(input:checked) {
            background: rgba(22, 163, 74, 0.15);
            border-color: #16a34a;
            color: #14532d;
        }

        /* Засварт - Саарал */
        .st-maintenance:has(input:checked) {
            background: rgba(107, 114, 128, 0.15);
            border-color: #6b7280;
            color: #374151;
        }

        .power-section {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }

        /* Responsive - том дэлгэцэд илүү олон багана */
        @media (min-width: 1400px) {
            .equipment-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1920px) {
            .equipment-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Жижиг дэлгэц */
        @media (max-width: 768px) {
            .equipment-grid {
                grid-template-columns: 1fr;
            }

            .eq-name {
                font-size: 11px;
            }

            .st-option {
                width: 28px;
                height: 24px;
            }
        }

        /* Print optimize */
        @media print {
            body {
                font-size: 10px;
            }

            .equipment-item {
                padding: 3px 6px;
            }

            .btn,
            .card-body {
                display: none !important;
            }
        }
    </style>
@endsection
