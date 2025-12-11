@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>БХС хоногийн тооцооны журнал</h3>

        <a href="{{ route('daily-balance-batteries.create') }}" class="btn btn-primary mb-3">
            + Шинэ нэмэх
        </a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Станц</th>
                    <th>Огноо</th>
                    <th>ТБНС-ээс авсан (MWh)</th>
                    <th>ТБНС-д нийлүүлсэн (MWh)</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->powerPlant->name ?? '-' }}</td>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->energy_taken }}</td>
                        <td>{{ $row->energy_given }}</td>
                        <td>
                            <a href="{{ route('daily-balance-batteries.edit', $row->id) }}"
                                class="btn btn-sm btn-warning">Засах</a>

                            <form action="{{ route('daily-balance-batteries.destroy', $row->id) }}" method="POST"
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
