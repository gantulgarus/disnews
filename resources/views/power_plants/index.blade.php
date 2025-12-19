@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Эх үүсвэрийн лавлах сан</h1>
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
            <a href="{{ route('power-plants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Шинэ станц нэмэх
            </a>
        </div>

        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <td>#</td>
                    <th>Нэр</th>
                    <th>Богино нэр</th>
                    <th>Төрөл</th>
                    <th>Бүс</th>
                    <th>Эрэмбэ</th>
                    <th>Байгууллага</th>
                    <th>Нүүрсний тогтмол</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($powerPlants as $powerPlant)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $powerPlant->name }}</td>
                        <td>{{ $powerPlant->short_name }}</td>
                        <td>{{ $powerPlant->powerPlantType->name ?? 'Төрөл тодорхойгүй' }}</td>
                        <td>{{ $powerPlant->region }}</td>
                        <td>{{ $powerPlant->Order }}</td>
                        <td>{{ $powerPlant->organization->name ?? 'Байгууллага тодорхойгүй' }}</td>
                        <td>{{ $powerPlant->coal_constant }}</td>
                        <td>
                            <a href="{{ route('power-plants.edit', $powerPlant) }}"
                                class="btn btn-sm btn-warning">Засах</a>

                            <form action="{{ route('power-plants.destroy', $powerPlant) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Устгахдаа итгэлтэй байна уу?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Устгах</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
