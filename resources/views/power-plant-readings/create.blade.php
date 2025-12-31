@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Гараар мэдээлэл бүртгэх (Бүх тоноглол)</h5>
                    </div>

                    <form action="{{ route('power-plant-readings.storeBulk') }}" method="POST">
                        @csrf

                        <div class="card-body">

                            {{-- Огноо --}}
                            <div class="mb-3">
                                <label class="form-label">Огноо</label>
                                <input type="date" name="reading_date" class="form-control"
                                    value="{{ old('reading_date', now()->format('Y-m-d')) }}" required>
                            </div>

                            {{-- Цаг --}}
                            <div class="mb-3">
                                <label class="form-label">Цаг</label>
                                <select name="reading_hour" class="form-select" required>
                                    <option value="">-- Цаг сонгох --</option>
                                    @for ($h = 1; $h <= 24; $h++)
                                        <option value="{{ $h }}"
                                            {{ old('reading_hour') == $h ? 'selected' : '' }}>
                                            {{ $h }} цаг
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Тоноглолууд --}}
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Станц</th>
                                        <th>Тоноглол</th>
                                        <th>Код</th>
                                        <th>Утга</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($equipments as $equipment)
                                        <tr>
                                            <td>{{ $equipment->powerPlant->name }}</td>
                                            <td>{{ $equipment->name }}</td>
                                            <td>{{ $equipment->code }}</td>
                                            <td>
                                                <input type="number" step="0.01" name="values[{{ $equipment->id }}]"
                                                    class="form-control" value="{{ old('values.' . $equipment->id) }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ route('power-plant-readings.index') }}" class="btn btn-secondary">
                                Буцах
                            </a>
                            <button type="submit" class="btn btn-primary">Хадгалах</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
