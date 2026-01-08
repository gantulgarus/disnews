@extends('layouts.admin')

@section('content')
    <div class="page">
        <div class="container-xl">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">Телефон мэдээ</h2>
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <a href="{{ route('telephone_messages.create') }}" class="btn btn-primary">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Шинэ мэдээ
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- Tabs -->
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="phoneMessageTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="received-tab" data-bs-toggle="tab" href="#received"
                                        role="tab" aria-controls="received" aria-selected="true">Ирсэн</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="sent-tab" data-bs-toggle="tab" href="#sent" role="tab"
                                        aria-controls="sent" aria-selected="false">Явсан</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <!-- Received -->
                                <div class="tab-pane show active" id="received" role="tabpanel"
                                    aria-labelledby="received-tab">
                                    @if ($received->isEmpty())
                                        <div class="empty">
                                            <div class="empty-icon">📭</div>
                                            <p class="empty-title">Ирсэн мэдээ байхгүй байна</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table card-table table-vcenter">
                                                <thead>
                                                    <tr>
                                                        <th>Төлөв</th>
                                                        <th>Илгээгч</th>
                                                        <th>Агуулга</th>
                                                        <th>Огноо</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($received as $msg)
                                                        <tr>
                                                            <td>
                                                                @foreach ($msg->receivers as $receiver)
                                                                    @if ($receiver->id === auth()->user()->organization_id)
                                                                        @if ($receiver->pivot->status === 'Шинээр ирсэн')
                                                                            <span class="badge bg-warning-lt">Шинээр
                                                                                ирсэн</span>
                                                                        @else
                                                                            <span class="badge bg-success-lt">Хүлээн
                                                                                авсан</span>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            </td>

                                                            <td>{{ optional($msg->senderOrganization)->name ?? $msg->sender_org_id }}
                                                            </td>
                                                            <td>{{ \Illuminate\Support\Str::limit($msg->content, 80) }}</td>
                                                            <td>{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('telephone_messages.show', $msg) }}"
                                                                    class="btn btn-sm btn-outline-primary">Дэлгэрэнгүй</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>

                                <!-- Sent -->
                                <div class="tab-pane" id="sent" role="tabpanel" aria-labelledby="sent-tab">
                                    @if ($sent->isEmpty())
                                        <div class="empty">
                                            <div class="empty-icon">✉️</div>
                                            <p class="empty-title">Явсан мэдээ байхгүй байна</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table card-table table-vcenter">
                                                <thead>
                                                    <tr>
                                                        <th>Төлөв</th>
                                                        <th>Хүлээн авагч</th>
                                                        <th>Агуулга</th>
                                                        <th>Огноо</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sent as $msg)
                                                        <tr>
                                                            <td>
                                                                @foreach ($msg->receivers as $receiver)
                                                                    <span class="badge bg-light text-dark">
                                                                        {{ $receiver->name }} -
                                                                        {{ $receiver->pivot->status }}
                                                                    </span>
                                                                @endforeach
                                                            </td>

                                                            <td>
                                                                @if (is_array($msg->receiver_org_ids))
                                                                    @foreach ($msg->receiver_org_ids as $rid)
                                                                        <span
                                                                            class="badge bg-light text-dark">{{ \App\Models\Organization::find($rid)->name ?? $rid }}</span>
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                            <td>{{ \Illuminate\Support\Str::limit($msg->content, 80) }}</td>
                                                            <td>{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('telephone_messages.show', $msg) }}"
                                                                    class="btn btn-sm btn-outline-primary">Дэлгэрэнгүй</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- end tab-content -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
