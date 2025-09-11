@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Тасралтын мэдээ</h2>
            <a href="{{ route('tnews.create') }}" class="btn btn-primary">+ мэдээ оруулах </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Огноо</th>
                        <th>ТЗЭ</th>
                        <th>Тасралт</th>
                        <th>Тайлбар</th>
                        <th>Дутуу түгээсэн ЭХ</th>
                        <th>Үйлдэл</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($Tnews as $Tnews)
                        <tr>
                            <td>{{ $Tnews->date }}</td>
                            <td>{{ $Tnews->TZE }}</td>
                            <td>{{ $Tnews->tasralt }}</td>
                            <td>{{ $Tnews->ArgaHemjee }}</td>
                            <td>{{ $Tnews->HyzErchim }}</td>

                            <td>
                                <a href="{{ route('tnews.edit', $Tnews) }}" class="btn btn-sm btn-warning">Засах</a>

                                <form action="{{ route('tnews.destroy', $Tnews) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Устгахдаа итгэлтэй байна уу?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Устгах</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection
