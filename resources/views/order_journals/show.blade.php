@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        {{-- Гарчиг --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Захиалгын дэлгэрэнгүй</h2>
            <a href="{{ route('order-journals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Буцах
            </a>
        </div>

        <div class="row g-4">
            {{-- Зүүн багана: Захиалгын мэдээлэл --}}
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Захиалгын мэдээлэл</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Захиалгын дугаар --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Дугаар</label>
                                <p class="fw-bold mb-0">{{ $orderJournal->order_number }}</p>
                            </div>

                            {{-- Төлөв --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Төлөв</label>
                                <div>
                                    @php
                                        $statusClass = match ($orderJournal->status) {
                                            0 => 'bg-secondary',
                                            1 => 'bg-info',
                                            2 => 'bg-warning',
                                            3 => 'bg-success',
                                            4 => 'bg-danger',
                                            default => 'bg-primary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} fs-6 text-white">
                                        {{ \App\Models\OrderJournal::$STATUS_NAMES[$orderJournal->status] ?? 'Тодорхойгүй' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Байгууллага --}}
                            <div class="col-12">
                                <label class="text-muted small mb-1">Байгууллага</label>
                                <p class="mb-0">{{ $orderJournal->organization->name }}</p>
                            </div>

                            {{-- Захиалгын төрөл --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Захиалгын төрөл</label>
                                <p class="mb-0">{{ $orderJournal->order_type }}</p>
                            </div>

                            {{-- Засварын агуулга --}}
                            <div class="col-12">
                                <label class="text-muted small mb-1">Засварын ажлын агуулга</label>
                                <p class="mb-0">{{ $orderJournal->content }}</p>
                            </div>

                            {{-- Хугацаа --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1"><i class="bi bi-calendar-check me-1"></i>Эхлэх
                                    хугацаа</label>
                                <p class="mb-0">{{ $orderJournal->planned_start_date }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-1"><i class="bi bi-calendar-x me-1"></i>Дуусах
                                    хугацаа</label>
                                <p class="mb-0">{{ $orderJournal->planned_end_date }}</p>
                            </div>

                            {{-- Баталсан хүн --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Баталсан</label>
                                <p class="mb-0">{{ $orderJournal->approver_name }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Албан тушаал</label>
                                <p class="mb-0">{{ $orderJournal->approver_position }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Баруун багана: Санал өгөх --}}
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Санал</h5>
                    </div>
                    <div class="card-body">
                        @forelse($orderJournal->approvals as $approval)
                            <div
                                class="border rounded p-3 mb-3 {{ is_null($approval->approved) ? 'bg-light' : ($approval->approved ? 'border-success' : 'border-danger') }}">
                                {{-- Хэрэглэгчийн нэр болон төлөв --}}
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="bi bi-person-circle me-1"></i>{{ $approval->user->name }}
                                        </h6>
                                        <small class="text-muted">
                                            @if (!is_null($approval->approved))
                                                @if ($approval->approved)
                                                    <span class="badge bg-success text-white">
                                                        <i class="bi bi-check-lg me-1"></i>Зөвшөөрсөн
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger text-white">
                                                        <i class="bi bi-x-lg me-1"></i>Татгалзсан
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary text-white">
                                                    <i class="bi bi-clock me-1"></i>Санал өгөөгүй
                                                </span>
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                {{-- Санал өгөх форм --}}
                                @if (is_null($approval->approved) && auth()->id() === $approval->user_id)
                                    <form action="{{ route('order-journals.approve', $orderJournal->id) }}" method="POST"
                                        class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Санал</label>
                                            <select name="approved" class="form-select" required>
                                                <option value="" selected disabled>Сонгоно уу</option>
                                                <option value="1">Зөвшөөрөх</option>
                                                <option value="0">Татгалзах</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Тайлбар (заавал биш)</label>
                                            <textarea name="comment" class="form-control" rows="3" placeholder="Тайлбараа энд бичнэ үү..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-send me-2"></i>Илгээх
                                        </button>
                                    </form>
                                @endif

                                {{-- Тайлбар --}}
                                @if ($approval->comment)
                                    <div class="mt-3 p-2 bg-white rounded border">
                                        <small class="text-muted d-block mb-1"><i
                                                class="bi bi-chat-left-text me-1"></i>Тайлбар:</small>
                                        <p class="mb-0 small">{{ $approval->comment }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>Санал өгөх хэрэглэгчид байхгүй байна.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border: none;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            border-bottom: 3px solid rgba(0, 0, 0, 0.1);
        }

        .badge {
            padding: 0.5rem 0.75rem;
        }

        label.text-muted {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
    </style>
@endsection
