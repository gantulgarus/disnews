@extends('layouts.admin')

@section('content')
    <style>
        .table thead th {
            white-space: normal;
        }
    </style>

    <div class="container-fluid">
        <h2 class="mb-4">Хоногийн тооцооны журнал</h2>

        <a href="{{ route('daily-balance-journals.create') }}" class="btn btn-primary mb-3">+ Шинээр бүртгэх</a>
        <a href="{{ route('daily-balance-journals.report') }}" class="btn btn-info mb-3">Тайлан</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead style="text-align: center;">
                <tr>
                    <th rowspan="3">Огноо</th>
                    <th rowspan="3">Станц</th>
                    <th rowspan="3">Боловсруулалт</th>
                    <th rowspan="3">Түгээлт</th>
                    <th rowspan="3">Д/Хэрэгцээ</th>
                    <th rowspan="3">Хувь</th>
                    <th colspan="5">Диспетчерийн графикийн хазайлт</th>
                    <th rowspan="3">Зөрчлийн шалтгаан</th>
                    <th rowspan="2" colspan="2">+ шийдвэр</th>
                    <th rowspan="3">Диспетчер</th>
                    <th rowspan="3">Үйлдэл</th>
                </tr>
                <tr>
                    <th rowspan="2">+ зөрчил</th>
                    <th colspan="2">- зөрчил</th>
                    <th rowspan="2">+ шийд</th>
                    <th rowspan="2">- шийд</th>
                </tr>
                <tr>
                    <th>спот</th>
                    <th>импорт</th>
                    <th>Хэрэглээний өсөлтөөр</th>
                    <th>Бусад станцын доголдлоор</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($journals as $journal)
                    <tr>
                        <td>{{ $journal->entry_date_time }}</td>
                        <td>{{ $journal->powerPlant->name ?? '—' }}</td>
                        <td>{{ $journal->processed_amount }}</td>
                        <td>{{ $journal->distribution_amount }}</td>
                        <td>{{ $journal->internal_demand }}</td>
                        <td>{{ $journal->percent }}</td>
                        <td>{{ $journal->positive_deviation }}</td>
                        <td>{{ $journal->negative_deviation_spot }}</td>
                        <td>{{ $journal->negative_deviation_import }}</td>
                        <td>{{ $journal->positive_resolution }}</td>
                        <td>{{ $journal->negative_resolution }}</td>
                        <td>{{ $journal->deviation_reason }}</td>
                        <td>{{ $journal->by_consumption_growth }}</td>
                        <td>{{ $journal->by_other_station_issue }}</td>
                        <td>{{ $journal->dispatcher_name }}</td>
                        <td>
                            <a href="{{ route('daily-balance-journals.show', $journal) }}"
                                class="btn btn-info btn-sm">Харах</a>
                            <a href="{{ route('daily-balance-journals.edit', $journal) }}"
                                class="btn btn-warning btn-sm">Засах</a>
                            <form action="{{ route('daily-balance-journals.destroy', $journal) }}" method="POST"
                                style="display:inline;">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Устгах уу?')" class="btn btn-danger btn-sm">Устгах</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $journals->links() }}
    </div>
@endsection
