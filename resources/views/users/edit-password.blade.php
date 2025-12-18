@extends('layouts.admin')

@section('content')
    <div class="container py-4">

        <h3 class="fw-bold mb-4">
            <i class="ti ti-lock"></i> Нууц үг солих - {{ $user->name }}
        </h3>

        {{-- Success message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ti ti-check"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <form method="POST" action="{{ route('users.update-password', $user) }}">
                            @csrf
                            @method('PUT')

                            {{-- Хэрэглэгчийн мэдээлэл --}}
                            <div class="mb-3">
                                <label class="form-label">Хэрэглэгчийн нэр</label>
                                <input type="text" value="{{ $user->name }}" class="form-control form-control-lg"
                                    disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Хэрэглэгчийн код</label>
                                <input type="text" value="{{ $user->usercode }}" class="form-control form-control-lg"
                                    disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">И-Мэйл</label>
                                <input type="text" value="{{ $user->email }}" class="form-control form-control-lg"
                                    disabled>
                            </div>

                            <hr class="my-4">

                            {{-- Шинэ нууц үг --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ti ti-key"></i> Шинэ нууц үг
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" required
                                    class="form-control form-control-lg shadow-sm @error('password') is-invalid @enderror"
                                    placeholder="Дор хаяж 8 тэмдэгт">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Нууц үг давтах --}}
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="ti ti-key"></i> Нууц үг давтах
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required
                                    class="form-control form-control-lg shadow-sm" placeholder="Нууц үгээ дахин оруулна уу">
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="ti ti-device-floppy"></i> Хадгалах
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="ti ti-arrow-left"></i> Буцах
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- Анхааруулга --}}
                <div class="alert alert-info mt-3">
                    <i class="ti ti-info-circle"></i>
                    <strong>Анхааруулга:</strong> Нууц үг солигдсоны дараа хэрэглэгч шинэ нууц үгээр нэвтрэх шаардлагатай.
                </div>
            </div>
        </div>

    </div>
@endsection
