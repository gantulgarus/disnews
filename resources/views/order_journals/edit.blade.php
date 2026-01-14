@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-file-edit me-2"></i>
                            Захиалгын журнал засварлах
                        </h3>
                    </div>

                    {{-- Validation алдаа --}}
                    @if ($errors->any())
                        <div class="alert alert-danger m-4">
                            <h4 class="alert-title">
                                <i class="ti ti-alert-circle me-1"></i>
                                Алдаа гарлаа
                            </h4>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('order-journals.update', $orderJournal->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            <div class="row">
                                {{-- Байгууллага --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Байгууллага</label>
                                    <select class="form-select @error('organization_id') is-invalid @enderror"
                                        name="organization_id" required>
                                        @foreach ($organizations as $org)
                                            <option value="{{ $org->id }}"
                                                {{ old('organization_id', $orderJournal->organization_id) == $org->id ? 'selected' : '' }}>
                                                {{ $org->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('organization_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Захиалгын төрөл --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Захиалгын төрөл</label>
                                    <select name="order_type" class="form-select @error('order_type') is-invalid @enderror"
                                        required>
                                        <option value="Энгийн"
                                            {{ old('order_type', $orderJournal->order_type) == 'Энгийн' ? 'selected' : '' }}>
                                            Энгийн</option>
                                        <option value="Аваарын"
                                            {{ old('order_type', $orderJournal->order_type) == 'Аваарын' ? 'selected' : '' }}>
                                            Аваарын</option>
                                    </select>
                                    @error('order_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Засварын агуулга --}}
                            <div class="mb-3">
                                <label class="form-label required">Засварын ажлын агуулга</label>
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="3" required>{{ old('content', $orderJournal->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Огноонууд --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Эхлэх хугацаа</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                        <input type="text" name="planned_start_date"
                                            class="form-control datetime @error('planned_start_date') is-invalid @enderror"
                                            placeholder="Огноо сонгох"
                                            value="{{ old('planned_start_date', optional($orderJournal->planned_start_date)->format('Y-m-d H:i')) }}">
                                        @error('planned_start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Дуусах хугацаа</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                        <input type="text" name="planned_end_date"
                                            class="form-control datetime @error('planned_end_date') is-invalid @enderror"
                                            placeholder="Огноо сонгох"
                                            value="{{ old('planned_end_date', optional($orderJournal->planned_end_date)->format('Y-m-d H:i')) }}">
                                        @error('planned_end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Хэрэглэгч таслах эсэх --}}
                            <div class="mb-3">
                                <label class="form-label">Хэрэглэгч таслах эсэх</label>
                                <select name="is_cut" id="is_cut"
                                    class="form-select @error('is_cut') is-invalid @enderror">
                                    <option value="0"
                                        {{ old('is_cut', $orderJournal->is_cut) == 0 ? 'selected' : '' }}>Үгүй</option>
                                    <option value="1"
                                        {{ old('is_cut', $orderJournal->is_cut) == 1 ? 'selected' : '' }}>Тийм</option>
                                </select>
                                @error('is_cut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Таслалтын тайлбар --}}
                            <div class="mb-3 {{ old('is_cut', $orderJournal->is_cut) == 1 ? '' : 'd-none' }}"
                                id="cut-description-wrapper">
                                <label class="form-label required">Таслалтын тайлбар</label>
                                <textarea name="cut_description" class="form-control @error('cut_description') is-invalid @enderror" rows="3"
                                    placeholder="Хэрэглэгч таслах шалтгаан, дэлгэрэнгүй тайлбар оруулна уу">{{ old('cut_description', $orderJournal->cut_description) }}</textarea>
                                @error('cut_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            {{-- Баталгаажуулалт --}}
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Баталсан хүний нэр</label>
                                    <input type="text" name="approver_name" class="form-control"
                                        value="{{ old('approver_name', $orderJournal->approver_name) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Баталсан хүний албан тушаал</label>
                                    <input type="text" name="approver_position" class="form-control"
                                        value="{{ old('approver_position', $orderJournal->approver_position) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Бүртгэсэн диспетчер</label>
                                    <input type="text" name="tze_dis_name" class="form-control"
                                        value="{{ old('tze_dis_name', $orderJournal->tze_dis_name) }}">
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ route('order-journals.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left"></i> Буцах
                            </a>
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="ti ti-device-floppy"></i> Засварлах
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isCutSelect = document.getElementById('is_cut');
            const descriptionWrapper = document.getElementById('cut-description-wrapper');

            function toggleCutDescription() {
                if (isCutSelect.value === '1') {
                    descriptionWrapper.classList.remove('d-none');
                } else {
                    descriptionWrapper.classList.add('d-none');
                }
            }

            // initial state
            toggleCutDescription();

            // onchange
            isCutSelect.addEventListener('change', toggleCutDescription);

            // datetime picker
            initFlatpickr(".datetime");
        });
    </script>
@endsection
