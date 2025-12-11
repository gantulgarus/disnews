@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h3>Станцын тоноглолын ачааллын утгуудыг засварлах</h3>

    <form action="{{ route('daily_power_hour_reports.updateByPlantAndTime', [$powerPlantId, $time]) }}" method="POST">
   


        @csrf
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Тоноглол</th>
                    <th>Ачаалал</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipments as $e)
                <tr>
                    <td>{{ $e->equipment_name }}</td>
                    <td>
                        <input type="number" step="0.01" class="form-control"
                               name="power_value[{{ $e->id }}]"
                               value="{{ $records[$e->id]->power_value ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mb-3">
            <label>Өдөр:</label>
            <input type="date" class="form-control w-25" name="date"
                   value="{{ $date }}">
        </div>

        <button type="submit" class="btn btn-primary">Хадгалах</button>
    </form>
</div>
@endsection
