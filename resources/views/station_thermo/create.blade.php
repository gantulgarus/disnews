@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            Шинэ мэдээлэл нэмэх
        </div>
        <div class="card-body">
            <form action="{{ route('station_thermo.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Огноо</label>
                    <input type="date" name="infodate" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Цаг</label>
                    <input type="number" name="infotime" class="form-control">
                </div>

                <!-- Бусад талбарууд шаардлагатай бол энд нэмнэ -->

                <button type="submit" class="btn btn-primary">Хадгалах</button>
            </form>
        </div>
    </div>
@endsection
