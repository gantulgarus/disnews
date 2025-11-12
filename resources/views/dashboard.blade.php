@extends('layouts.admin')

@section('content')
    <div class="container-fliud p-4">
        <form id="dateForm" method="GET" class="mb-4 row g-2 align-items-end">
            <div class="col-auto">
                {{-- <label for="date" class="form-label">Огноо:</label> --}}
                <input type="date" name="date" id="dateInput" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Харах</button>
            </div>
        </form>

        <div class="card mt-3 border-0 shadow-sm bg-light">
            <div class="card-body text-center">
                <h5 class="fw-bold text-success mb-0">
                    Нийт чадал: {{ number_format($totalPmax, 2) }} МВт
                </h5>
            </div>
        </div>

        <div class="row mt-4">
            @foreach ($typeSums as $type)
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold">{{ $type['type_name'] }}</h5>
                            <p class="mt-2 mb-0 text-muted">Max чадал:</p>
                            <h3 class="text-primary fw-bold">
                                {{ number_format($type['sumPmax'], 2) }} МВт
                            </h3>
                            {{-- <p class="mt-2 mb-0 text-muted">P чадал:</p>
                            <h4 class="text-success fw-bold">
                                {{ number_format($type['sumP'], 2) }} МВт
                            </h4> --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row row-deck row-cards">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">24 цагийн системийн нийт чадлын график</h3>



                        {{-- Сервер талын @if ($peakLoad['value']) ... block-ыг устга. JS дээр #peak-ээр харуулна --}}
                        <div id="chart-area">
                            <div id="loading">Уншиж байна...</div>
                            <canvas id="lineChart" style="display:none;" width="100%" height="40"></canvas>
                            <div id="error" class="alert alert-danger d-none"></div>
                            <div id="peak" class="alert alert-primary d-non mt-4"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chart;

        async function loadChart(date) {
            document.getElementById('loading').style.display = 'block';
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
                        // 🔹 Custom босоо шугам зурдаг plugin
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
                                ctx.strokeStyle = '#ff0000'; // шугамны өнгө улаан
                                ctx.setLineDash([4, 4]); // тасархай шугам
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
                 <strong>Утга:</strong> ${data.peakLoad.formatted_value}</p>`;
                    document.getElementById('peak').classList.remove('d-none');
                }

            } catch (e) {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('error').innerText = "Холболтын алдаа гарлаа";
                document.getElementById('error').classList.remove('d-none');
            }
        }

        // анхдагч ачаалал (dateInput байгаа эсэхийг шална)
        const dateInput = document.getElementById('dateInput');
        if (dateInput) {
            loadChart(dateInput.value);
        }

        // огноо өөрчлөхөд дахин дуудах
        document.getElementById('dateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadChart(document.getElementById('dateInput').value);
        });
    </script>
@endsection
