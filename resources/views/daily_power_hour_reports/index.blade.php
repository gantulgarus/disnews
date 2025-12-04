@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h3>Хоногийн ачааллын цагуудын мэдээ</h3>

   

<form method="GET" action="{{ route('daily_power_hour_reports.index') }}" class="mb-3">
    <div class="row">
        <div class="col-auto">
            <input type="date" name="date" value="{{ $date }}" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Хайх</button>
        </div>
    </div>
</form>

  <a href="{{ route('daily_power_hour_reports.create') }}" class="btn btn-success mb-3">+ Шинэ мэдээ</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Өдөр</th>
                <th>Цаг</th>

                @foreach($equipments as $e)
                    <th>{{ $e->power_equipment }}</th>
                @endforeach

                <th width="80">Үйлдэл</th>
            </tr>
        </thead>

        <tbody>
            @foreach($pivot as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['time'] }}</td>

                    @foreach($equipments as $e)
                        <td>{{ $row[$e->power_equipment] ?? '-' }}</td>
                    @endforeach

                    <td>
                        <a href="{{ route('daily_power_hour_reports.edit', $row['time']) }}" 
                            class="btn btn-sm btn-primary">
                                засах
                        </a>
                        

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>



</div>
@endsection
