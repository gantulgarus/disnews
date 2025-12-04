@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Хоногийн ачааллын тоноглол</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Хаах"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Хаах"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('daily_power_equipments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Тоноглол нэмэх
            </a>
        </div>

        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <td>#</td>
                    <th>Станц нэр</th>
                    <th>Тоноглол нэр</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($equipments as $index => $equipment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $equipment->powerPlant->name ?? 'N/A' }}</td>
                    <td>{{ $equipment->power_equipment }}</td>
                    <td>{{ $equipment->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('daily_power_equipments.edit', $equipment->id) }}" class="btn btn-sm btn-warning">Засах</a>

                        <form action="{{ route('daily_power_equipments.destroy', $equipment->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Устгахдаа итгэлтэй байна уу?')">
                                Устгах
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Тоноглол хоосон байна.</td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>
@endsection
