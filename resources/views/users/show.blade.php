@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Хэрэглэгч</h2>
    <p><strong>Байгууллага:</strong> {{ $user->organization_id }}</p>
    <p><strong>Нэр:</strong> {{ $user->name }}</p>
    <p><strong>И-Майл:</strong> {{ $user->email }}</p>
    <p><strong>Гар утас:</strong> {{ $user->phone }}</p>
    
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Буцах</a>
</div>
