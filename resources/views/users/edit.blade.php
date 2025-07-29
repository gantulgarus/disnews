@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Хэрэглэгч [Засах]</h2>
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        <div class="mb-3"><label>Байгууллага</label>
        <select name="organization_id" id="organization_id" class="form-control">
         <option value="">-- Байгууллага сонгох --</option>
             @foreach ($organizations as $organization)
            <option value="{{ $organization->id }}"
                {{ $user->organization_id == $organization->id ? 'selected' : '' }}>
                {{ $organization->name }}
            </option>
        @endforeach
    </select>
        </div>
        <div class="mb-3"><label>Нэр</label><input type="text" name="name" value="{{ $user->name }}" class="form-control"></div>
        <div class="mb-3"><label>И-Майл</label><input type="email" name="email" value="{{ $user->email }}" class="form-control"></div>
        <div class="mb-3"><label>Гар утас</label><input type="text" name="phone" value="{{ $user->phone }}" class="form-control"></div>
       
        <button class="btn btn-primary">Хадгалах</button>
    </form>
</div>
@endsection



