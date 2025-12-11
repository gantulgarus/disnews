@extends('layouts.admin')

@section('content')
    <div class="container mt-4">

        <div class="d-flex justify-content-between mb-3">
            <h3>Алтайн бүсийн ачааллын бүртгэл</h3>
            <a href="{{ route('altai-region-capacity.create') }}" class="btn btn-primary btn-sm">+ Шинэ бүртгэл</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>Огноо</th>
                    <th>Их ачаалал (МВт)</th>
                    <th>Бага ачаалал (МВт)</th>
                    <th>ББЭХС-ээс</th>
                    <th>ТБНС-ээс</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i)
                    <tr>
                        <td>{{ $i->date }}</td>
                        <td>{{ $i->max_load }}</td>
                        <td>{{ $i->min_load }}</td>
                        <td>{{ $i->import_from_bbexs }}</td>
                        <td>{{ $i->import_from_tbns }}</td>
                        <td>
                            <a href="{{ route('altai-region-capacity.edit', $i->id) }}"
                                class="btn btn-warning btn-sm">Засах</a>

                            <form action="{{ route('altai-region-capacity.destroy', $i->id) }}" method="POST"
                                style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Устгах уу?')">Устгах</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $items->links() }}

    </div>
@endsection
