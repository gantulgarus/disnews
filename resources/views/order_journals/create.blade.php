@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ti ti-file-plus me-2"></i>
                            Шинэ захиалга үүсгэх
                        </h3>
                    </div>

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


                    <form action="{{ route('order-journals.store') }}" method="POST">
                        @csrf

                        <div class="card-body">


                            <div class="row">
                                {{-- Байгууллага --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Байгууллага</label>
                                    <select class="form-select" name="organization_id" required>
                                        @foreach ($organizations as $org)
                                            <option value="{{ $org->id }}"
                                                {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                                {{ $org->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                {{-- Захиалгын төрөл --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Захиалгын төрөл</label>
                                    <select name="order_type" class="form-select" required>
                                        <option value="Энгийн" {{ old('order_type') == 'Энгийн' ? 'selected' : '' }}>Энгийн
                                        </option>
                                        <option value="Аваарын" {{ old('order_type') == 'Аваарын' ? 'selected' : '' }}>
                                            Аваарын</option>
                                    </select>

                                </div>
                            </div>

                            {{-- Засварын ажлын агуулга --}}
                            <div class="mb-3">
                                <label class="form-label required">Засварын ажлын агуулга</label>
                                <textarea name="content" class="form-control" rows="3" required>{{ old('content') }}</textarea>
                            </div>

                            {{-- Огноонууд --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Эхлэх хугацаа</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                        <input type="text" name="planned_start_date" class="form-control datetime"
                                            placeholder="Огноо сонгох" value="{{ old('planned_start_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Дуусах хугацаа</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                        <input type="text" name="planned_end_date" class="form-control datetime"
                                            placeholder="Огноо сонгох" value="{{ old('planned_end_date') }}">
                                    </div>
                                </div>
                            </div>


                            {{-- Хэрэглэгч таслах эсэх --}}
                            <div class="mb-3">
                                <label class="form-label">Хэрэглэгч таслах эсэх</label>
                                <select name="is_cut" id="is_cut" class="form-select">
                                    <option value="0" {{ old('is_cut', '0') == '0' ? 'selected' : '' }}>Үгүй</option>
                                    <option value="1" {{ old('is_cut') == '1' ? 'selected' : '' }}>Тийм</option>
                                </select>

                            </div>

                            {{-- Таслалтын тайлбар (default: hidden) --}}
                            <div class="mb-3 {{ old('is_cut') == '1' ? '' : 'd-none' }}" id="cut-description-wrapper">
                                <label class="form-label required">Таслалтын тайлбар</label>
                                <textarea name="cut_description" class="form-control" rows="3" placeholder="Хэдэн МВт, ямар хэрэглэгч таслах">{{ old('cut_description') }}</textarea>
                            </div>


                            <hr class="my-4">

                            {{-- Баталгаажуулалт --}}
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Баталсан хүний нэр</label>
                                    <input type="text" name="approver_name" class="form-control"
                                        value="{{ old('approver_name') }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Баталсан хүний албан тушаал</label>
                                    <input type="text" name="approver_position" class="form-control"
                                        value="{{ old('approver_position') }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Бүртгэсэн диспетчер</label>
                                    <input type="text" name="tze_dis_name" class="form-control"
                                        value="{{ old('tze_dis_name') }}">
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ route('order-journals.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left"></i> Буцах
                            </a>
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="ti ti-device-floppy"></i> Хадгалах
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
