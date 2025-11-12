@extends('layouts.admin')

@section('content')
    <div class="container-xl py-4">
        <form action="{{ route('thermo-daily-regimes.update', $thermoDailyRegime) }}" method="POST" class="card">
            @csrf
            @method('PUT')

            <div class="card-header">
                <h3 class="card-title">Дулааны горим засах</h3>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Цахилгаан станц</label>
                        <select name="power_plant_id" class="form-select">
                            @foreach ($powerPlants as $p)
                                <option value="{{ $p->id }}" @selected($p->id == $thermoDailyRegime->power_plant_id)>{{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Огноо</label>
                        <input type="date" name="date" class="form-control" value="{{ $thermoDailyRegime->date }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Цагийн бүс</label>
                        <select name="time_range" class="form-select">
                            @foreach (['0-8', '8-16', '16-24'] as $range)
                                <option value="{{ $range }}" @selected($thermoDailyRegime->time_range == $range)>{{ $range }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @foreach ([
            'temperature' => 'Температур (°C)',
            't1' => 'T1',
            't2' => 'T2',
            'p1' => 'P1',
            'p2' => 'P2',
            'd' => 'D',
            'g' => 'G',
            'q' => 'Q',
            'q_total' => 'Q нийт',
        ] as $name => $label)
                        <div class="col-md-3">
                            <label class="form-label">{{ $label }}</label>
                            <input type="number" step="any" name="{{ $name }}"
                                value="{{ $thermoDailyRegime->$name }}" class="form-control">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('thermo-daily-regimes.index') }}" class="btn btn-secondary">Буцах</a>
                <button type="submit" class="btn btn-success">Хадгалах</button>
            </div>
        </form>
    </div>
@endsection
