@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Захиалгын Журнал</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <a href="{{ route('order-journals.create') }}" class="btn btn-primary mb-3">Шинэ захиалга үүсгэх</a>

        <!-- Хайлтын форм -->
        <form method="GET" action="{{ route('order-journals.index') }}" class="row g-2 mb-3">
            <div class="col-md-2">
                <input type="text" name="order_number" class="form-control" placeholder="Захиалгын дугаар"
                    value="{{ request('order_number') }}">
            </div>
            <div class="col-md-2">
                <select name="organization_id" class="form-select">
                    <option value="">Байгууллага сонгох</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Төлөв сонгох</option>
                    @php
                        $allowedStatuses = [
                            \App\Models\OrderJournal::STATUS_NEW,
                            \App\Models\OrderJournal::STATUS_APPROVED,
                            \App\Models\OrderJournal::STATUS_CANCELLED,
                            \App\Models\OrderJournal::STATUS_OPEN,
                            \App\Models\OrderJournal::STATUS_CLOSED,
                            \App\Models\OrderJournal::STATUS_POSTPONED,
                            \App\Models\OrderJournal::STATUS_IN_REVIEW,
                        ];
                    @endphp

                    @foreach (\App\Models\OrderJournal::$STATUS_NAMES as $key => $name)
                        @if (in_array($key, $allowedStatuses))
                            <option value="{{ $key }}"
                                {{ request('status') !== null && (int) request('status') === $key ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Order Type -->
            <div class="col-md-2">
                <select name="order_type" class="form-select">
                    <option value="">Төрөл сонгох</option>
                    <option value="Энгийн" {{ request('order_type') === 'Энгийн' ? 'selected' : '' }}>Энгийн</option>
                    <option value="Аваарын" {{ request('order_type') === 'Аваарын' ? 'selected' : '' }}>Аваарын</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="planned_start_date" class="form-control"
                    value="{{ request('planned_start_date') }}">
            </div>



            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary me-2">Хайх</button>
                <a href="{{ route('order-journals.index') }}" class="btn btn-secondary">Цэвэрлэх</a>
            </div>
        </form>



        <div class="card">
            <div>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="text-wrap text-center">Захиалгын дугаар</th>
                            <th rowspan="2">Төлөв</th>
                            <th rowspan="2">Байгууллага</th>
                            <th rowspan="2">Төрөл</th>
                            <th rowspan="2" class="text-wrap">Засварын ажлын агуулга</th>
                            <th colspan="2" class="text-center">Захиалгат хугацаа</th>
                            <th rowspan="2">Баталсан</th>
                            <th colspan="2" class="text-center">Бодит хугацаа</th>
                            <th rowspan="2">Үйлдэл</th>
                        </tr>
                        <tr>
                            <th>Эхлэх</th>
                            <th>Дуусах</th>
                            <th>Эхэлсэн</th>
                            <th>Дууссан</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($journals as $journal)
                            <tr @class([
                                'table-info' => $journal->approvals->where('user_id', auth()->id())->whereNull('approved')->count(),
                                'table-warning text-muted' =>
                                    $journal->status === \App\Models\OrderJournal::STATUS_OPEN,
                                'table-secondary text-muted' =>
                                    $journal->status === \App\Models\OrderJournal::STATUS_CLOSED,
                            ])>
                                <td class="text-center">{{ $journal->order_number }}</td>
                                <td>
                                    @php
                                        $statusClass = match ($journal->status) {
                                            0 => 'badge bg-gray text-black',
                                            3 => 'badge bg-green text-white',
                                            4 => 'badge bg-red text-white',
                                            7 => 'badge bg-orange text-white',
                                            8 => 'badge bg-dark text-white',
                                            10 => 'badge bg-yellow text-white',
                                            default => 'badge bg-secondary text-white',
                                        };
                                    @endphp
                                    <span class="{{ $statusClass }}">
                                        {{ \App\Models\OrderJournal::$STATUS_NAMES[$journal->status] ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $journal->organization->name }}</td>

                                <td>
                                    @php
                                        $typeBadge = match ($journal->order_type) {
                                            'Энгийн' => ['bg-green-lt', 'text-green-lt-fg', 'Энгийн'],
                                            'Аваарын' => ['bg-red-lt', 'text-red-lt-fg', 'Аваарын'],
                                            default => ['bg-secondary', 'text-secondary-fg', $journal->order_type],
                                        };
                                    @endphp

                                    <span class="badge {{ $typeBadge[0] }} {{ $typeBadge[1] }}">
                                        {{ $typeBadge[2] }}
                                    </span>
                                </td>
                                <td>{{ $journal->content }}</td>
                                <td>{{ $journal->planned_start_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $journal->planned_end_date->format('Y-m-d H:i') }}</td>
                                <td>{{ $journal->approver_name }} <p class="small text-muted mb-0">
                                        {{ $journal->approver_position }}</p>
                                </td>
                                <td>{{ $journal->real_start_date ? $journal->real_start_date->format('Y-m-d H:i') : '' }}
                                </td>
                                <td>{{ $journal->real_end_date ? $journal->real_end_date->format('Y-m-d H:i') : '' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Харах товч - бүх хэрэглэгчид -->
                                        <a href="{{ route('order-journals.show', $journal->id) }}"
                                            class="btn btn-primary btn-icon" aria-label="Button" title="Дэлгэрэнгүй үзэх">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
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
                                            $isCreator = $journal->created_user_id === $user->id;
                                            $isDUT = $user->organization_id == 5; // ДҮТ байгууллага
                                            $isDutDispatcher = $user->permissionLevel?->code === 'DISP';

                                            // Диспетчерийн албаны дарга болон Ерөнхий диспетчер эсэх
                                            $isDispLead = $permission === 'DISP_LEAD';
                                            $isGenDisp = $permission === 'GEN_DISP';
                                        @endphp

                                        <!-- ДҮТ-н дис: Санал авахаар илгээх товч (зөвхөн Шинэ төлөвт, DISP_LEAD болон GEN_DISP биш бол) -->
                                        @if ($isDUT && !$isDispLead && !$isGenDisp && $journal->status === \App\Models\OrderJournal::STATUS_NEW)
                                            <button class="btn btn-cyan btn-icon" aria-label="Button" data-bs-toggle="modal"
                                                data-bs-target="#forwardModal{{ $journal->id }}"
                                                title="Санал авахаар илгээх">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 14l11 -11" />
                                                    <path
                                                        d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Засах болон устгах товч: зөвхөн үүсгэгч, зөвхөн Шинэ төлөвт -->
                                        {{-- @if (($isCreator && $journal->status === \App\Models\OrderJournal::STATUS_NEW) || $isDutDispatcher)
                                            <a href="{{ route('order-journals.edit', $journal->id) }}"
                                                class="btn btn-yellow btn-icon" aria-label="Button" title="Засах">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path
                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('order-journals.destroy', $journal->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-red btn-icon" aria-label="Button"
                                                    onclick="return confirm('Устгах уу?')" title="Устгах">
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
                                        @endif --}}
                                    </div>
                                </td>
                            </tr>

                            <!-- Forward Modal -->
                            <div class="modal fade" id="forwardModal{{ $journal->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('order-journals.forward', $journal->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Захиалгыг илгээх / санал авах</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label>Санал өгөх хэрэглэгчид сонгох</label>
                                                <select name="approvers[]" class="form-select mb-2 select2-approvers"
                                                    multiple required>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">
                                                            {{ $user->name }} — {{ $user->organization->name }} |
                                                            {{ $user->division?->Div_name }}
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
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $('.select2-approvers').each(function() {
                let modal = $(this).closest('.modal');

                $(this).select2({
                    placeholder: "Хэрэглэгч хайх...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: modal
                });
            });

            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('.select2-approvers').val(null).trigger('change');
            });


        });
    </script>
@endsection
