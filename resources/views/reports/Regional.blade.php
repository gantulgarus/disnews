@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Раойнуудын оргил ачааллын гүйцэтгэл</h3>

   <div class="mb-3">
        <span class="px-2 py-1 bg-success text-white me-2" style="border-radius: 4px;">
             ({{ $yearPrevious }}) амралтын өдөр
        </span>
        <span class="px-2 py-1 bg-warning text-dark" style="border-radius: 4px;">
             ({{ $yearCurrent }}) амралтын өдөр
        </span>
   </div>

    <form method="GET" action="{{ route('reports.Regional') }}" class="row g-3 align-items-center mb-4">
        <div class="col-auto">
            <label for="month" class="col-form-label">Сар:</label>
        </div>
        <div class="col-auto">
            <input type="month" id="month" name="month"
                   value="{{ $selectedMonth }}"
                   class="form-control form-control-sm"
                   max="{{ now()->format('Y-m') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">Харах</button>
        </div>
    </form>

   <table class="table table-bordered table-sm text-center" style="font-size: 12px; white-space: nowrap">
    <thead>
        <tr>
            <th>Станц</th>
            <th>Он</th>
            @for($i = 1; $i <= $daysInMonth; $i++)
                <th>{{ $i }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($data as $var => $rows)
            @php
                $rows = collect($rows);
                $current  = $rows->firstWhere('row_type', 'CURRENT');
                $previous = $rows->firstWhere('row_type', 'PREVIOUS');
                if (!$current || !$previous) continue;
                $stationName = $vars[$var] ?? $var;
            @endphp

            @foreach(['PREVIOUS' => $previous, 'CURRENT' => $current] as $label => $row)
                <tr>
                    @if($label === 'PREVIOUS')
                        <td rowspan="2" class="text-start align-middle fw-bold">{{ $stationName }}</td>
                    @endif

                    <td class="text-start"><small>{{ $label == 'CURRENT' ? $yearCurrent : $yearPrevious }}</small></td>

                    @for($i = 1; $i <= $daysInMonth; $i++)
                        @php
                            $day = 'd'.str_pad($i,2,'0',STR_PAD_LEFT);

                            $currentValue  = isset($current->$day)  ? $current->$day  : 0;
                            $previousValue = isset($previous->$day) ? $previous->$day : 0;

                            $value = $label == 'CURRENT' 
                                ? number_format($currentValue,2) . '<br><small class="text-danger">' . number_format($currentValue - $previousValue,2) . '</small>'
                                : number_format($previousValue,2);

                            // Амралтын өдрийг шалгах
                            $date = \Carbon\Carbon::create($label == 'CURRENT' ? $yearCurrent : $yearPrevious, $month, $i);
                            $isWeekend = $date->isWeekend();

                            $bgColor = '';
                            if ($isWeekend) {
                                $bgColor = $label == 'CURRENT' ? 'bg-warning text-dark' : 'bg-success text-white';
                            }
                        @endphp
                        <td class="{{ $bgColor }}">{!! $value !!}</td>
                    @endfor
                </tr>
            @endforeach
        @endforeach
        @php
    $sumRow = [];
    for($i = 1; $i <= $daysInMonth; $i++) {
        $day = 'd'.str_pad($i,2,'0',STR_PAD_LEFT);
        $sumPrevious = 0;
        $sumCurrent = 0;

        foreach($data as $var => $rows) {
            $rows = collect($rows);
            $current  = $rows->firstWhere('row_type', 'CURRENT');
            $previous = $rows->firstWhere('row_type', 'PREVIOUS');
            if (!$current || !$previous) continue;

            $sumPrevious += isset($previous->$day) ? $previous->$day : 0;
            $sumCurrent  += isset($current->$day)  ? $current->$day  : 0;
        }

        $sumRow[$day] = [
            'previous' => $sumPrevious,
            'current'  => $sumCurrent,
            'diff'     => $sumCurrent - $sumPrevious
        ];
    }
@endphp

{{-- Говь станцын дараа нийлбэр --}}
<tr class="fw-bold bg-light">
    <td class="text-start">Нийт</td>
    <td></td> {{-- Он хоосон --}}
    @for($i = 1; $i <= $daysInMonth; $i++)
        @php $day = 'd'.str_pad($i,2,'0',STR_PAD_LEFT); @endphp
        <td>
            <small>{{ number_format($sumRow[$day]['previous'],2) }}</small><br>
            <small>{{ number_format($sumRow[$day]['current'],2) }}</small><br>
            <small class="text-danger">{{ number_format($sumRow[$day]['diff'],2) }}</small>
        </td>
    @endfor
</tr>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="card mt-4">
    <div class="card-body">
        <h5 class="text-center mb-3">Районуудын нийлбэр (2024 vs 2025)</h5>
        <canvas id="summaryChart" height="120"></canvas>
    </div>
</div>

<script>
const labels = @json($days);

const data2024 = @json($sum2024);
const data2025 = @json($sum2025);
const diffData = @json($diff);

new Chart(document.getElementById('summaryChart'), {
    data: {
        labels: labels,
        datasets: [
            {
                type: 'line',
                label: '2024 оны өдрийн нийлбэр',
                data: data2024,
                borderWidth: 2,
                tension: 0.3,
                yAxisID: 'y'
            },
            {
                type: 'line',
                label: '2025 оны өдрийн нийлбэр',
                data: data2025,
                borderWidth: 2,
                tension: 0.3,
                yAxisID: 'y'
            },
            {
                type: 'bar',
                label: 'Зөрүү (2025 - 2024)',
                data: diffData,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        scales: {
            y: {
                position: 'left',
                title: {
                    display: true,
                    text: 'Нийлбэр'
                }
            },
            y1: {
                position: 'right',
                grid: {
                    drawOnChartArea: false
                },
                title: {
                    display: true,
                    text: 'Зөрүү'
                }
            }
        }
    }
});
</script>




</div>
@endsection
