@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-3">Тоноглолын жагсаалт</h4>

        <a href="{{ route('equipments.create') }}" class="btn btn-primary mb-3">+ Шинэ тоноглол</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Нэр</th>
                    <th>Станц</th>
                    <th>Ангилал</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipments as $equipment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $equipment->name }}</td>
                        <td>{{ $equipment->powerPlant->name ?? '-' }}</td>
                        <td>{{ $equipment->type->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('equipments.edit', $equipment->id) }}" class="btn btn-sm btn-warning">Засах</a>
                            <form action="{{ route('equipments.destroy', $equipment->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Устгах уу?')">Устгах</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $equipments->links() }}
    </div>
@endsection
