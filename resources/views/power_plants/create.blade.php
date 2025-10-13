@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <h1 class="page-title">Шинэ станц үүсгэх</h1>

        <form action="{{ route('power-plants.store') }}" method="POST" class="card">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Станцын нэр</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Товч нэр</label>
                    <input type="text" name="short_name" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="ti ti-device-floppy"></i> Хадгалах
                </button>
            </div>
        </form>
    </div>
@endsection
