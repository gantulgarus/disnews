@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <h3 class="fw-bold mb-4">Шинэ цэсний эрх нэмэх</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Эрхийн нэр (system name)</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="dashboard.view"
                    required>
            </div>

            <div class="mb-3">
                <label for="display_name" class="form-label">Нэр (display name)</label>
                <input type="text" class="form-control" id="display_name" name="display_name"
                    placeholder="Хянах самбар харах" required>
            </div>

            <div class="mb-3">
                <label for="group" class="form-label">Бүлэг (цэс)</label>
                <input type="text" class="form-control" id="group" name="group"
                    placeholder="dashboard, users, reports">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Тайлбар</label>
                <textarea class="form-control" id="description" name="description" rows="2"
                    placeholder="Энэ эрх нь хянах самбарыг харах боломж олгоно"></textarea>
            </div>

            <div class="text-end">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Буцах</a>
                <button type="submit" class="btn btn-primary">Хадгалах</button>
            </div>
        </form>
    </div>
@endsection
