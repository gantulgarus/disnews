@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">

        <h3 class="fw-bold mb-4">Хэрэглэгчийн эрх тохируулах: {{ $user->name }}</h3>

        <form action="{{ route('users.update-permissions', $user) }}" method="POST">
            @csrf

            @foreach ($permissions as $group => $groupPermissions)
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        {{ ucfirst($group) }} цэс
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($groupPermissions as $permission)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                            {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->display_name ?? $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="text-end">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Буцах</a>
                <button type="submit" class="btn btn-primary">Хадгалах</button>
            </div>
        </form>
    </div>
@endsection
