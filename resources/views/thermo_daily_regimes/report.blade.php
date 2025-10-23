@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title text-uppercase fw-bold mb-0">
                    ТБНС-НИЙ ЭХ ҮҮСВЭР, СҮЛЖЭЭНИЙ ДУЛААНЫ ХОНОГИЙН АЖИЛЛАГААНЫ ГОРИМ
                </h3>

                <form action="{{ route('thermo-daily-regimes.index') }}" method="GET"
                    class="d-flex align-items-center gap-2">
                    <input type="date" id="date" name="date" value="{{ request('date', $date) }}"
                        class="form-control">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Хайх
                    </button>
                </form>
            </div>




            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">ЦАГ</th>
                            @foreach ($powerPlants as $plant)
                                <th colspan="8">{{ $plant->name }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($powerPlants as $plant)
                                <th>t°</th>
                                <th>t1</th>
                                <th>t2</th>
                                <th>P1</th>
                                <th>P2</th>
                                <th>D</th>
                                <th>G</th>
                                <th>Q</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach (['0-8', '8-16', '16-24'] as $range)
                            <tr>
                                <td class="fw-semibold">{{ $range }}</td>
                                @foreach ($powerPlants as $plant)
                                    @php
                                        $data = $regimes
                                            ->where('power_plant_id', $plant->id)
                                            ->where('time_range', $range)
                                            ->first();
                                    @endphp
                                    <td>{{ $data->temperature ?? '-' }}</td>
                                    <td>{{ $data->t1 ?? '-' }}</td>
                                    <td>{{ $data->t2 ?? '-' }}</td>
                                    <td>{{ $data->p1 ?? '-' }}</td>
                                    <td>{{ $data->p2 ?? '-' }}</td>
                                    <td>{{ $data->d ?? '-' }}</td>
                                    <td>{{ $data->g ?? '-' }}</td>
                                    <td class="fw-semibold text-primary">{{ $data->q ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td>Qнийт</td>
                            @foreach ($powerPlants as $plant)
                                @php
                                    $sumQ = $regimes->where('power_plant_id', $plant->id)->sum('q');
                                @endphp
                                <td colspan="8" class="text-primary text-end">
                                    Qнийт : {{ number_format($sumQ, 3, '.', ' ') }}
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
