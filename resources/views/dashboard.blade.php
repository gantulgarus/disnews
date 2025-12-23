@extends('layouts.admin')

@section('content')
    <style>
        /* SCADA dark background */
        .scada-panel {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            color: #e0e0e0;
            box-shadow: inset 0 0 10px #000;
        }

        /* LED digital number */
        .scada-number {
            font-family: 'LED', monospace;
            font-size: 38px;
            color: #00ff33;
            text-shadow: 0 0 8px #00ff33;
            letter-spacing: 2px;
        }

        @font-face {
            font-family: 'LED';
            src: url('https://fonts.cdnfonts.com/s/20482/DS-DIGI.TTF') format('truetype');
        }

        /* Subtitle */
        .scada-title {
            font-size: 14px;
            color: rgb(186, 186, 205);
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* Station block - Responsive design */
        .scada-station {
            background: #111;
            border: 1px solid #333;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            box-shadow: inset 0 0 10px #000;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Station title - Responsive font size */
        .scada-title {
            font-size: 11px;
            color: rgb(186, 186, 205);
            margin-bottom: 8px;
            text-transform: uppercase;
            line-height: 1.2;
            /* Хэт урт текстийг багасгах */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Дундаж дэлгэц дээр илүү бага фонт */
        @media (max-width: 1400px) and (min-width: 992px) {
            .scada-title {
                font-size: 9px;
            }

            .scada-station {
                padding: 10px 8px;
            }

            .scada-station img {
                width: 32px !important;
            }

            .scada-station-number {
                font-size: 24px !important;
            }
        }

        /* Tablet дээр */
        @media (max-width: 991px) {
            .scada-title {
                font-size: 12px;
            }

            .station-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        /* Mobile дээр */
        @media (max-width: 576px) {
            .scada-title {
                font-size: 10px;
            }

            .station-grid {
                grid-template-columns: 1fr;
            }

            .scada-station {
                height: 120px;
            }
        }

        .scada-station-number {
            font-size: 28px;
            color: #00ff33;
            text-shadow: 0 0 10px #00ff33;
        }

        /* Station grid - минимум өргөнийг багасгах */
        .station-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 2rem;
        }
    </style>

    <div class="container-fluid p-4">
        <form method="GET" class="mb-4 row g-2 align-items-end">
            <div class="col-auto">
                <input type="date" name="date" id="dateInput" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Харах</button>
            </div>
        </form>

        <!-- Нийт чадал SCADA panel -->
        @if ($isSystemView)
            <div class="scada-panel mb-4">
                <div class="scada-title">НИЙТ ХЭРЭГЛЭЭ</div>
                <div class="scada-number">
                    @if ($realtimeData && isset($realtimeData['totalP']))
                        {{ number_format($realtimeData['totalP'], 1) }} МВт
                    @else
                        <span class="scada-error">Мэдээлэл байхгүй</span>
                    @endif
                </div>
                <div class="mt-2">
                    <span class="live-led"></span>
                    <span class="ms-2">Огноо:
                        @if ($realtimeData && isset($realtimeData['formattedTimestamp']))
                            {{ $realtimeData['formattedTimestamp'] }}
                        @else
                            --
                        @endif
                    </span>
                </div>
            </div>
        @endif

        @php
            $stationIcons = [
                'Дулааны цахилгаан станц' => 'power-plant.svg',
                'Салхин цахилгаан станц' => 'wind-power.svg',
                'Нарны цахилгаан станц' => 'solar-power.svg',
                'Батарей хуримтлуур' => 'battery-bolt.svg',
                'Импорт' => 'power-tower.svg',
            ];
        @endphp

        <!-- Станцуудын grid -->
        <div class="station-grid">
            @if ($realtimeData && isset($realtimeData['typeSums']))
                @foreach ($realtimeData['typeSums'] as $typeSum)
                    <div class="scada-station">
                        <div class="scada-title" title="{{ $typeSum['type_name'] }}">
                            {{ $typeSum['type_name'] }}
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <img src="{{ asset('images/' . ($stationIcons[$typeSum['type_name']] ?? 'power-plant.svg')) }}"
                                alt="{{ $typeSum['type_name'] }}"
                                style="width: 36px; filter: invert(1) brightness(1.6) drop-shadow(0 0 6px #00eaff);">
                            <div class="scada-station-number" style="line-height: 1;">
                                {{ number_format($typeSum['sumP'], 1) }} МВт
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach ($stationIcons as $name => $icon)
                    <div class="scada-station">
                        <div class="scada-title" title="{{ $name }}">
                            {{ $name }}
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <img src="{{ asset('images/' . $icon) }}" alt="{{ $name }}"
                                style="width: 36px; filter: invert(1) brightness(1.6) drop-shadow(0 0 6px #00eaff);">
                            <div class="scada-station-number" style="line-height: 1;">
                                <span class="scada-error">--</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="row row-deck row-cards">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($isSystemView)
                            <h3 class="card-title"> 24 цагийн системийн нийт чадлын график</h3>
                        @else
                            <h3 class="card-title"> Станцын нийт чадлын график</h3>
                        @endif

                        <div id="chart-area">
                            <div id="loading" class="chart-loading">
                                <div class="spinner-large"></div>
                                <div>Уншиж байна...</div>
                            </div>
                            <canvas id="lineChart" style="display:none;" width="100%" height="40"></canvas>
                            <div id="error" class="alert alert-danger d-none"></div>
                            <div id="peak" class="alert alert-primary d-none mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chart;

        // Load chart data ONLY
        async function loadChart(date) {
            document.getElementById('loading').style.display = 'flex';
            document.getElementById('lineChart').style.display = 'none';
            document.getElementById('error').classList.add('d-none');
            document.getElementById('peak').classList.add('d-none');

            try {
                const res = await fetch("{{ route('dashboard.data') }}?date=" + date);
                const data = await res.json();

                document.getElementById('loading').style.display = 'none';

                if (!data.success) {
                    document.getElementById('error').innerText = data.error || 'Алдаа гарлаа';
                    document.getElementById('error').classList.remove('d-none');
                    return;
                }

                document.getElementById('lineChart').style.display = 'block';

                if (chart) chart.destroy();
                const ctx = document.getElementById('lineChart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.times,
                        datasets: [{
                            label: 'Горим',
                            data: data.regimeValues,
                            borderColor: 'blue',
                            tension: 0.3,
                            spanGaps: true,
                        }, {
                            label: 'Гүйцэтгэл',
                            data: data.zconclusionValues,
                            borderColor: 'red',
                            tension: 0.3,
                            spanGaps: true,
                        }]
                    },
                    options: {
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        const datasetLabel = context.dataset.label || '';
                                        const value = context.raw ?? 0;
                                        return `${datasetLabel}: ${value.toFixed(2)} МВт`;
                                    },
                                    afterBody: function(contexts) {
                                        if (contexts.length < 2) return;
                                        const regime = contexts.find(c => c.dataset.label === 'Горим')
                                            ?.raw ?? null;
                                        const zconclusion = contexts.find(c => c.dataset.label ===
                                            'Гүйцэтгэл')?.raw ?? null;
                                        if (regime != null && zconclusion != null) {
                                            const diff = zconclusion - regime;
                                            const sign = diff >= 0 ? '+' : '';
                                            return `Зөрүү: ${sign}${diff.toFixed(2)} МВт`;
                                        }
                                        return '';
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                title: {
                                    display: true,
                                    text: 'МВт'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Цаг'
                                }
                            }
                        },
                        animation: false
                    },
                    plugins: [{
                        id: 'hoverLine',
                        afterDatasetsDraw(chart, args, opts) {
                            const {
                                ctx,
                                tooltip,
                                chartArea: {
                                    top,
                                    bottom
                                }
                            } = chart;
                            if (tooltip?._active?.length) {
                                const activePoint = tooltip._active[0].element;
                                const x = activePoint.x;

                                ctx.save();
                                ctx.beginPath();
                                ctx.moveTo(x, top);
                                ctx.lineTo(x, bottom);
                                ctx.lineWidth = 1;
                                ctx.strokeStyle = '#ff0000';
                                ctx.setLineDash([4, 4]);
                                ctx.stroke();
                                ctx.restore();
                            }
                        }
                    }]
                });

                if (data.peakLoad && data.peakLoad.value) {
                    document.getElementById('peak').innerHTML =
                        `<h4>Хамгийн их ачаалал ${data.date}</h4>
                 <p><strong>Цаг:</strong> ${data.peakLoad.time}<br>
                 <strong>Утга:</strong> ${data.peakLoad.formatted_value} МВт</p>`;
                    document.getElementById('peak').classList.remove('d-none');
                }

            } catch (e) {
                console.error('Chart loading error:', e);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').innerText = "Холболтын алдаа гарлаа";
                document.getElementById('error').classList.remove('d-none');
            }
        }

        // Initial chart load
        window.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('dateInput');
            if (dateInput && dateInput.value) {
                loadChart(dateInput.value);
            }
        });
    </script>
@endsection
