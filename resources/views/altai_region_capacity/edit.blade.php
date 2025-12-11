@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>Мэдээлэл засах — {{ $item->date }}</h3>

        <form method="POST" action="{{ route('altai-region-capacity.update', $item->id) }}">
            @csrf
            @method('PUT')

            @include('altai_region_capacity.form', ['item' => $item])

            <button class="btn btn-primary mt-3">Шинэчлэх</button>
        </form>
    </div>
@endsection
