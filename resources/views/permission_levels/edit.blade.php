@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    
    <h2>Хэрэглэгчийн эрхийн түвшин [засах]</h2>

    <form action="{{ route('permission_levels.update', $permissionLevel) }}" method="POST">

        @csrf @method('PUT')
        <div class="mb-3">
            <label>Нэршил</label>
            <input type="text" name="name" value="{{ $permissionLevel->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Нэршил код</label>
            <input type="text" name="code" value="{{ $permissionLevel->code }}" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Хадгалах</button>
    </form>
</div>
@endsection
