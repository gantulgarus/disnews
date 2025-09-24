@extends('layouts.admin')

@section('content')

<div class="container">
    <h1>Байгууллага</h1>
    <a href="{{ route('organizations.create') }}" class="btn btn-primary mb-3">+ Байгууллага нэмэх</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Байгууллага код</th>
            <th>Байгууллага нэр</th>
            <th>Үйлдэл</th>
        </tr>
        @foreach ($organizations as $org)
        <tr>
            <td>{{ $org->id }}</td>
            <td>{{ $org->org_code }}</td>
            <td>{{ $org->name }}</td>
            <td>
                <a href="{{ route('organizations.edit', $org->id) }}" class="btn btn-warning">Засах</a>
                <form action="{{ route('organizations.destroy', $org->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" onclick="return confirm('Устгах уу?')">Устгах</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
