@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Өнөөдрийн өгөгдөл</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Цаг</th>
                    <th colspan="2">АШ 257</th>
                    <th colspan="2">АШ 258</th>
                    <th colspan="2">Тойт 110</th>
                </tr>
                <tr>
                    <th></th>
                    <th>Импорт</th>
                    <th>Экспорт</th>
                    <th>Импорт</th>
                    <th>Экспорт</th>
                    <th>Импорт</th>
                    <th>Экспорт</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row->hour }}:00</td>
                        <td>{{ $row->import_257 }}</td>
                        <td>{{ $row->export_257 }}</td>
                        <td>{{ $row->import_258 }}</td>
                        <td>{{ $row->export_258 }}</td>
                        <td>{{ $row->import_110 }}</td>
                        <td>{{ $row->export_110 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
