@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Бүртгэл засах</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('power-energy-adjustments.update', $powerEnergyAdjustment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Хязгаарласан (МВт)</label>
                <input type="number" step="0.001" name="restricted_kwh" class="form-control"
                    value="{{ $powerEnergyAdjustment->restricted_kwh }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Хөнгөлсөн (МВт)</label>
                <input type="number" step="0.001" name="discounted_kwh" class="form-control"
                    value="{{ $powerEnergyAdjustment->discounted_kwh }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Огноо</label>
                <input type="date" name="date" class="form-control"
                    value="{{ $powerEnergyAdjustment->date->format('Y-m-d') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Шинэчлэх</button>
            <a href="{{ route('power-energy-adjustments.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
