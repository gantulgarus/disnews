@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    <h3>Хоногийн ачааллын цаг засах</h3>

   

    {{-- Амжилтын мэдэгдэл --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Алдааны мэдэгдэл --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


   
 
<form action="{{ route('daily_power_hour_reports.updateByTime', $report->time) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="hidden" name="power_plant_id" value="{{ $report->power_plant_id }}">
    <input type="hidden" name="date" value="{{ $report->date ?? date('Y-m-d') }}">

    <table class="table">
        <thead>
            <tr>
                <th>Тоноглол</th>
                <th>Утга</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipments as $eq)
            <tr>
                <td>{{ $eq->power_equipment }}</td>
                <td>
                    <input type="hidden" name="equipments[{{ $loop->index }}][id]" value="{{ $eq->id }}">
                    <input type="text" name="equipments[{{ $loop->index }}][power_value]" 
                           value="{{ $pivotValues[$eq->id] ?? '' }}" class="form-control">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Шинэчлэх</button>
</form>


</div>
@endsection
