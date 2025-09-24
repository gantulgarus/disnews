@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Байгууллага [Засах]</h2>
    <form method="POST" action="{{ route('organizations.update', $organization->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Байгууллага код: </label>
            <input type="text" name="org_code" value="{{ $organization->org_code }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Байгууллага нэр: </label>
            <input type="text" name="name" value="{{ $organization->name }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Хадгалах</button>
    </form>
</div>
@endsection
