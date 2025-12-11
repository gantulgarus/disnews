@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Импорт / Экспорт хоногийн мэдээлэл</h3>

        <a href="{{ route('daily-balance-import-exports.create') }}" class="btn btn-primary mb-3">
            + Шинэ бичлэг нэмэх
        </a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Станц</th>
                    <th>Огноо</th>
                    <th>Импорт (MWh)</th>
                    <th>Экспорт (MWh)</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $row)
                    <tr>
                        <td>{{ $row->powerPlant->name ?? '-' }}</td>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->import }}</td>
                        <td>{{ $row->export }}</td>
                        <td>
                            <a href="{{ route('daily-balance-import-exports.edit', $row->id) }}"
                                class="btn btn-sm btn-warning">Засах</a>

                            <form action="{{ route('daily-balance-import-exports.destroy', $row->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Устгах уу?')">Устгах</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
@endsection
