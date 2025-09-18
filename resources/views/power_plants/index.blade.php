@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <h1 class="page-title">Станцуудын төлөвийн мэдээлэл</h1>
            <p class="page-subtitle text-muted">Цахилгаан станцуудын тоног төхөөрөмжийн төлөв байдлын мэдээлэл</p>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('power-plants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Шинэ станц нэмэх
            </a>
        </div>

        @foreach ($powerPlants as $powerPlant)
            <div class="power-plant-card card mb-4">
                <div class="card-header power-plant-header position-relative">
                    <h2 class="power-plant-name mb-0">{{ $powerPlant->name }}</h2>
                    {{-- <div class="power-plant-shortname">{{ $powerPlant->short_name }}</div> --}}
                    <a href="{{ route('power-plants.edit', $powerPlant->id) }}"
                        class="btn btn-sm btn-light btn-edit-power-plant">
                        <i class="fas fa-edit me-1"></i> Засах
                    </a>
                    <a href="{{ route('power-plant-daily-reports.create', ['power_plant_id' => $powerPlant->id]) }}"
                        class="btn btn-sm btn-success btn-add-report">
                        <i class="fas fa-plus me-1"></i> Төлвийн мэдээ оруулах
                    </a>
                </div>

                <div class="card-body">
                    @php
                        $report = $dailyReports->get($powerPlant->id)?->first();
                    @endphp

                    @if ($report)
                        <div class="row">
                            <!-- Зуух -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="equipment-section h-100">
                                    <h3 class="section-title"><i class="fas fa-fire me-2"></i> Зуух</h3>
                                    <div class="d-flex justify-content-around text-center">
                                        <div class="status-card status-working">
                                            <div class="status-value">
                                                @if ($report->boiler_working)
                                                    @foreach (json_decode($report->boiler_working) as $boiler_id)
                                                        <p>{{ \App\Models\Boiler::find($boiler_id)->name }}</p>
                                                    @endforeach
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                            <div class="status-label">Ажилд</div>
                                        </div>
                                        <div class="status-card status-preparation">
                                            <div class="status-value">
                                                @if ($report->boiler_preparation)
                                                    @foreach (json_decode($report->boiler_preparation) as $boiler_id)
                                                        <p>{{ \App\Models\Boiler::find($boiler_id)->name }}</p>
                                                    @endforeach
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                            <div class="status-label">Бэлтгэлд</div>
                                        </div>
                                        <div class="status-card status-repair">
                                            <div class="status-value">
                                                @if ($report->boiler_repair)
                                                    @foreach (json_decode($report->boiler_repair) as $boiler_id)
                                                        <p>{{ \App\Models\Boiler::find($boiler_id)->name }}</p>
                                                    @endforeach
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                            <div class="status-label">Засварт</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Турбингенератор -->
                            <div class="col-md-6">
                                <div class="equipment-section h-100">
                                    <h3 class="section-title"><i class="fas fa-cog me-2"></i> Турбингенератор</h3>
                                    <div class="d-flex justify-content-around text-center">
                                        <div class="status-card status-working">
                                            <div class="status-value">
                                                @if ($report->turbine_working)
                                                    @foreach (json_decode($report->turbine_working) as $turbine_id)
                                                        <p>{{ \App\Models\TurbineGenerator::find($turbine_id)->name }}</p>
                                                    @endforeach
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                            <div class="status-label">Ажилд</div>
                                        </div>
                                        <div class="status-card status-preparation">
                                            <div class="status-value">
                                                @if ($report->turbine_preparation)
                                                    @foreach (json_decode($report->turbine_preparation) as $turbine_id)
                                                        <p>{{ \App\Models\TurbineGenerator::find($turbine_id)->name }}</p>
                                                    @endforeach
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                            <div class="status-label">Бэлтгэлд</div>
                                        </div>
                                        <div class="status-card status-repair">
                                            <div class="status-value">
                                                @if ($report->turbine_repair)
                                                    @foreach (json_decode($report->turbine_repair) as $turbine_id)
                                                        <p>{{ \App\Models\TurbineGenerator::find($turbine_id)->name }}</p>
                                                    @endforeach
                                                @else
                                                    <p></p>
                                                @endif
                                            </div>
                                            <div class="status-label">Засварт</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h4><i class="fas fa-bolt me-2"></i> Цахилгаан эрчим хүч</h4>
                                    <div class="power-info">
                                        <div class="power-current">
                                            <span class="power-value">{{ $report->power }}</span>
                                            <span class="power-unit">МВт</span>
                                        </div>
                                        <div class="power-max">
                                            Хамгийн их: <strong>{{ $report->power_max }} МВт</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="notes-card">
                                    <h4><i class="fas fa-sticky-note me-2"></i> Тайлбар</h4>
                                    <p class="notes-content">{{ $report->notes ?: 'Тайлбар оруулаагүй байна' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="report-date mt-3">
                            <small class="text-muted">Мэдээллийн огноо: {{ $report->report_date }}</small>
                        </div>
                    @else
                        <div class="no-data text-center py-5">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h4>Төлөвийн мэдээ оруулаагүй байна</h4>
                            <p class="text-muted">Энэ цахилгаан станцын төлөвийн мэдээлэл байхгүй байна.</p>
                            <a href="{{ route('power-plant-daily-reports.create', ['power_plant_id' => $powerPlant->id]) }}"
                                class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Мэдээ оруулах
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #2980b9;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
        }

        .page-header {
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .power-plant-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .power-plant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        .power-plant-header {
            background: linear-gradient(90deg, var(--secondary-color), #3498db);
            color: white;
            padding: 1.2rem 1.5rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .power-plant-name {
            font-size: 1.6rem;
            font-weight: 600;
            margin: 0;
        }

        .power-plant-shortname {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .equipment-section {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 20px;
            height: 100%;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
        }

        .status-card {
            padding: 15px 10px;
            border-radius: 10px;
            min-width: 90px;
        }

        .status-working {
            background-color: rgba(39, 174, 96, 0.15);
            color: var(--success-color);
            border: 1px solid rgba(39, 174, 96, 0.3);
        }

        .status-preparation {
            background-color: rgba(243, 156, 18, 0.15);
            color: var(--warning-color);
            border: 1px solid rgba(243, 156, 18, 0.3);
        }

        .status-repair {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--danger-color);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .status-value {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .status-label {
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .info-card,
        .notes-card {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 20px;
            height: 100%;
        }

        .info-card h4,
        .notes-card h4 {
            font-size: 1.1rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .power-info {
            text-align: center;
        }

        .power-current {
            display: flex;
            align-items: baseline;
            justify-content: center;
            margin-bottom: 10px;
        }

        .power-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .power-unit {
            font-size: 1.2rem;
            margin-left: 5px;
            color: var(--primary-color);
        }

        .power-max {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .notes-content {
            line-height: 1.6;
            color: #495057;
        }

        .btn-edit-power-plant {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s;
        }

        .btn-edit-power-plant:hover {
            background: white;
            color: var(--secondary-color);
        }

        .btn-add-report {
            position: absolute;
            top: 15px;
            right: 130px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s;
        }

        .btn-add-report:hover {
            background: white;
            color: var(--secondary-color);
        }

        .no-data {
            padding: 40px 20px;
            color: #6c757d;
        }

        .no-data h4 {
            margin-bottom: 10px;
            color: #6c757d;
        }

        .report-date {
            text-align: right;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .btn-add-report {
                position: relative;
                top: 0;
                right: 0;
                margin-top: 10px;
                display: block;
                width: 100%;
            }

            .btn-edit-power-plant {
                position: relative;
                top: 0;
                right: 0;
                margin-top: 10px;
                display: block;
                width: 100%;
            }

            .power-plant-header {
                text-align: center;
                padding-bottom: 100px;
            }

            .power-plant-header .btn {
                position: absolute;
                bottom: 15px;
                left: 50%;
                transform: translateX(-50%);
                width: 80%;
            }

            .status-card {
                min-width: 70px;
                padding: 10px 5px;
            }

            .status-value {
                font-size: 1.5rem;
            }

            .power-value {
                font-size: 2rem;
            }
        }
    </style>
@endsection
