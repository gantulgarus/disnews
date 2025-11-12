<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">

        <!-- Зүүн тал: Системийн нэр -->
        <a class="navbar-brand" href="#">
            <span class="fw-bold fs-3">{{ config('app.name') }}</span>
        </a>

        <!-- Баруун тал: notification + user menu -->
        <div class="navbar-nav flex-row align-items-center">

            <!-- Notification -->
            <div class="nav-item dropdown me-3">
                <a href="#" class="nav-link px-0 position-relative" data-bs-toggle="dropdown"
                    aria-label="Show notifications">
                    <span class="nav-link-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                        </svg>
                        @if ($newMessagesCount > 0)
                            <span class="badge bg-red"></span>
                        @endif
                    </span>

                </a>

                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card"
                    style="min-width: 350px;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Телефон мэдээ</h3>
                        </div>
                        <div class="list-group list-group-flush list-group-hoverable">
                            @forelse($newMessages as $msg)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="me-2">
                                            <a href="{{ route('telephone_messages.show', $msg) }}"
                                                class="text-body d-block text-truncate">
                                                {{ \Illuminate\Support\Str::limit($msg->content, 50) }}
                                            </a>
                                            <small class="text-muted d-block text-truncate">
                                                {{ optional($msg->senderOrganization)->name ?? $msg->sender_org_id }}
                                            </small>
                                        </div>
                                        <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-secondary">
                                    Шинээр ирсэн мэдээ алга
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            <!-- User menu -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url({{ asset('images/man.png') }})"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ Auth::user()?->name }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item">Статус</a>
                    <a href="{{ route('users.profile') }}" class="dropdown-item">Профайл</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); this.closest('form').submit();">Гарах</a>
                    </form>
                </div>
            </div>

        </div>

    </div>
</header>
