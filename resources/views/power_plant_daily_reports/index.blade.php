@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Цахилгаан станцын өдөр тутмын тайлан</h1>
        <a href="{{ route('power-plant-daily-reports.create') }}" class="btn btn-primary mb-3">Нэмэх</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th rowspan="2">№</th>
                        <th rowspan="2">Огноо</th>
                        <th rowspan="2">Станц</th>
                        <th colspan="3" class="text-center">Зуух</th>
                        <th colspan="3" class="text-center">Турбин</th>
                        <th rowspan="2">P (МВт)</th>
                        <th rowspan="2">Pmax (МВт)</th>
                        <th rowspan="2">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                        <th rowspan="2">Үйлдэл</th>
                    </tr>
                    <tr>
                        <th>Ажилд</th>
                        <th>Бэлтгэлд</th>
                        <th>Засварт</th>
                        <th>Ажилд</th>
                        <th>Бэлтгэлд</th>
                        <th>Засварт</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $index => $report)
                        <tr>
                            <td>{{ ($reports->currentPage() - 1) * $reports->perPage() + $index + 1 }}</td>
                            <td>{{ $report->report_date }}</td>
                            <td>{{ $report->powerPlant->name }}</td>

                            <!-- Boiler Working -->
                            <td>
                                @if ($report->boiler_working)
                                    @foreach (json_decode($report->boiler_working) as $boiler_id)
                                        <p>{{ \App\Models\Boiler::find($boiler_id)->name }}</p>
                                    @endforeach
                                @else
                                    <p></p>
                                @endif
                            </td>

                            <!-- Boiler Preparation -->
                            <td>
                                @if ($report->boiler_preparation)
                                    @foreach (json_decode($report->boiler_preparation) as $boiler_id)
                                        <p>{{ \App\Models\Boiler::find($boiler_id)->name }}</p>
                                    @endforeach
                                @else
                                    <p></p>
                                @endif
                            </td>

                            <!-- Boiler Repair -->
                            <td>
                                @if ($report->boiler_repair)
                                    @foreach (json_decode($report->boiler_repair) as $boiler_id)
                                        <p>{{ \App\Models\Boiler::find($boiler_id)->name }}</p>
                                    @endforeach
                                @else
                                    <p></p>
                                @endif
                            </td>

                            <!-- Turbine Working -->
                            <td>
                                @if ($report->turbine_working)
                                    @foreach (json_decode($report->turbine_working) as $turbine_id)
                                        <p>{{ \App\Models\TurbineGenerator::find($turbine_id)->name }}</p>
                                    @endforeach
                                @else
                                    <p></p>
                                @endif
                            </td>

                            <!-- Turbine Preparation -->
                            <td>
                                @if ($report->turbine_preparation)
                                    @foreach (json_decode($report->turbine_preparation) as $turbine_id)
                                        <p>{{ \App\Models\TurbineGenerator::find($turbine_id)->name }}</p>
                                    @endforeach
                                @else
                                    <p></p>
                                @endif
                            </td>

                            <!-- Turbine Repair -->
                            <td>
                                @if ($report->turbine_repair)
                                    @foreach (json_decode($report->turbine_repair) as $turbine_id)
                                        <p>{{ \App\Models\TurbineGenerator::find($turbine_id)->name }}</p>
                                    @endforeach
                                @else
                                    <p></p>
                                @endif
                            </td>
                            <td>{{ $report->power_max ?? '-' }}</td>
                            <td>{{ $report->power ?? '-' }}</td>
                            <td>{{ $report->notes ?? '-' }}</td>


                            <td>
                                <!-- Actions (Edit, Delete, etc.) -->
                                {{-- <a href="{{ route('power-plant-daily-reports.edit', $report->id) }}"
                                    class="btn btn-warning">Засах</a>
                                <form action="{{ route('power-plant-daily-reports.destroy', $report->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Устгах</button>
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $reports->links() }}
    </div>
@endsection
