@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Өнөөдрийн өгөгдөл</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Огноо</th>
                    <th>Interv</th>
                    <th>TIME_DISPLAY</th>
                    <th>OBEKT</th>
                    <th>SULJEE</th>
                    <th>FIDER</th>
                    <th>LINE_NAME</th>
                    <th>IMPORT_KWT</th>
                    <th>EXPORT_KWT</th>
                    <th>TOOTSOOLUUR_COUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row->OGNOO }}</td>
                        <td>{{ $row->TIME_INTERVAL }}</td>
                        <td>{{ $row->TIME_DISPLAY }}</td>
                        <td>{{ $row->OBEKT }}</td>
                        <td>{{ $row->SULJEE }}</td>
                        <td>{{ $row->FIDER }}</td>
                        <td>{{ $row->LINE_NAME }}</td>
                        <td>{{ $row->IMPORT_KWT }}</td>
                        <td>{{ $row->EXPORT_KWT }}</td>
                        <td>{{ $row->TOOTSOOLUUR_COUNT }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
