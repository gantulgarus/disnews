@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-2">
        <div class="page-header d-print-none mb-2">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title mb-0">Эх үүсвэрийн сүлжээний усны горим барилтын 24 цагийн мэдээ</h2>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-3">
                <!-- Огноо сонгох хэсэг -->
                <form method="GET" action="{{ route('power-plant-readings.daily-overview') }}" class="mb-2">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1 small">Огноо сонгох</label>
                            <input type="date" name="date" id="date" value="{{ $date }}"
                                max="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="10" cy="10" r="7"></circle>
                                    <line x1="21" y1="21" x2="15" y2="15"></line>
                                </svg>
                                Харах
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button"
                                onclick="window.location.href='{{ route('power-plant-readings.daily-overview', ['date' => now()->format('Y-m-d')]) }}'"
                                class="btn btn-secondary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                </svg>
                                Өнөөдөр
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button"
                                onclick="window.location.href='{{ route('power-plant-readings.temperature-charts') }}'"
                                class="btn btn-info btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 12a8 8 0 1 0 16 0a8 8 0 0 0 -16 0"></path>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                График харах
                            </button>
                        </div>
                        <div class="col-auto ms-auto">
                            @if ($powerPlantsData->count() > 0)
                                <span class="badge bg-info text-white me-2">
                                    <strong>{{ $date }}</strong> - {{ $totalReadings }} хэмжилт |
                                    {{ $hoursCovered }} цаг
                                </span>
                            @endif
                            <button type="button" onclick="fetchDataForDate()" class="btn btn-success btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                </svg>
                                API татах
                            </button>
                        </div>
                    </div>
                </form>

                @if ($powerPlantsData->count() > 0)
                    <!-- Хүснэгт -->
                    <div class="table-responsive compact-table">
                        <table class="table table-sm table-bordered table-hover table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center bg-primary text-white sticky-col col-station">Станц</th>
                                    <th class="text-center bg-primary text-white sticky-col col-equipment">Тоноглол</th>
                                    <th class="text-center bg-primary text-white sticky-col col-unit">Нэгж</th>
                                    @for ($hour = 1; $hour <= 24; $hour++)
                                        <th class="text-center bg-primary text-white sticky-col col-hour">
                                            {{ $hour }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($powerPlantsData as $plantName => $plantData)
                                    @foreach ($plantData['equipments'] as $index => $equipment)
                                        <tr>
                                            @if ($index === 0)
                                                <td class="text-center fw-bold bg-light align-middle sticky-col col-station"
                                                    rowspan="{{ $plantData['equipments']->count() }}">
                                                    <div class="text-primary fw-bold small">{{ $plantName }}</div>
                                                    <div class="text-muted" style="font-size: 9px;">
                                                        {{ $plantData['equipments']->count() }}
                                                    </div>
                                                </td>
                                            @endif
                                            <td class="align-middle sticky-col col-equipment">
                                                <div class="fw-bold small">{{ $equipment->name }}</div>
                                                <div class="text-muted font-monospace" style="font-size: 9px;">
                                                    {{ $equipment->code }}
                                                </div>
                                            </td>
                                            <td class="text-center align-middle sticky-col col-unit">
                                                <span class="badge bg-secondary text-white"
                                                    style="font-size: 9px;">{{ $equipment->unit }}</span>
                                            </td>
                                            @for ($hour = 1; $hour <= 24; $hour++)
                                                @php
                                                    $reading = $plantData['hourly_data'][$equipment->id][$hour] ?? null;
                                                @endphp
                                                <td class="text-center align-middle col-hour">
                                                    @if ($reading)
                                                        <span class="fw-bold text-dark small">
                                                            {{ number_format($reading->value, 1) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Мэдээлэл байхгүй -->
                    <div class="empty py-4">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                                <line x1="9" y1="10" x2="9.01" y2="10"></line>
                                <line x1="15" y1="10" x2="15.01" y2="10"></line>
                                <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0"></path>
                            </svg>
                        </div>
                        <p class="empty-title">Мэдээлэл олдсонгүй</p>
                        <p class="empty-subtitle text-muted">{{ $date }} өдрийн мэдээлэл байхгүй байна.</p>
                        {{-- <div class="empty-action">
                            <button onclick="fetchDataForDate()" class="btn btn-success btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16"
                                    height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                </svg>
                                API-аас өгөгдөл татах
                            </button>
                        </div> --}}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function fetchDataForDate() {
            const date = document.getElementById('date').value;
            if (!date) {
                alert('Огноо сонгоно уу!');
                return;
            }

            const btn = event.target;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Татаж байна...';

            fetch('/power-plant-readings/fetch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        date: date
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`✓ Амжилттай!\n\nХадгалагдсан: ${data.stats.saved}\nАлдаа: ${data.stats.errors}`);
                        window.location.reload();
                    } else {
                        alert('✗ Алдаа: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('✗ Алдаа гарлаа: ' + error.message);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
        }
    </script>

    <style>
        /* Compact дизайн */
        .compact-table {
            max-height: calc(100vh - 180px);
            overflow: auto;
            font-size: 11px;
        }

        /* Багануудын өргөн */
        .col-station {
            width: 100px;
            min-width: 100px;
        }

        .col-equipment {
            width: 180px;
            min-width: 180px;
        }

        .col-unit {
            width: 50px;
            min-width: 50px;
        }

        .col-hour {
            width: 50px;
            min-width: 50px;
            max-width: 50px;
        }

        /* Sticky баганууд */
        .sticky-col {
            position: sticky;
            background: white;
            z-index: 10;
        }

        thead .sticky-col {
            top: 0;
            background: var(--tblr-primary) !important;
            z-index: 11;
        }

        .col-station.sticky-col {
            left: 0;
        }

        .col-equipment.sticky-col {
            left: 100px;
        }

        .col-unit.sticky-col {
            left: 280px;
        }

        /* Station багана sticky өнгө */
        tbody .col-station.sticky-col {
            background: #f8f9fa !important;
        }

        /* Hover effect */
        .table-hover tbody tr:hover td {
            background-color: rgba(var(--tblr-primary-rgb), 0.05) !important;
        }

        .table-hover tbody tr:hover .sticky-col {
            background-color: rgba(var(--tblr-primary-rgb), 0.08) !important;
        }

        .table-hover tbody tr:hover .col-station.sticky-col {
            background-color: rgba(var(--tblr-primary-rgb), 0.12) !important;
        }

        /* Padding багасгах */
        .table-sm td,
        .table-sm th {
            padding: 0.25rem 0.3rem;
        }

        /* Border */
        .table-bordered td,
        .table-bordered th {
            border: 1px solid #e6e7e9;
        }

        /* Print хувилбар */
        @media print {

            .page-header,
            form,
            .btn {
                display: none !important;
            }

            .table-responsive {
                max-height: none !important;
                overflow: visible !important;
            }

            .compact-table {
                font-size: 8px;
            }

            .col-hour {
                width: 35px;
                min-width: 35px;
            }
        }

        /* Scrollbar загвар */
        .compact-table::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .compact-table::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .compact-table::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .compact-table::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection
