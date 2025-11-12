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
                            <span class="avatar avatar-xl me-3"
                                style="background-image: url({{ asset('images/man.png') }});"></span>
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
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Хэлтэс:</span>
                                <span>{{ optional(Auth::user()->division)->Div_name ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Permission Level:</span>
                                <span>{{ optional(Auth::user()->permissionLevel)->name ?? '-' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Permission Code:</span>
                                <span>{{ optional(Auth::user()->permissionCode)->name ?? Auth::user()?->permission_code }}</span>
                            </li>
                        </ul>

                        <div class="mt-4 d-flex justify-content-end">
                            <a href="#" class="btn btn-primary">
                                Прoфайл засах
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
