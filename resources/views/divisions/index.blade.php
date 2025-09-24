@extends('layouts.admin')

@section('content')

<div class="container">
    <h2>Албан тушаал</h2>
    <a href="{{ route('divisions.create') }}" class="btn btn-primary">+ Албан тушаал</a>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Албан тушаал</th>
                <th>Албан тушаал код</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($divisions as $division)
                <tr>
                    
                    <td>{{ $division->Div_name }}</td>
                    <td>{{ $division->Div_code }}</td>
                    <td>
                        <a href="{{ route('divisions.edit', $division->id) }}" class="btn btn-warning btn-sm">Засах</a>
                        <form action="{{ route('divisions.destroy', $division->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Устгах ?')">Устгах</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
