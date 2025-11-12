@extends('layouts.admin')

@section('content')
    <div class="container my-4">
        <h4 class="mb-3">Дулааны станцын горимын мэдээлэл</h4>
        <!-- Filter form -->
        <form action="{{ route('station_thermo.news') }}" method="GET" class="row g-2 m-2">
            <div class="col-auto">
                <input type="date" name="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Хайх</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle text-center" style="font-size: 0.6rem;">
                <thead class="table-light">
                    <tr>
                        <th rowspan="3">ЦАГ</th>
                        <th colspan="6">ДЦС-2</th>
                        <th colspan="6">ДЦС-3 ӨДХ</th>
                        <th colspan="6">ДЦС-3 ДДХ</th>
                        <th colspan="15">ДЦС-4</th>
                        <th colspan="8">Амгалан ДС</th>
                    </tr>
                    <tr>
                        <!-- ДЦС-2 -->
                        <th>Gсул</th>
                        <th>Gну</th>
                        <th>P1</th>
                        <th>P2</th>
                        <th>T1</th>
                        <th>T2</th>
                        <!-- ДЦС-3 ӨДХ -->
                        <th>Gсул</th>
                        <th>Gну</th>
                        <th>P1</th>
                        <th>P2</th>
                        <th>T1</th>
                        <th>T2</th>
                        <!-- ДЦС-3 ДДХ -->
                        <th>Gсул</th>
                        <th>Gну</th>
                        <th>P1</th>
                        <th>P2</th>
                        <th>T1</th>
                        <th>T2</th>
                        <!-- ДЦС-4 -->
                        <th>9a</th>
                        <th>10a</th>
                        <th>11a</th>
                        <th>15</th>
                        <th>16a</th>
                        <th>G сул (нийт)</th>
                        <th>Gну</th>
                        <th>P1</th>
                        <th>P2</th>
                        <th>T1</th>
                        <th>9a</th>
                        <th>10а</th>
                        <th>11а</th>
                        <th>15</th>
                        <th>16</th>
                        <!-- Амгалан -->
                        <th>G сул</th>
                        <th>G сул2</th>
                        <th>Gну</th>
                        <th>P1</th>
                        <th>P2</th>
                        <th>T1</th>
                        <th>T2</th>
                        <th>T2_2</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $row->infodate }} {{ $row->infotime }}</td>

                            <!-- ДЦС-2 -->
                            <td>{{ $row->pp2g1 }}</td>
                            <td>{{ $row->pp2gn }}</td>
                            <td>{{ $row->pp2p1 }}</td>
                            <td>{{ $row->pp2p2 }}</td>
                            <td>{{ $row->pp2t1 }}</td>
                            <td>{{ $row->pp2t2 }}</td>

                            <!-- ДЦС-3 ӨДХ -->
                            <td>{{ $row->pp3hg1 }}</td>
                            <td>{{ $row->pp3hgn }}</td>
                            <td>{{ $row->pp3hp1 }}</td>
                            <td>{{ $row->pp3hp2 }}</td>
                            <td>{{ $row->pp3ht1 }}</td>
                            <td>{{ $row->pp3ht2 }}</td>

                            <!-- ДЦС-3 ДДХ -->
                            <td>{{ $row->pp3lg1 }}</td>
                            <td>{{ $row->pp3lgn }}</td>
                            <td>{{ $row->pp3lp1 }}</td>
                            <td>{{ $row->pp3lp2 }}</td>
                            <td>{{ $row->pp3lt1 }}</td>
                            <td>{{ $row->pp3lt2 }}</td>

                            <!-- ДЦС-4 -->
                            <td>{{ $row->pp4700g1 }}</td>
                            <td>{{ $row->pp41000g1 }}</td>
                            <td>{{ $row->pp41200g1 }}</td>
                            <td>{{ $row->pp4y700g1 }}</td>
                            <td>{{ $row->pp4210g1 }}</td>
                            <td>{{ $row->pp4g }}</td>
                            <td>{{ $row->pp4gn }}</td>
                            <td>{{ $row->pp4p1 }}</td>
                            <td>{{ $row->pp4p2 }}</td>
                            <td>{{ $row->pp4t1 }}</td>
                            <td>{{ $row->pp4700t2 }}</td>
                            <td>{{ $row->pp41000t2 }}</td>
                            <td>{{ $row->pp41200t2 }}</td>
                            <td>{{ $row->pp4y700t2 }}</td>
                            <td>{{ $row->pp4210t2 }}</td>

                            <!-- Амгалан -->
                            <td>{{ $row->amg1 }}</td>
                            <td>{{ $row->amg2 }}</td>
                            <td>{{ $row->amgn }}</td>
                            <td>{{ $row->amp1 }}</td>
                            <td>{{ $row->amp2 }}</td>
                            <td>{{ $row->amt1 }}</td>
                            <td>{{ $row->amt2 }}</td>
                            <td>{{ $row->amt2_2 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Графикууд -->
            <div class="container my-4">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h6>📊 ДЦС-2: Усны температур (T1, T2)</h6>
                        <canvas id="chartDCS2" height="150"></canvas>
                    </div>

                    <div class="col-md-4 mb-4">
                        <h6>📊 ДЦС-3 ӨДХ: Усны температур (T1, T2)</h6>
                        <canvas id="chartDCS3HP" height="150"></canvas>
                    </div>

                    <div class="col-md-4 mb-4">
                        <h6>📊 ДЦС-3 ДДХ: Усны температур (T1, T2)</h6>
                        <canvas id="chartDCS3LP" height="150"></canvas>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h6>📊 ДЦС-4: Усны температур (T1, T2)</h6>
                        <canvas id="chartDCS4" height="150"></canvas>
                    </div>

                    <div class="col-md-4 mb-4">
                        <h6>📊 Амгалан ДС: Усны температур (T1, T2)</h6>
                        <canvas id="chartAM" height="150"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($data->pluck('infotime'));

        const makeDataset = (label, t1, t2, color1, color2) => ({
            labels,
            datasets: [{
                    label: label + ' T1 (шууд ус)',
                    data: t1,
                    borderColor: color1,
                    fill: false,
                    tension: 0.1
                },
                {
                    label: label + ' T2 (буцах ус)',
                    data: t2,
                    borderColor: color2,
                    fill: false,
                    tension: 0.1
                }
            ]
        });

        const config = (data) => ({
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        // Графикууд
        new Chart(document.getElementById('chartDCS2'), config(makeDataset('ДЦС-2', @json($data->pluck('pp2t1')),
            @json($data->pluck('pp2t2')), 'red', 'blue')));
        new Chart(document.getElementById('chartDCS3HP'), config(makeDataset('ДЦС-3 ӨДХ', @json($data->pluck('pp3ht1')),
            @json($data->pluck('pp3ht2')), 'red', 'blue')));
        new Chart(document.getElementById('chartDCS3LP'), config(makeDataset('ДЦС-3 ДДХ', @json($data->pluck('pp3lt1')),
            @json($data->pluck('pp3lt2')), 'red', 'blue')));
        new Chart(document.getElementById('chartDCS4'), config(makeDataset('ДЦС-4', @json($data->pluck('pp4t1')),
            @json($data->pluck('pp4700t2')), 'red', 'blue')));
        new Chart(document.getElementById('chartAM'), config(makeDataset('Амгалан ДС', @json($data->pluck('amt1')),
            @json($data->pluck('amt2')), 'red', 'blue')));
    </script>
@endsection
