@extends('layouts.admin')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">📩 Илгээсэн мессежүүд</h3>
                    <a href="{{ route('sms.index') }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-send"></i> Мессеж илгээх
                    </a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success m-3">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Бүлгийн нэр</th>
                                <th>Мессеж</th>
                                <th>Хүлээн авагчдын тоо</th>
                                <th>Огноо</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $index => $msg)
                                <tr>
                                    <td>{{ $messages->firstItem() + $index }}</td>
                                    <td>
                                        @if ($msg->group_ids)
                                            {{ implode(', ', array_map(fn($id) => $allGroups[$id] ?? '-', $msg->group_ids)) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td style="max-width: 400px; white-space: normal;">{{ $msg->message }}</td>
                                    <td>{{ $msg->recipients_count }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($msg->sent_at)->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Илгээсэн мессеж олдсонгүй.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    {{ $messages->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
