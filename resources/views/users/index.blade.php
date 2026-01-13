@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Ажилтнуудын жагсаалт</h3>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Хэрэглэгч нэмэх
            </a>
        </div>

        {{-- Амжилтын мэдэгдэл --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="ti ti-check"></i></strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif




        <div class="card shadow-sm">
            <div class="card-header">
                <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3">

                    <div class="col-md-4">
                        <select name="organization_id" class="form-select">
                            <option value="">-- Бүх байгууллага --</option>
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}"
                                    {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="text" name="usercode" class="form-control" placeholder="Хэрэглэгчийн код"
                            value="{{ request('usercode') }}">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            <i class="ti ti-search"></i> Хайх
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary w-100">
                            Цэвэрлэх
                        </a>
                    </div>

                </form>
            </div>
            <div class="card-body p-4">
                <table class="table table-hover table-vcenter card-table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="60" class="text-center">№</th>
                            <th>Байгууллага</th>
                            <th>Албан тушаал</th>
                            <th>Нэр</th>
                            <th>Хэрэглэгчийн код</th>
                            <th>Имэйл</th>
                            <th>Гар утас</th>
                            <th>Хэрэглэгчийн эрх</th>
                            <th class="text-center">Үйлдэл</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="text-center">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>
                                <td>{{ $user->organization?->name }}</td>
                                <td>{{ $user->division?->Div_name }}</td>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td>{{ $user->usercode }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    <span class="badge bg-blue-lt">
                                        {{ $user->permissionLevel?->code ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">

                                    {{-- Засах --}}
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                        <i class="ti ti-edit"></i> Засах
                                    </a>

                                    {{-- Нууц үг солих товч --}}
                                    <a href="{{ route('users.edit-password', $user) }}" class="btn btn-sm btn-primary">
                                        <i class="ti ti-lock"></i> Нууц үг
                                    </a>

                                    {{-- Хэрэглэгчийн эрх тохируулах --}}
                                    <a href="{{ route('users.edit-permissions', $user) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-shield-lock"></i> Эрх
                                    </a>

                                    {{-- Устгах --}}
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Устгахдаа итгэлтэй байна уу?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="ti ti-trash"></i> Устгах
                                        </button>
                                    </form>

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3 px-3">
                    {{ $users->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
