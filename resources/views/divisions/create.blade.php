@extends('layouts.admin')

@section('content')

<div class="container">
    <h2>Албан тушаал</h2>
    <form action="{{ route('divisions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Албан тушаал</label>
            <input type="text" name="Div_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Албан тушаал код</label>
            <input type="text" name="Div_code" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Хадгалах</button>
    </form>
</div>
@endsection
