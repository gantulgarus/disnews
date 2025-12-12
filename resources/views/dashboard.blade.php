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
            src: url('https://fonts.cdnfonts.com/s/20482/DS-DIGI.woff') format('woff');
        }

        /* Subtitle */
        .scada-title {
            font-size: 14px;
            color: rgb(186, 186, 205);
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* Station block */
        .scada-station {
            background: #111;
            border: 1px solid #333;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            box-shadow: inset 0 0 10px #000;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .scada-station-number {
            font-size: 28px;
            color: #00ff33;
            text-shadow: 0 0 10px #00ff33;
        }

        /* Live Update LED */
        .live-led {
            width: 12px;
            height: 12px;
            background: #00ff00;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #00ff00;
            animation: blink 1s infinite;
        }

        .station-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 2rem;
        }

        @keyframes blink {
            50% {
                opacity: 0.3;
            }
        }

        /* Inline loader */
        .inline-loader {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #00ff33;
        }

        .spinner-small {
            width: 20px;
            height: 20px;
            border: 3px solid #333;
            border-top: 3px solid #00ff33;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Skeleton loader for stations */
        .skeleton {
            background: linear-gradient(90deg, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-number {
            height: 40px;
        }

        /* Chart loading */
        .chart-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            color: #666;
        }

        .chart-loading .spinner-large {
            width: 50px;
            height: 50px;
            border: 4px solid #e0e0e0;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        .view-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .view-badge.system {
            background: #007bff;
            color: white;
        }

        .view-badge.station {
            background: #28a745;
            color: white;
        }
    </style>

    <div class="container-fluid p-4">
        <form id="dateForm" method="GET" class="mb-4 row g-2 align-items-end">
            <div class="col-auto">
                <input type="date" name="date" id="dateInput" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Харах</button>
            </div>
            <div class="col-auto ms-auto">
                <span id="viewBadge" class="view-badge" style="display: none;"></span>
            </div>
        </form>

        <!-- Нийт чадал SCADA panel -->
        <div class="scada-panel mb-4">
            <div class="scada-title" id="totalPowerTitle">НИЙТ ХЭРЭГЛЭЭ</div>
            <div class="scada-number" id="totalPowerDisplay">
                <div class="inline-loader">
                    <div class="spinner-small"></div>
                    <span>Уншиж байна...</span>
                </div>
            </div>
            <div class="mt-2">
                <span class="live-led"></span>
                <span class="ms-2">Огноо: <span id="lastUpdate">--</span></span>
            </div>
        </div>

        @php
            $stationTypeIcons = [
                'Дулааны цахилгаан станц' => 'power-plant.svg',
                'Салхин цахилгаан станц' => 'wind-power.svg',
                'Нарны цахилгаан станц' => 'solar-power.svg',
                'Батарей хуримтлуур' => 'battery-bolt.svg',
                'Импорт' => 'power-tower.svg',
            ];
        @endphp

        <!-- Станцуудын grid -->
        <div class="station-grid" id="stationGrid">
            @foreach ($stationGroups as $key => $group)
                <div class="scada-station">
                    <div class="scada-title">{{ $group['name'] }}</div>
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <img src="{{ asset('images/' . $stationTypeIcons[$group['name']]) }}" alt="{{ $group['name'] }}"
                            style="width: 40px; filter: invert(1) brightness(1.6) drop-shadow(0 0 6px #00eaff);">
                        <div class="scada-station-number" style="line-height: 1;">
                            <div class="skeleton skeleton-number d-inline-block" style="width: 140px; height: 28px;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row row-deck row-cards">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title" id="chartTitle">24 цагийн системийн нийт чадлын график</h3>
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
        // Version: 2.0 - Fix regime display issue
        let chart;
        const icons = {
            'Дулааны цахилгаан станц': 'power-plant.svg',
            'Салхин цахилгаан станц': 'wind-power.svg',
            'Нарны цахилгаан станц': 'solar-power.svg',
            'Батарей хуримтлуур': 'battery-bolt.svg',
            'Импорт': 'power-tower.svg'
        };

        function updateViewLabels(isSystem) {
            const badge = document.getElementById('viewBadge');
            const totalPowerTitle = document.getElementById('totalPowerTitle');
            const chartTitle = document.getElementById('chartTitle');

            if (isSystem) {
                badge.className = 'view-badge system';
                badge.textContent = 'СИСТЕМИЙН ХАРАГДАЦ';
                totalPowerTitle.textContent = 'НИЙТ ХЭРЭГЛЭЭ';
                chartTitle.textContent = '24 цагийн системийн нийт чадлын график';
            } else {
                badge.className = 'view-badge station';
                badge.textContent = 'СТАНЦЫН ХАРАГДАЦ';
                totalPowerTitle.textContent = 'СТАНЦЫН НИЙТ ЧАДАЛ';
                chartTitle.textContent = '24 цагийн станцын нийт чадлын график';
            }
            badge.style.display = 'inline-block';
        }

        async function loadRealtimeData() {
            try {
                const res = await fetch("{{ route('dashboard.realtime') }}");
                const data = await res.json();

                if (!data.success) {
                    document.getElementById('totalPowerDisplay').innerHTML =
                        '<span style="color: #ff6b6b; font-size: 20px;">Алдаа гарлаа</span>';
                    return;
                }

                updateViewLabels(data.isSystemView);

                document.getElementById('totalPowerDisplay').innerHTML =
                    `${parseFloat(data.totalP).toFixed(2)} МВт`;

                if (data.latestTimestamp) {
                    const date = new Date(data.latestTimestamp * 1000);
                    document.getElementById('lastUpdate').innerText =
                        date.toLocaleString('en-CA', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        }).replace(',', '');
                }

                const stationGrid = document.getElementById('stationGrid');
                stationGrid.innerHTML = '';

                data.typeSums.forEach(type => {
                    const stationDiv = document.createElement('div');
                    stationDiv.className = 'scada-station';
                    stationDiv.innerHTML = `
                        <div class="scada-title">${type.type_name}</div>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <img src="/images/${icons[type.type_name]}" alt="${type.type_name}"
                                style="width: 40px; filter: invert(1) brightness(1.6) drop-shadow(0 0 6px #00eaff);">
                            <div class="scada-station-number" style="line-height: 1;">
                                ${parseFloat(type.sumP).toFixed(2)} МВт
                            </div>
                        </div>
                    `;
                    stationGrid.appendChild(stationDiv);
                });
            } catch (e) {
                console.error('Error loading realtime data:', e);
                document.getElementById('totalPowerDisplay').innerHTML =
                    '<span style="color: #ff6b6b; font-size: 20px;">Холболтын алдаа</span>';
            }
        }

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

                console.log('Chart data:', {
                    isSystemView: data.isSystemView,
                    hasRegime: !!(data.regimeValues && data.regimeValues.length > 0),
                    regimeLength: data.regimeValues ? data.regimeValues.length : 0,
                    zconclusionLength: data.zconclusionValues ? data.zconclusionValues.length : 0
                });

                document.getElementById('lineChart').style.display = 'block';

                if (chart) chart.destroy();
                const ctx = document.getElementById('lineChart').getContext('2d');

                const datasets = [];

                // Горим dataset нэмэх
                const hasRegimeData = data.regimeValues && Array.isArray(data.regimeValues) && data.regimeValues
                    .length > 0;

                if (hasRegimeData) {
                    console.log('✓ Adding regime dataset');
                    datasets.push({
                        label: 'Горим',
                        data: data.regimeValues,
                        borderColor: '#0066ff',
                        backgroundColor: 'rgba(0, 102, 255, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        spanGaps: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                    });
                }

                // Гүйцэтгэл dataset нэмэх
                datasets.push({
                    label: data.isSystemView ? 'Гүйцэтгэл' : 'Станцын чадал',
                    data: data.zconclusionValues,
                    borderColor: '#ff0000',
                    backgroundColor: 'rgba(255, 0, 0, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    spanGaps: true,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                });

                console.log('Creating chart with', datasets.length, 'datasets');

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.times,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${(context.raw ?? 0).toFixed(2)} МВт`;
                                    },
                                    afterBody: function(contexts) {
                                        if (contexts.length < 2) return '';
                                        const regime = contexts.find(c => c.dataset.label === 'Горим')?.raw;
                                        const actual = contexts.find(c => c.dataset.label === 'Гүйцэтгэл' ||
                                            c.dataset.label === 'Станцын чадал')?.raw;
                                        if (regime != null && actual != null) {
                                            const diff = actual - regime;
                                            return `Зөрүү: ${diff >= 0 ? '+' : ''}${diff.toFixed(2)} МВт`;
                                        }
                                        return '';
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'МВт'
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.1)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Цаг'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        animation: false
                    },
                    plugins: [{
                        id: 'hoverLine',
                        afterDatasetsDraw(chart) {
                            const {
                                ctx,
                                tooltip,
                                chartArea: {
                                    top,
                                    bottom
                                }
                            } = chart;
                            if (tooltip?._active?.length) {
                                const x = tooltip._active[0].element.x;
                                ctx.save();
                                ctx.beginPath();
                                ctx.moveTo(x, top);
                                ctx.lineTo(x, bottom);
                                ctx.lineWidth = 1;
                                ctx.strokeStyle = 'rgba(0,0,0,0.3)';
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
                console.error('Chart error:', e);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').innerText = "Холболтын алдаа гарлаа";
                document.getElementById('error').classList.remove('d-none');
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            loadRealtimeData();
            const dateInput = document.getElementById('dateInput');
            if (dateInput) loadChart(dateInput.value);
        });

        document.getElementById('dateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadChart(document.getElementById('dateInput').value);
        });
    </script>
@endsection
