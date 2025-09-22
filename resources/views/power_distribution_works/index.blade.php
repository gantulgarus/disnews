@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Захиалгат ажлууд</h1>
        <a href="{{ route('power-distribution-works.create') }}" class="btn btn-primary mb-3">Шинэ ажил нэмэх</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ТЗЭ</th>
                    <th>Засварын ажлын утга</th>
                    <th>Тайлбар</th>
                    <th>Хязгаарласан эрчим хүч</th>
                    <th>Огноо</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($works as $work)
                    <tr>
                        <td>{{ $work->tze }}</td>
                        <td>{{ $work->repair_work }}</td>
                        <td>{{ $work->description }}</td>
                        <td>{{ $work->restricted_energy }}</td>
                        <td>{{ $work->date }}</td>
                        <td>
                            <a href="{{ route('power_distribution_works.edit', $work) }}"
                                class="btn btn-sm btn-warning">Засах</a>
                            <form action="{{ route('power_distribution_works.destroy', $work) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Устгах уу?')">Устгах</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $works->links() }}
    </div>
@endsection
