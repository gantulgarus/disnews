@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Хэрэглэгчийн Профайл</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <span class="avatar avatar-xl me-3" id="profile-avatar"
                                style="background-image: url('{{ Auth::user()->avatar ? 'https://api.dicebear.com/9.x/adventurer/svg?seed=' . Auth::user()->avatar : asset('images/user.png') }}'); cursor:pointer;">
                            </span>
                            <div>
                                <h5 class="mb-0">{{ Auth::user()?->name }}</h5>
                                <small class="text-muted">{{ Auth::user()?->email }}</small>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Утас:</span>
                                <span>{{ Auth::user()?->phone ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Байгууллага:</span>
                                <span>{{ optional(Auth::user()->organization)->name ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Avatar Modal -->
                <div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('profile.updateAvatar') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Аватар сонгох</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        @php
                                            $avatars = [
                                                'Sadie',
                                                'Oliver',
                                                'Ava',
                                                'Ethan',
                                                'Mia',
                                                'Liam',
                                                'Sophia',
                                                'Noah',
                                                'Isabella',
                                                'Lucas',
                                            ];
                                        @endphp

                                        @foreach ($avatars as $seed)
                                            <div class="col-2 text-center mb-3">
                                                <img src="https://api.dicebear.com/9.x/adventurer/svg?seed={{ $seed }}"
                                                    class="avatar-option rounded-circle" data-seed="{{ $seed }}"
                                                    style="width:80px; height:80px; cursor:pointer;">
                                                <p class="small mt-1">{{ $seed }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="avatar" id="avatar-input"
                                        value="{{ Auth::user()->avatar }}">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Хадгалах</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Болих</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Profile avatar click -> modal
            document.getElementById('profile-avatar').addEventListener('click', function() {
                var avatarModal = new bootstrap.Modal(document.getElementById('avatarModal'));
                avatarModal.show();
            });

            // Avatar сонгох
            document.querySelectorAll('.avatar-option').forEach(function(img) {
                img.addEventListener('click', function() {
                    // Бусад selection border арилгах
                    document.querySelectorAll('.avatar-option').forEach(i => i.classList.remove(
                        'border-primary'));
                    img.classList.add('border-primary');
                    // Hidden input-д хадгалах
                    document.getElementById('avatar-input').value = img.getAttribute('data-seed');
                });
            });
        });
    </script>
@endsection
