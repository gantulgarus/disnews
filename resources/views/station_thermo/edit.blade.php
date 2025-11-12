@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            Мэдээлэл засах
        </div>
        <div class="card-body">
            <form action="{{ route('station_thermo.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Огноо</label>
                    <input type="date" name="infodate" value="{{ $item->infodate }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Цаг</label>
                    <input type="number" name="infotime" value="{{ $item->infotime }}" class="form-control">
                </div>

                <!-- Бусад талбарууд шаардлагатай бол энд нэмнэ -->

                <button type="submit" class="btn btn-success">Шинэчлэх</button>
            </form>
        </div>
    </div>
@endsection
