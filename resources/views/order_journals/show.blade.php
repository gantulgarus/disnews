@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Захиалгын дэлгэрэнгүй</h2>

        <div class="row g-4">
            {{-- Зүүн багана: Захиалгын дэлгэрэнгүй --}}
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Дугаар: {{ $orderJournal->order_number }}</h5>
                        <p>
                            <strong>Төлөв:</strong>
                            @php
                                $statusClass = match ($orderJournal->status) {
                                    0 => 'badge bg-gray text-black',
                                    1 => 'badge bg-gray text-white',
                                    2 => 'badge bg-yellow text-dark',
                                    3 => 'badge bg-green text-white',
                                    4 => 'badge bg-red text-white',
                                    default => 'badge bg-blue text-white',
                                };
                            @endphp
                            <span class="{{ $statusClass }}">
                                {{ \App\Models\OrderJournal::$STATUS_NAMES[$orderJournal->status] ?? '-' }}
                            </span>
                        </p>
                        <p><strong>Байгууллага:</strong> {{ $orderJournal->organization->name }}</p>
                        <p><strong>Захиалгын төрөл:</strong> {{ $orderJournal->order_type }}</p>
                        <p><strong>Засварын ажлын агуулга:</strong> {{ $orderJournal->content }}</p>
                        <p><strong>Эхлэх хугацаа:</strong> {{ $orderJournal->planned_start_date }}</p>
                        <p><strong>Дуусах хугацаа:</strong> {{ $orderJournal->planned_end_date }}</p>
                        <p><strong>Баталсан:</strong> {{ $orderJournal->approver_name }}</p>
                        <p><strong>Баталсан албан тушаал:</strong> {{ $orderJournal->approver_position }}</p>
                    </div>
                </div>
            </div>

            {{-- Баруун багана: Санал өгөх --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Санал</h5>
                    </div>
                    <div class="card-body">
                        @forelse($orderJournal->approvals as $approval)
                            <div class="card mb-2 p-2">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <strong>{{ $approval->user->name }}</strong>
                                        @if (!is_null($approval->approved))
                                            <span
                                                class="badge {{ $approval->approved ? 'bg-green text-white' : 'bg-red text-white' }} ms-2">
                                                {{ $approval->approved ? 'Зөвшөөрсөн' : 'Татгалзсан' }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark ms-2">Санал өгөөгүй</span>
                                        @endif
                                    </div>
                                    @if (is_null($approval->approved) && auth()->id() === $approval->user_id)
                                        <form action="{{ route('order-journal-approvals.approve', $approval->id) }}"
                                            method="POST" class="d-flex gap-2 mt-2 mt-md-0 flex-wrap">
                                            @csrf
                                            <select name="approved" class="form-select form-select-sm" required>
                                                <option value="">Сонгоно уу</option>
                                                <option value="1">Зөвшөөрөх</option>
                                                <option value="0">Татгалзах</option>
                                            </select>
                                            <input type="text" name="comment" class="form-control flex-grow-1"
                                                placeholder="Тайлбар" />
                                            <button type="submit" class="btn btn-primary btn-sm">Илгээх</button>
                                        </form>
                                    @endif
                                </div>
                                @if ($approval->comment)
                                    <div class="mt-1">
                                        <em>Тайлбар:</em> {{ $approval->comment }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p>Санал өгөх хэрэглэгчид тогтоогдоогүй байна.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('order-journals.index') }}" class="btn btn-secondary mt-3">Буцах</a>
    </div>
@endsection
