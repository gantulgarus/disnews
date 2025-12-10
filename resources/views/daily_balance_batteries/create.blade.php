@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>БХС - Шинэ бичлэг нэмэх</h3>

        <form action="{{ route('daily-balance-batteries.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Станц</label>
                <select name="power_plant_id" class="form-control">
                    @foreach ($plants as $plant)
                        <option value="{{ $plant->id }}" {{ old('power_plant_id') == $plant->id ? 'selected' : '' }}>
                            {{ $plant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Огноо</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>ТБНС-ээс авсан (MWh)</label>
                <input type="number" step="0.001" name="energy_taken" value="{{ old('energy_taken') }}"
                    class="form-control">
            </div>

            <div class="mb-3">
                <label>ТБНС-д нийлүүлсэн (MWh)</label>
                <input type="number" step="0.001" name="energy_given" value="{{ old('energy_given') }}"
                    class="form-control">
            </div>

            <button class="btn btn-primary">Хадгалах</button>
        </form>
    </div>
@endsection
