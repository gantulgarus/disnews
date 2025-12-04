@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Захиалгын Журнал</h2>
        <a href="{{ route('order-journals.create') }}" class="btn btn-primary mb-3">Шинэ захиалга үүсгэх</a>

        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
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
                            <tr @if ($journal->approvals->where('user_id', auth()->id())->whereNull('approved')->count()) class="table-warning" @endif>
                                <td>{{ $journal->order_number }}</td>
                                <td>
                                    @php
                                        $statusClass = match ($journal->status) {
                                            0 => 'badge bg-gray text-black',
                                            1 => 'badge bg-gray text-white',
                                            2 => 'badge bg-yellow text-dark',
                                            3 => 'badge bg-green text-white',
                                            4 => 'badge bg-red text-white',
                                            default => 'badge bg-blue text-white',
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
                                <td class="text-end">

                                    <!-- Харах товч - бүх хэрэглэгчид -->
                                    <a href="{{ route('order-journals.show', $journal->id) }}" class="btn btn-sm btn-info"
                                        title="Дэлгэрэнгүй үзэх">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>

                                    @php
                                        $user = auth()->user();
                                        $permission = $user->permissionLevel?->code;
                                        $isCreator = $journal->user_id === $user->id;
                                        $isDUT = $user->organization_id == 5; // ДҮТ байгууллага

                                        // Диспетчерийн албаны дарга болон Ерөнхий диспетчер эсэх
                                        $isDispLead = $permission === 'DISP_LEAD';
                                        $isGenDisp = $permission === 'GEN_DISP';
                                    @endphp

                                    <!-- ДҮТ-н хэрэглэгчид: Илгээх товч (зөвхөн Шинэ төлөвт, DISP_LEAD болон GEN_DISP биш бол) -->
                                    @if ($isDUT && !$isDispLead && !$isGenDisp && $journal->status === \App\Models\OrderJournal::STATUS_NEW)
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#forwardModal{{ $journal->id }}" title="Санал авахаар илгээх">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 14l11 -11" />
                                                <path
                                                    d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                            </svg>
                                        </button>
                                    @endif

                                    <!-- Диспетчер: Аваарын захиалгыг батлах (цуцлагдсан, зөвшөөрсөн, батлагдсанаас бусад) -->
                                    @if (
                                        $permission === 'DISP' &&
                                            $journal->order_type === 'Аваарын' &&
                                            !in_array($journal->status, [
                                                \App\Models\OrderJournal::STATUS_CANCELLED,
                                                \App\Models\OrderJournal::STATUS_ACCEPTED,
                                                \App\Models\OrderJournal::STATUS_APPROVED,
                                            ]))
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $journal->id }}"
                                            title="Аваарын захиалгыг батлах">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    @endif

                                    <!-- Диспетчерийн албаны дарга: Цуцлагдсан, зөвшөөрсөн болон батлагдсанаас бусад үед зөвшөөрөх/татгалзах -->
                                    @if (
                                        $isDispLead &&
                                            !in_array($journal->status, [
                                                \App\Models\OrderJournal::STATUS_CANCELLED,
                                                \App\Models\OrderJournal::STATUS_ACCEPTED,
                                                \App\Models\OrderJournal::STATUS_APPROVED,
                                            ]))
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $journal->id }}" title="Зөвшөөрөх/Татгалзах">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    @endif

                                    <!-- Ерөнхий диспетчер: Цуцлагдсан болон батлагдсанаас бусад үед батлах -->
                                    @if (
                                        $isGenDisp &&
                                            !in_array($journal->status, [
                                                \App\Models\OrderJournal::STATUS_CANCELLED,
                                                \App\Models\OrderJournal::STATUS_APPROVED,
                                            ]))
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $journal->id }}" title="Ерөнхий батлах">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    @endif

                                    <!-- Засах болон устгах товч: зөвхөн үүсгэгч, зөвхөн Шинэ төлөвт -->
                                    @if ($isCreator && $journal->status === \App\Models\OrderJournal::STATUS_NEW)
                                        <a href="{{ route('order-journals.edit', $journal->id) }}"
                                            class="btn btn-sm btn-warning" title="Засах">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path
                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('order-journals.destroy', $journal->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Устгах уу?')"
                                                title="Устгах">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Forward Modal -->
                            <div class="modal fade" id="forwardModal{{ $journal->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('order-journals.forward', $journal->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Захиалгыг илгээх / санал авах</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label>Санал өгөх хэрэглэгчид сонгох</label>
                                                <select name="approvers[]" class="form-select mb-2" multiple required>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}
                                                            ({{ $user->organization->name }})
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <label>Тайлбар (санал өгөх үед)</label>
                                                <textarea name="comment" class="form-control" rows="3" placeholder="Тайлбар бичих боломжтой"></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Илгээх</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Буцах</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Approval Modal -->
                            <div class="modal fade" id="approveModal{{ $journal->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('order-journals.approve', $journal->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Захиалга батлах</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Тайлбар</label>
                                                    <textarea name="comment" class="form-control" rows="4" placeholder="Тайлбар оруулах (заавал биш)"></textarea>
                                                </div>

                                                @if ($permission === 'DISP_LEAD' || $permission === 'DISP' || $permission === 'GEN_DISP')
                                                    <div class="mb-3">
                                                        <label class="form-label">Үйлдэл</label>
                                                        <div class="form-selectgroup">
                                                            <label class="form-selectgroup-item">
                                                                <input type="radio" name="action" value="approve"
                                                                    class="form-selectgroup-input" checked>
                                                                <span class="form-selectgroup-label">
                                                                    <i class="ti ti-check text-success me-1"></i>
                                                                    @if ($permission === 'GEN_DISP')
                                                                        Батлах
                                                                    @else
                                                                        Зөвшөөрөх
                                                                    @endif
                                                                </span>
                                                            </label>
                                                            <label class="form-selectgroup-item">
                                                                <input type="radio" name="action" value="reject"
                                                                    class="form-selectgroup-input">
                                                                <span class="form-selectgroup-label">
                                                                    <i class="ti ti-x text-danger me-1"></i>
                                                                    @if ($permission === 'GEN_DISP')
                                                                        Цуцлах
                                                                    @else
                                                                        Татгалзах
                                                                    @endif
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Буцах</button>
                                                <button type="submit" class="btn btn-primary">Батлах</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
