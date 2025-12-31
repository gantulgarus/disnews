@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Термо тоног төхөөрөмжийн жагсаалт</h2>
                    <a href="{{ route('power-plant-thermo-equipments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Шинэ нэмэх
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Станц</th>
                                <th>Код</th>
                                <th>Нэр</th>
                                <th>Хэмжих нэгж</th>
                                <th>Цахилгаан станц</th>
                                <th>Үүсгэсэн огноо</th>
                                <th class="text-end">Үйлдэл</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipments as $equipment)
                                <tr>
                                    <td>{{ $equipment->id }}</td>
                                    <td>{{ $equipment->powerPlant->name ?? '-' }}</td>
                                    <td><span class="badge bg-secondary text-white">{{ $equipment->code }}</span></td>
                                    <td>{{ $equipment->name }}</td>
                                    <td>{{ $equipment->unit ?? '-' }}</td>
                                    <td>{{ $equipment->powerPlant->name ?? '-' }}</td>
                                    <td>{{ $equipment->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('power-plant-thermo-equipments.show', $equipment) }}"
                                                class="btn btn-sm btn-info" title="Дэлгэрэнгүй">
                                                Харах
                                            </a>
                                            <a href="{{ route('power-plant-thermo-equipments.edit', $equipment) }}"
                                                class="btn btn-sm btn-warning" title="Засах">
                                                Засах
                                            </a>
                                            <form action="{{ route('power-plant-thermo-equipments.destroy', $equipment) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Устгахдаа итгэлтэй байна уу?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Устгах">
                                                    Устгах
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">Тоног төхөөрөмж олдсонгүй</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $equipments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
