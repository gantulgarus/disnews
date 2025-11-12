@extends('layouts.admin')


@section('content')
<div class="container mt-4">
    <h2>Тасралтын мэдээ бүртгэх</h2>
    <form method="POST" action="{{ route('tnews.store') }}">
        @csrf

        <div class="mb-3"><label>Огноо:</label><input type="date" name="date" class="form-control" required></div>
        
        <div class="mb-3"><label>Цаг:</label>
        <input type="time" name="time" class="form-control" required></div>

        <div class="mb-3"><label>ТЗЭ:</label>
        <input type="text" name="TZE" class="form-control" required></div>

        <div class="mb-3"><label>Тасралтын мэдээлэл:</label>
        <textarea name="tasralt" class="form-control" required></textarea></div>

        <div class="mb-3"><label>Тайлбар:</label>
        <textarea name="ArgaHemjee" class="form-control"></textarea></div>
        
        <div class="mb-3"><label>Хязгаарлагдсан эрчим хүч:</label>
        <textarea name="HyzErchim" class="form-control"></textarea></div>

        <br>
        <button  class="btn btn-success">Хадгалах</button>
    </form>
</div>

@endsection