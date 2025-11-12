@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h4 class="mb-3">Эх үүсвэрүүдийн тоноглолын төлвийн мэдээ</h4>

        <form method="GET" action="{{ route('daily-equipment-report.index') }}" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <input type="date" name="date" value="{{ $date }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary">Харах</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-sm align-middle mt-2">
            <thead class="table-light">
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Станцууд</th>
                    <th colspan="3" class="text-center">Зуух</th>
                    <th colspan="3" class="text-center">Турбогенератор</th>
                    <th colspan="3" class="text-center">Багц</th>
                    <th colspan="3" class="text-center">Инвертер</th>
                    <th rowspan="2">P (МВт)</th>
                    <th rowspan="2">P max (МВт)</th>
                    <th rowspan="2">Үндсэн тоноглолын засвар, гарсан доголдол</th>
                    <th rowspan="2"></th>
                </tr>
                <tr>
                    <th>Ажилд</th>
                    <th>Бэлтгэлд</th>
                    <th>Засварт</th>
                    <th>Ажилд</th>
                    <th>Бэлтгэлд</th>
                    <th>Засварт</th>
                    <th>Ажилд</th>
                    <th>Бэлтгэлд</th>
                    <th>Засварт</th>
                    <th>Ажилд</th>
                    <th>Бэлтгэлд</th>
                    <th>Засварт</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($powerPlants as $plant)
                    @php
                        $boilers = $plant->equipments->where('equipment_type_id', 1);
                        $turbos = $plant->equipments->where('equipment_type_id', 2);
                        $inverters = $plant->equipments->where('equipment_type_id', 3);
                        $batches = $plant->equipments->where('equipment_type_id', 4);
                        $statuses = $plant->equipmentStatuses->keyBy('equipment_id');
                        $info = $plant->powerInfos->first();
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $plant->name }}</td>

                        {{-- Зуух --}}
                        <td>{{ $boilers->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $boilers->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $boilers->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                        </td>

                        {{-- Турбогенератор --}}
                        <td>{{ $turbos->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $turbos->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $turbos->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                        </td>

                        {{-- Багц --}}
                        <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $batches->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                        </td>

                        {{-- Инвертер --}}
                        <td>{{ $inverters->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Ажилд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $inverters->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Бэлтгэлд')->pluck('name')->join(', ') }}
                        </td>
                        <td>{{ $inverters->filter(fn($e) => ($statuses[$e->id]->status ?? null) === 'Засварт')->pluck('name')->join(', ') }}
                        </td>

                        <td>{{ $info?->p }}</td>
                        <td>{{ $info?->p_max }}</td>
                        <td>{{ $info?->remark }}</td>

                        {{-- Edit button --}}
                        <td>
                            <a href="{{ route('daily-equipment-report.create', ['powerPlant' => $plant->id]) }}"
                                class="btn btn-sm btn-primary mb-2">
                                Мэдээ оруулах
                            </a>

                            {{-- <a href="{{ route('daily-equipment-report.edit', ['powerPlant' => $plant->id, 'date' => $date]) }}"
                                class="btn btn-sm btn-warning">
                                Засах
                            </a> --}}
                            {{-- 🆕 Дэлгэрэнгүй товч --}}
                            {{-- <a href="{{ route('daily-equipment-report.details', ['powerPlant' => $plant->id]) }}"
                                class="btn btn-sm btn-info">
                                Дэлгэрэнгүй
                            </a> --}}
                            <form action="{{ route('daily-equipment-report.destroy', ['powerPlant' => $plant->id]) }}"
                                method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Та энэ станцын бүх мэдээг устгахыг хүсэж байна уу?')">
                                    Устгах
                                </button>
                            </form>

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">Мэдээлэл байхгүй</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
@endsection
