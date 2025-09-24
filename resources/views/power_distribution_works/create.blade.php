@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Шинэ захиалгат ажил нэмэх</h1>
        <form action="{{ route('power-distribution-works.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>ТЗЭ</label>
                <input type="text" name="tze" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Засварын ажлын утга</label>
                <input type="text" name="repair_work" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Тайлбар</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label>Хязгаарласан эрчим хүч</label>
                <input type="number" step="0.01" name="restricted_energy" class="form-control">
            </div>
            <div class="mb-3">
                <label>Огноо</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Нэмэх</button>
        </form>
    </div>
@endsection
