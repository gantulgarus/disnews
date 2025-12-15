@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Шинэ бүртгэл нэмэх</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('power-energy-adjustments.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Огноо</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}"
                    required>
            </div>

            <div class="mb-3">
                <label>Хязгаарласан ЦЭХ (кВт.цаг)</label>
                <input type="number" step="0.001" name="restricted_kwh" value="{{ old('restricted_kwh') }}"
                    class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Хөнгөлсөн ЦЭХ (кВт.цаг)</label>
                <input type="number" step="0.001" name="discounted_kwh" value="{{ old('discounted_kwh') }}"
                    class="form-control" required>
            </div>



            <button type="submit" class="btn btn-primary">Хадгалах</button>
        </form>
    </div>
@endsection
