@extends('layouts.admin')

@section('content')

<div class="container">
    <h2>Албан тушаал [засах]</h2>
    <form action="{{ route('divisions.update', $division->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Албан тушаал</label>
            <input type="text" name="Div_name" value="{{ $division->Div_name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Албан тушаал код</label>
            <input type="text" name="Div_code" value="{{ $division->Div_code }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Засварлах</button>
    </form>
</div>
@endsection
