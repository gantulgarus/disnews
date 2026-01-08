@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Цэсний эрхийн жагсаалт</h3>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Шинэ эрх нэмэх
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-vcenter card-table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Нэр (System)</th>
                            <th>Нэр (Display)</th>
                            <th>Бүлэг</th>
                            <th>Тайлбар</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $perm)
                            <tr>
                                <td>{{ $perm->name }}</td>
                                <td>{{ $perm->display_name }}</td>
                                <td>{{ $perm->group ?? '-' }}</td>
                                <td>{{ $perm->description ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
