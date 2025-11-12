@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>{{ $powerPlant->name }} — тоноглолын төлөвийн дэлгэрэнгүй мэдээ</h4>
            <a href="{{ route('daily-equipment-report.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Буцах
            </a>
        </div>

        @if ($statuses->isEmpty())
            <div class="alert alert-info">
                Энэ станцын бүртгэл олдсонгүй.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Огноо</th>
                            <th>Цаг</th>
                            <th>Тоноглолын нэр</th>
                            <th>Төрөл</th>
                            <th>Төлөв</th>
                            <th>Тайлбар</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statuses as $status)
                            <tr>
                                <td>{{ $status->date ? $status->date : '-' }}</td>
                                <td>{{ $status->created_at ? $status->created_at : '-' }}</td>
                                <td>{{ $status->equipment->name ?? '-' }}</td>
                                <td>{{ $status->equipment->type->name ?? '-' }}</td>
                                <td>{{ $status->status ?? '-' }}</td>
                                <td>{{ $status->remark ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
