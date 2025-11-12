@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <div class="card shadow-sm border-0 rounded-3">
                    <div
                        class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-3">
                        <h5 class="mb-0">📱 SMS илгээх</h5>
                        <a href="{{ route('sms.messages') }}" class="btn btn-light btn-sm fw-semibold">
                            Илгээсэн мессежүүд →
                        </a>
                    </div>

                    <div class="card-body p-4">
                        {{-- Success message --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ✅ {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Error message --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ❌ {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('sms.send') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold d-block mb-2">Бүлэг сонгох</label>

                                <div class="mb-2">
                                    <button type="button" id="selectAllBtn" class="btn btn-outline-secondary btn-sm">
                                        ✅ Бүх бүлэг сонгох
                                    </button>
                                </div>

                                @foreach ($groups as $group)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input group-checkbox" type="checkbox" name="group_ids[]"
                                            id="group_{{ $group->id }}" value="{{ $group->id }}">
                                        <label class="form-check-label" for="group_{{ $group->id }}">
                                            {{ $group->name }}
                                            <span class="text-muted">({{ $group->recipients_count }} хүн)</span>
                                        </label>
                                    </div>
                                @endforeach

                                @error('group_ids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">Мессежийн агуулга</label>
                                <textarea name="message" id="message" rows="4" class="form-control" placeholder="Мессежийн агуулгаа бичнэ үү..."
                                    required></textarea>
                                @error('message')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                📤 Илгээх
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- JS хэсэг --}}
    <script>
        document.getElementById('selectAllBtn').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.group-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? '✅ Бүх бүлэг сонгох' : '❌ Сонголт цуцлах';
        });
    </script>
@endsection
