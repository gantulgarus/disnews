@extends('layouts.admin')

@section('content')
    <div class="container py-4">

        <h3 class="fw-bold mb-4">Хэрэглэгч засах</h3>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong><i class="ti ti-alert-circle"></i> Алдаа гарлаа:</strong>
                <ul class="mt-2 mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    {{-- Байгууллага --}}
                    <div class="mb-3">
                        <label class="form-label">Байгууллага</label>
                        <select name="organization_id" class="form-select form-select-lg">
                            <option value="">-- Байгууллага сонгох --</option>
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}"
                                    {{ $user->organization_id == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Албан тушаал</label>
                        <select name="division_id" class="form-select form-select-lg">
                            <option value="">-- Сонгох --</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ $user->division_id == $division->id ? 'selected' : '' }}>
                                    {{ $division->Div_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Нэр --}}
                    <div class="mb-3">
                        <label class="form-label">Нэр</label>
                        <input type="text" name="name" value="{{ $user->name }}"
                            class="form-control form-control-lg shadow-sm">
                    </div>

                    {{-- И-Мэйл --}}
                    <div class="mb-3">
                        <label class="form-label">И-Мэйл</label>
                        <input type="email" name="email" value="{{ $user->email }}"
                            class="form-control form-control-lg shadow-sm">
                    </div>

                    {{-- Хэрэглэгчийн код --}}
                    <div class="mb-3">
                        <label class="form-label">Хэрэглэгчийн код</label>
                        <input type="text" name="usercode" value="{{ $user->usercode }}"
                            class="form-control form-control-lg shadow-sm">
                    </div>

                    {{-- Гар утас --}}
                    <div class="mb-3">
                        <label class="form-label">Гар утас</label>
                        <input type="text" name="phone" value="{{ $user->phone }}"
                            class="form-control form-control-lg shadow-sm">
                    </div>

                    {{-- Permission Level --}}
                    <div class="mb-3">
                        <label class="form-label">Хэрэглэгчийн эрх</label>
                        <select name="permission_level_id" class="form-select form-select-lg">
                            <option value="">-- Сонгох --</option>
                            @foreach ($permissions as $perm)
                                <option value="{{ $perm->id }}"
                                    {{ $user->permission_level_id == $perm->id ? 'selected' : '' }}>
                                    {{ $perm->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Button --}}
                    <button class="btn btn-primary btn-lg">
                        <i class="ti ti-device-floppy"></i> Хадгалах
                    </button>

                </form>
            </div>
        </div>
    </div>
@endsection
