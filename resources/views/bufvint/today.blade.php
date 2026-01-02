@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Өнөөдрийн өгөгдөл</h3>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Цаг</th>
                    <th>АШ 257 - Импорт</th>
                    <th>АШ 257 - Экспорт</th>
                    <th>АШ 258 - Импорт</th>
                    <th>АШ 258 - Экспорт</th>
                    <th>Тойт 110 - Импорт</th>
                    <th>Тойт 110 - Экспорт</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pivot as $time => $fidData)
                    <tr>
                        <td>{{ $time }}</td>
                        <td>{{ $fidData[257]['IMPORT'] ?? 0 }}</td>
                        <td>{{ $fidData[257]['EXPORT'] ?? 0 }}</td>
                        <td>{{ $fidData[258]['IMPORT'] ?? 0 }}</td>
                        <td>{{ $fidData[258]['EXPORT'] ?? 0 }}</td>
                        <td>{{ $fidData[110]['IMPORT'] ?? 0 }}</td>
                        <td>{{ $fidData[110]['EXPORT'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    </div>
@endsection
