@extends('layouts.admin')

@section('content')
    @php
        $user = auth()->user();
        $permission = $user->permissionLevel?->code;
        $userOrgCode = auth()->user()->organization->org_code ?? null;

        $isDispLead = $permission === 'DISP_LEAD';
        $isGenDisp = $permission === 'GEN_DISP';
        $isDisp = $permission === 'DISP';

        // Батлах товчнуудыг зөвшөөрөхөд шалгах
        $showApproveButtons =
            ($isDisp || $isDispLead || $isGenDisp) &&
            !in_array($orderJournal->status, [
                \App\Models\OrderJournal::STATUS_OPEN, // Нээлттэй
                \App\Models\OrderJournal::STATUS_CLOSED, // Хаалттай
            ]);

        // Диспетчерийн албаны дарга зөвшөөрсөн товч дарсан эсэх
        $dispLeadDecided = $orderJournal->statusHistories
            ->where('user_id', auth()->id())
            ->whereIn('new_status', [
                \App\Models\OrderJournal::STATUS_ACCEPTED,
                \App\Models\OrderJournal::STATUS_CANCELLED,
            ])
            ->isNotEmpty();

        // Ерөнхий диспетчер баталсан товч дарсан эсэх
        $genDispDecided = $orderJournal->statusHistories
            ->where('user_id', auth()->id())
            ->whereIn('new_status', [
                \App\Models\OrderJournal::STATUS_APPROVED, // Баталсан
                \App\Models\OrderJournal::STATUS_CANCELLED, // Цуцалсан
            ])
            ->isNotEmpty();

        // Нээх товч харуулах эсэх
        $canOpen =
            $orderJournal->status === \App\Models\OrderJournal::STATUS_APPROVED && $userOrgCode === 102 && $isDisp;

        // Хаах товч харуулах эсэх
        $canClose = $orderJournal->status === \App\Models\OrderJournal::STATUS_OPEN && $userOrgCode === 102 && $isDisp;

        // Үйлдлийн текстийг төлөвөөс хамаарч тодорхойлох
        function getActionText($history)
        {
            return match ($history->new_status) {
                \App\Models\OrderJournal::STATUS_FORWARDED => 'Захиалга бусад албанд илгээв',
                \App\Models\OrderJournal::STATUS_ACCEPTED => 'Захиалга зөвшөөрөв',
                \App\Models\OrderJournal::STATUS_APPROVED => 'Захиалга батлав',
                \App\Models\OrderJournal::STATUS_OPEN => 'Захиалга нээв',
                \App\Models\OrderJournal::STATUS_CLOSED => 'Захиалга хаав',
                default => 'Захиалгын төлөв өөрчилөв',
            };
        }
    @endphp

    <div class="container-fluid py-4">

        {{-- Гарчиг --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Захиалгын дэлгэрэнгүй</h2>
            <a href="{{ route('order-journals.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-2"></i>Буцах
            </a>
        </div>



        <div class="row g-4">
            {{-- Зүүн багана: Захиалгын мэдээлэл --}}
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Захиалгын дэлгэрэнгүй мэдээлэл</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Захиалгын дугаар --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Захиалгын дугаар</label>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white fs-5 px-3 py-2">
                                        {{ $orderJournal->order_number }}
                                    </span>
                                </div>
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
                                <p class="mb-0 fw-bold">{{ $orderJournal->organization->name }}</p>
                            </div>

                            {{-- Захиалгын төрөл --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Захиалгын төрөл</label>
                                @php
                                    $typeClass = $orderJournal->order_type === 'Аваар' ? 'bg-danger' : 'bg-info';
                                @endphp

                                <div>

                                    <span class="badge {{ $typeClass }} fs-6 text-white">
                                        <i class="ti ti-alert-triangle me-1"></i>
                                        {{ $orderJournal->order_type }}
                                    </span>
                                </div>
                            </div>
                            {{-- Засварын агуулга --}}
                            <div class="col-12">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-tools me-1"></i> Засварын ажлын агуулга
                                </label>

                                <div class="p-2 bg-light border rounded">
                                    <p class="mb-0">
                                        {{ $orderJournal->content }}
                                    </p>
                                </div>
                            </div>


                            {{-- Хугацаа --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-calendar-check me-1"></i>
                                    Эхлэх хугацаа
                                </label>
                                <p class="mb-0 fw-semibold">{{ $orderJournal->planned_start_date->format('Y-m-d H:i') }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-calendar-x me-1"></i>
                                    Дуусах хугацаа
                                </label>
                                <p class="mb-0 fw-semibold">{{ $orderJournal->planned_end_date->format('Y-m-d H:i') }}</p>
                            </div>



                            {{-- Баталсан хүн --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Баталсан</label>
                                <p class="mb-0 fw-semibold">{{ $orderJournal->approver_name }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Албан тушаал</label>
                                <p class="mb-0 fw-semibold">{{ $orderJournal->approver_position }}</p>
                            </div>

                            {{-- Дамжуулсан ДҮТ-н диспетчер --}}

                            <div class="col-md-12">
                                <label class="text-muted small mb-1">Дамжуулсан ДҮТ-н диспетчер</label>
                                <p class="mb-0 fw-semibold">
                                    {{ $orderJournal->dutDispatcher?->name ?? 'Тодорхойгүй' }}
                                </p>
                            </div>

                            {{-- Бодит хугацаа --}}
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-play me-1"></i> Бодит эхэлсэн цаг
                                </label>
                                <p class="mb-0 fw-semibold">
                                    {{ $orderJournal->real_start_date?->format('Y-m-d H:i') ?? 'Тодорхойгүй' }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-square-rounded-check me-1"></i> Бодит дууссан цаг
                                </label>
                                <p class="mb-0 fw-semibold">
                                    {{ $orderJournal->real_end_date?->format('Y-m-d H:i') ?? 'Тодорхойгүй' }}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                @if ($userOrgCode === 102)
                    {{-- Захиалгын түүх --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Захиалгын түүх</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                {{-- Захиалга үүссэн --}}
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Захиалга үүсгэсэн</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i>{{ $orderJournal->createdUser->name }}
                                                </small>
                                            </div>
                                            <small class="text-muted">
                                                <i
                                                    class="bi bi-calendar3 me-1"></i>{{ $orderJournal->created_at->format('Y-m-d H:i') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-secondary text-white">Шинэ</span>
                                    </div>
                                </div>

                                {{-- Төлөв солигдсон түүх --}}
                                @php
                                    $statusHistory = $orderJournal
                                        ->statusHistories()
                                        ->orderBy('created_at', 'asc')
                                        ->get();
                                @endphp

                                @foreach ($statusHistory as $history)
                                    <div class="timeline-item">
                                        @php
                                            $markerClass = match ($history->new_status) {
                                                1 => 'bg-info',
                                                2 => 'bg-warning',
                                                3 => 'bg-success',
                                                4 => 'bg-danger',
                                                6 => 'bg-primary',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <div class="timeline-marker {{ $markerClass }}"></div>
                                        <div class="timeline-content">
                                            {{-- Хүний мэдээлэл --}}
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">
                                                        {{ $history->user->name }}
                                                        ({{ $history->user->division?->Div_name ?? '' }})
                                                    </h6>
                                                </div>
                                                <small class="text-muted">
                                                    <i
                                                        class="bi bi-calendar3 me-1"></i>{{ $history->created_at->format('Y-m-d H:i') }}
                                                </small>
                                            </div>

                                            {{-- Статус солигдсон --}}
                                            <div class="mb-2 d-flex align-items-center">

                                                <span class="text-white badge {{ $markerClass }}">
                                                    {{ \App\Models\OrderJournal::$STATUS_NAMES[$history->new_status] ?? '-' }}
                                                </span>
                                            </div>

                                            {{-- Тайлбар --}}
                                            @if ($history->comment)
                                                <div class="mt-2 p-2 bg-light rounded border">
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="bi bi-chat-left-text me-1"></i>Тайлбар:
                                                    </small>
                                                    <p class="mb-0 small">{{ $history->comment }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach


                                {{-- Одоогийн төлөв --}}
                                <div class="timeline-item">
                                    @php
                                        $currentMarkerClass = match ($orderJournal->status) {
                                            0 => 'bg-secondary',
                                            1 => 'bg-info',
                                            2 => 'bg-warning',
                                            3 => 'bg-success',
                                            4 => 'bg-danger',
                                            default => 'bg-primary',
                                        };
                                    @endphp
                                    <div class="timeline-marker {{ $currentMarkerClass }} pulse"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Одоогийн төлөв</h6>
                                        <span class="badge {{ $currentMarkerClass }} fs-6 text-white">
                                            {{ \App\Models\OrderJournal::$STATUS_NAMES[$orderJournal->status] ?? 'Тодорхойгүй' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            {{-- Баруун багана: Санал өгөх --}}
            @if ($userOrgCode === 102)
                <div class="col-lg-5">

                    {{-- Батлах, зөвшөөрөх товч --}}
                    @if ($showApproveButtons)
                        <div class="d-grid">
                            @if ($isDisp && $orderJournal->order_type === 'Аваарын')
                                <button class="btn btn-danger btn-lg w-100 btn-w-100-lg" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                    <i class="ti ti-check fs-1 me-2 text-white"></i>
                                    Аваарын захиалга батлах
                                </button>
                            @endif

                            @if ($isDispLead && !$dispLeadDecided)
                                <button class="btn btn-danger btn-lg w-100 btn-w-100-lg" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                    <i class="ti ti-check fs-1 me-2 text-white"></i>
                                    Зөвшөөрөх / Татгалзах
                                </button>
                            @endif

                            @if ($isGenDisp && !$genDispDecided)
                                <button class="btn btn-danger btn-lg w-100 btn-w-100-lg" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                    <i class="ti ti-check fs-1 me-2 text-white"></i>Батлах
                                </button>
                            @endif
                        </div>
                    @endif

                    <div class="mb-3 d-grid">
                        {{-- Нээх товч --}}
                        @if ($canOpen)
                            <button class="btn btn-primary btn-lg w-100" data-bs-toggle="modal"
                                data-bs-target="#openOrderModal">
                                <i class="ti ti-play me-2"></i>Захиалга нээх
                            </button>
                        @endif

                        {{-- Хаах товч --}}
                        @if ($canClose)
                            <button class="btn btn-success btn-lg w-100" data-bs-toggle="modal"
                                data-bs-target="#closeOrderModal">
                                <i class="ti ti-square-rounded-check me-2"></i>Захиалга хаах
                            </button>
                        @endif
                    </div>

                    {{-- Санал өгөх хэсэг --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Санал</h5>
                        </div>
                        <div class="card-body">
                            @forelse($orderJournal->approvals as $approval)
                                <div
                                    class="border rounded p-3 mb-3 {{ is_null($approval->approved) ? 'bg-light' : ($approval->approved ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle') }}">
                                    {{-- Хэрэглэгчийн нэр болон төлөв --}}
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="bi bi-person-circle me-1"></i>{{ $approval->user->name }}
                                                ({{ $approval->user->division?->Div_name }})
                                                {{ $approval->updated_at->format('Y-m-d H:i') }}
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

                                    {{-- Санал өгөх форм - зөвхөн санал өгөөгүй, өөрийн approval бол --}}
                                    @if (is_null($approval->approved) && auth()->id() === $approval->user_id)
                                        <form action="{{ route('order-journals.approveOpinion', $approval->id) }}"
                                            method="POST" enctype="multipart/form-data" class="mt-3">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Санал</label>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="approved"
                                                        id="approve_{{ $approval->id }}" value="1" required>
                                                    <label class="btn btn-outline-success"
                                                        for="approve_{{ $approval->id }}">
                                                        <i class="bi bi-check-lg me-1"></i>Зөвшөөрөх
                                                    </label>

                                                    <input type="radio" class="btn-check" name="approved"
                                                        id="reject_{{ $approval->id }}" value="0" required>
                                                    <label class="btn btn-outline-danger"
                                                        for="reject_{{ $approval->id }}">
                                                        <i class="bi bi-x-lg me-1"></i>Татгалзах
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Тайлбар</label>
                                                <textarea name="comment" class="form-control" rows="3" placeholder="Тайлбараа энд бичнэ үү..."></textarea>
                                            </div>
                                            {{-- 📎 Файл хавсаргах --}}
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    <i class="bi bi-paperclip me-1"></i>Файл хавсаргах
                                                </label>
                                                <input type="file" name="attachment" class="form-control">
                                                <small class="text-muted">PDF, Word, Excel, Image (≤10MB)</small>
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-send me-2"></i>Санал илгээх
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Тайлбар --}}
                                    @if ($approval->comment)
                                        <div class="mt-3 p-2 bg-white rounded border">
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-chat-left-text me-1"></i>Тайлбар:
                                            </small>
                                            <p class="mb-0 small">{{ $approval->comment }}</p>
                                        </div>
                                    @endif
                                    {{-- Хавсралт файл --}}
                                    @if ($approval->attachment)
                                        <div class="mt-3">
                                            <h6 class="mb-2">Хавсралт файл</h6>

                                            @php
                                                $fileName = basename($approval->attachment);
                                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                $displayName =
                                                    strlen($fileName) > 25
                                                        ? substr($fileName, 0, 22) . '...'
                                                        : $fileName;

                                                // Font Awesome icon-г өргөтгөлийн дагуу сонгоно
                                                $faIcon = match ($ext) {
                                                    'pdf' => 'fa-file-pdf text-danger',
                                                    'doc', 'docx' => 'fa-file-word text-primary',
                                                    'xls', 'xlsx' => 'fa-file-excel text-success',
                                                    'png', 'jpg', 'jpeg', 'gif' => 'fa-file-image text-warning',
                                                    'zip', 'rar' => 'fa-file-archive text-muted',
                                                    'txt' => 'fa-file-alt text-secondary',
                                                    default => 'fa-file text-secondary',
                                                };
                                            @endphp

                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas {{ $faIcon }} fa-lg"></i>
                                                <a href="{{ asset('storage/' . $approval->attachment) }}" target="_blank"
                                                    class="text-truncate d-inline-block" style="max-width: 200px;">
                                                    {{ $displayName }}
                                                </a>
                                            </div>
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
            @endif
        </div>

        {{-- Ерөнхий диспетчер батлах --}}
        <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('order-journals.approve', $orderJournal->id) }}" method="POST">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">Захиалга батлах</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Тайлбар</label>
                                <textarea name="comment" class="form-control" rows="3" placeholder="Тайлбар (заавал биш)"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Үйлдэл</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="action" value="approve"
                                            class="form-selectgroup-input" checked>
                                        <span class="form-selectgroup-label">
                                            <i class="ti ti-check text-success me-1"></i>
                                            {{ $isGenDisp ? 'Батлах' : 'Зөвшөөрөх' }}
                                        </span>
                                    </label>

                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="action" value="reject"
                                            class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">
                                            <i class="ti ti-x text-danger me-1"></i>
                                            {{ $isGenDisp ? 'Цуцлах' : 'Татгалзах' }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Буцах</button>
                            <button type="submit" class="btn btn-primary">
                                Илгээх
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Диспетчер захиалга нээх --}}
        <div class="modal fade" id="openOrderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('order-journals.open', $orderJournal->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Захиалгыг нээх</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Бодит эхэлсэн цаг</label>
                                <input type="datetime-local" name="real_start_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тайлбар (заавал биш)</label>
                                <textarea name="comment" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Буцах</button>
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Та энэ захиалгыг нээхэд итгэлтэй байна уу?')">Нээх</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Диспетчер захиалга хаах --}}
        <div class="modal fade" id="closeOrderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('order-journals.close', $orderJournal->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Захиалгыг хаах</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Бодит дууссан цаг</label>
                                <input type="datetime-local" name="real_end_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тайлбар (заавал биш)</label>
                                <textarea name="comment" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Буцах</button>
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Та энэ захиалгыг хаахад итгэлтэй байна уу?')">Хаах</button>
                        </div>
                    </form>
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

        /* Timeline стайл */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #0d6efd, #20c997);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -1.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 2px currentColor;
        }

        .timeline-marker.pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
            }
        }

        .timeline-content {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e9ecef;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .timeline-content h6 {
            color: #495057;
            font-weight: 600;
        }

        .bg-success-subtle {
            background-color: #d1e7dd !important;
        }

        .bg-danger-subtle {
            background-color: #f8d7da !important;
        }

        .btn-w-100-lg {
            height: 60px;
            /* Өндрийг нэмэгдүүлж болно */
            font-size: 1.25rem;
            /* Текстийн хэмжээг хадгалах */
        }
    </style>
@endsection
