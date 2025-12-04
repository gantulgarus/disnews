@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Байгууллага</h2>
    <form method="POST" action="{{ route('organizations.store') }}">
        @csrf
        <div class="mb-3">
            <label>Байгууллага код: </label>
            <input type="text" name="org_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Байгууллага нэр: </label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Хадгалах</button>
    </form>
</div>
@endsection
