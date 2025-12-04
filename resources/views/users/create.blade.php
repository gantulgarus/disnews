@extends('layouts.admin')

@section('content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="ti ti-user-plus"></i> Хэрэглэгч бүртгэх</h3>
        </div>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <strong><i class="ti ti-alert-circle"></i> Алдаа гарлаа!</strong>
                <ul class="mt-2 mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body py-4">

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    {{-- Байгууллага --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Байгууллага</label>
                        <select name="organization_id"
                            class="form-select form-select-lg @error('organization_id') is-invalid @enderror" required>
                            <option value="">-- Байгууллага сонгох --</option>
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}"
                                    {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organization_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Нэр --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Нэр</label>
                        <input type="text" name="name"
                            class="form-control form-control-lg shadow-sm @error('name') is-invalid @enderror"
                            value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Имэйл --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">И-Мэйл</label>
                        <input type="email" name="email"
                            class="form-control form-control-lg shadow-sm @error('email') is-invalid @enderror"
                            value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Хэрэглэгчийн код --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Хэрэглэгчийн код</label>
                        <input type="text" name="usercode"
                            class="form-control form-control-lg shadow-sm @error('usercode') is-invalid @enderror"
                            value="{{ old('usercode') }}">
                        @error('usercode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Гар утас --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Гар утас</label>
                        <input type="text" name="phone"
                            class="form-control form-control-lg shadow-sm @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Нууц үг --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Нууц үг</label>
                        <input type="password" name="password"
                            class="form-control form-control-lg shadow-sm @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Хэрэглэгчийн эрх --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Хэрэглэгчийн эрх</label>
                        <select name="permission_level_id"
                            class="form-select form-select-lg @error('permission_level_id') is-invalid @enderror">
                            <option value="">-- Сонгох --</option>
                            @foreach ($permissions as $perm)
                                <option value="{{ $perm->id }}"
                                    {{ old('permission_level_id') == $perm->id ? 'selected' : '' }}>
                                    {{ $perm->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('permission_level_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button class="btn btn-success btn-lg">
                        <i class="ti ti-device-floppy"></i> Хадгалах
                    </button>
                </form>

            </div>
        </div>
    </div>
@endsection
