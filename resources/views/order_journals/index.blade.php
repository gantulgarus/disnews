@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Захиалгын Журнал</h2>
        <a href="{{ route('order-journals.create') }}" class="btn btn-primary mb-3">Шинэ захиалга үүсгэх</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Дугаар</th>
                    <th>Төлөв</th>
                    <th>Байгууллага</th>
                    <th>Төрөл</th>
                    <th>Засварын ажлын агуулга</th>
                    <th>Захиалга эхлэх хугацаа</th>
                    <th>Захиалга дуусах хугацаа</th>
                    <th>Баталсан</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($journals as $journal)
                    <tr>
                        <td>{{ $journal->order_number }}</td>
                        <td>
                            @php
                                $statusClass = match ($journal->status) {
                                    0 => 'badge bg-secondary',
                                    1 => 'badge bg-secondary',
                                    2 => 'badge bg-warning text-white',
                                    3 => 'badge bg-success',
                                    4 => 'badge bg-danger',
                                    default => 'badge bg-info',
                                };
                            @endphp
                            <span class="{{ $statusClass }}">
                                {{ \App\Models\OrderJournal::$STATUS_NAMES[$journal->status] ?? '-' }}
                            </span>
                        </td>
                        <td>{{ $journal->organization->name }}</td>
                        <td>{{ $journal->order_type }}</td>
                        <td>{{ $journal->content }}</td>
                        <td>{{ $journal->planned_start_date }}</td>
                        <td>{{ $journal->planned_end_date }}</td>
                        <td>{{ $journal->approver_name }}</td>
                        <td>
                            <a href="{{ route('order-journals.edit', $journal->id) }}"
                                class="btn btn-sm btn-warning">Засах</a>
                            <form action="{{ route('order-journals.destroy', $journal->id) }}" method="POST"
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
    </div>
@endsection
