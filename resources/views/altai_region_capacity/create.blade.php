@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>Алтайн бүсийн ачааллын шинэ бүртгэл</h3>

        <form method="POST" action="{{ route('altai-region-capacity.store') }}">
            @csrf

            @include('altai_region_capacity.form')

            <button class="btn btn-success mt-3">Хадгалах</button>
        </form>
    </div>
@endsection
