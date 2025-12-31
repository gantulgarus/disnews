@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $powerPlant->name }} - {{ $request->date }} {{ $request->hour }}ц</h5>
                        <a href="{{ route('power-plant-readings.index', ['date' => $request->date]) }}"
                            class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Буцах
                        </a>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('power-plant-readings.updateBulk') }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="power_plant_id" value="{{ $request->power_plant_id }}">
                            <input type="hidden" name="date" value="{{ $request->date }}">
                            <input type="hidden" name="hour" value="{{ $request->hour }}">

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Код</th>
                                            <th>Тоног төхөөрөмж</th>
                                            <th>Нэгж</th>
                                            <th>Утга</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($equipments as $index => $equipment)
                                            @php
                                                $value = $existingReadings[$equipment->id]->value ?? '';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><code>{{ $equipment->code }}</code></td>
                                                <td>{{ $equipment->name }}</td>
                                                <td>{{ $equipment->unit ?? '-' }}</td>
                                                <td>
                                                    <input type="number" step="0.01"
                                                        name="readings[{{ $equipment->id }}]"
                                                        class="form-control form-control-sm"
                                                        value="{{ old('readings.' . $equipment->id, $value) }}"
                                                        placeholder="0.00">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Хадгалах
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
