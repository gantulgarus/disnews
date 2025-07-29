@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Тасралтын мэдээ засварлах</h2>
    <form method="POST" action="{{ route('tnews.update', $tnews->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Огноо:</label>
            <input type="date" name="date" value="{{ $tnews->date }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Цаг:</label>
            <input type="time" name="time" value="{{ $tnews->time }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>ТЗЭ:</label>
            <input type="text" name="TZE" value="{{ $tnews->TZE }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Тасралтын мэдээлэл:</label>
            <textarea name="tasralt" class="form-control" required>{{ $tnews->tasralt }}</textarea>
        </div>

        <div class="mb-3">
            <label>Тайлбар:</label>
            <textarea name="ArgaHemjee" class="form-control">{{ $tnews->ArgaHemjee }}</textarea>
        </div>

        <div class="mb-3">
            <label>Хязгаарлагдсан эрчим хүч:</label>
            <textarea name="HyzErchim" class="form-control">{{ $tnews->HyzErchim }}</textarea>
        </div>

        <button class="btn btn-primary">Шинэчлэх</button>
    </form>
</div>
@endsection
