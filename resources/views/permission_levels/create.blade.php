@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    <h2>Хэрэглэгчийн эрхийн түвшин</h2>

    <form action="{{ route('permission_levels.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Нэршил</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Нэршил код</label>
            <input type="text" name="code" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Хадгалах</button>
    </form>
</div>   
@endsection
