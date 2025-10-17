@extends('layouts.admin')

@section('content')


<div class="container mt-4">


    <div class="d-flex justify-content-between align-items-center mb-3">

        <h4 class="mb-3">Ажилтнуудын жагсаалт</h4>
        <a href="{{ route('users.create') }}" class="btn btn-primary">+ Хэрэглэгч нэмэх</a>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Байгууллага</th>
                <th>Нэр</th>
                <th>Имэйл</th>
                <th>Гар утас</th>
                <th>Хэрэглэгчийн эрх</th>
                <th>Үйлдэл</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                     <td>{{ $user->organization?->name }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->permissionLevel?->name ?? '-' }}</td>
                    
                    <td>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Засах</a>

                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Устгахдаа итгэлтэй байна уу?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Устгах</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

