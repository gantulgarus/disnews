@extends('layouts.admin')

@section('content')

<div class="container">
    <h2>Хэрэглэгчийн эрхийн түвшин</h2>
    <a href="{{ route('permission_levels.create') }}" class="btn btn-primary">+ эрхийн түвшин</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table table class="table table-bordered">
        <thead>
            <tr>
                <th>Нэршил</th>
                <th>Нэршил код</th>
                <th width="150px">Үйлдэл</th>
            </tr>
        </thead>
        <tbody>
            @foreach($levels as $level)
                <tr>
                    <td>{{ $level->name }}</td>
                    <td>{{ $level->code }}</td>
                    <td>
                        <a href="{{ route('permission_levels.edit', $level) }}" class="btn btn-warning btn-sm">Засах</a>
                        <form action="{{ route('permission_levels.destroy', $level) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('устгах ?')">Устгах</button>
                        </form>
                    </td>
                </tr>
            @endforeach            
    </table>
</div>




@endsection
