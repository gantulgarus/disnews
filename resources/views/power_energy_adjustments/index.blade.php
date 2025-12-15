@extends('layouts.admin')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between mb-3">
            <h2>ЦЭХ Бүртгэл</h2>
            <a class="btn btn-primary" href="{{ route('power-energy-adjustments.create') }}">Шинээр нэмэх</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Хязгаарласан кВт.цаг</th>
                    <th>Хөнгөлсөн кВт.цаг</th>
                    <th>Огноо</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($adjustments as $adj)
                    <tr>
                        <td>{{ $adj->id }}</td>
                        <td>{{ $adj->restricted_kwh }}</td>
                        <td>{{ $adj->discounted_kwh }}</td>
                        <td>{{ $adj->date->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('power-energy-adjustments.show', $adj->id) }}"
                                class="btn btn-info btn-sm">Харах</a>
                            <a href="{{ route('power-energy-adjustments.edit', $adj->id) }}"
                                class="btn btn-warning btn-sm">Засах</a>

                            <form action="{{ route('power-energy-adjustments.destroy', $adj->id) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Устгах уу?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Устгах</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $adjustments->links() }}
@endsection
