@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">24 цагийн системийн нийт чадлын график</h3>
                        {{-- <div id="chart-mentions" class="chart-lg"></div> --}}
                        <form method="GET" class="mb-3">
                            <input type="date" name="date" value="{{ $date }}">
                            <button type="submit">Харах</button>
                        </form>

                        @if ($peakLoad['value'])
                            <div class="alert alert-primary">
                                <h4>Хамгийн их ачаалал {{ $date }}</h4>
                                <p>
                                    <strong>Цаг:</strong> {{ $peakLoad['time'] }}<br>
                                    <strong>Утга:</strong> {{ $peakLoad['formatted_value'] }}<br>
                                    {{-- <strong>Эх сурвалж:</strong> {{ $peakLoad['source'] }} --}}
                                </p>
                            </div>
                        @endif

                        <canvas id="lineChart" width="100%" height="40"></canvas>

                        <hr>

                        <h4 class="mt-4">Цагийн харьцуулсан хүснэгт</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Цаг</th>
                                    <th>Regime VALUE</th>
                                    <th>ZConclusion VALUE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($times as $index => $time)
                                    <tr>
                                        <td>{{ $time }}</td>
                                        <td>{{ $regimeValues[$index] ?? '—' }}</td>
                                        <td>{{ $zconclusionValues[$index] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('lineChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($times),
                    datasets: [{
                            label: 'Горим',
                            data: @json($regimeValues),
                            borderColor: 'blue',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            tension: 0.3,
                            spanGaps: true, // хоосон утгуудыг холбох
                        },
                        {
                            label: 'Гүйцэтгэл',
                            data: @json($zconclusionValues),
                            borderColor: 'red',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.3,
                            spanGaps: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Цаг'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Чадал (МВт)'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
