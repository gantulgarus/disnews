@extends('layouts.admin')

@section('content')

<div class="container mt-4">
    <h2>Хэрэглэгч бүртгэх</h2>
    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="mb-3"><label>Байгууллага</label>
        <select name="organization_id" id="organization_id" class="form-control" required>
            <option value="">-- Байгууллага сонгох --</option>
            @foreach ($organizations as $organization)
                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
            @endforeach
        </select>

        </div>
        <div class="mb-3"><label>Нэр</label><input type="text" name="name" class="form-control"></div>
        <div class="mb-3"><label>И-Майл</label><input type="email" name="email" class="form-control"></div>
        <div class="mb-3"><label>Гар утас</label><input type="text" name="phone" class="form-control"></div>
        <div class="mb-3"><label>Нууц үг</label><input type="password" name="password" class="form-control"></div>

        <div class="mb-3">
            <label>Хэрэглэгчийн эрх</label>
            <select name="permission_code" class="form-control">
                <option value="">-- Сонгох --</option>
                @foreach ($permissions as $perm)
                    <option value="{{ $perm->code }}" {{ old('permission_code') == $perm->code ? 'selected' : '' }}>
                        {{ $perm->name }}
                    </option>
                @endforeach
            </select>
        </div>


        <button class="btn btn-success">Хадгалах</button>
    </form>
</div>

@endsection
