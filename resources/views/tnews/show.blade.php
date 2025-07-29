@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Тасралтын мэдээлэл</h2>
    <p><strong>Огноо:</strong> {{ $tnews->date }}</p>
    <p><strong>Цаг:</strong> {{ $tnews->time }}</p>
    <p><strong>ТЗЭ:</strong> {{ $tnews->TZE }}</p>
    <p><strong>Тасралт:</strong> {{ $tnews->tasralt }}</p>
    <p><strong>Арга хэмжээ:</strong> {{ $tnews->ArgaHemjee }}</p>
    <p><strong>Хязгаарлагдсан эрчим хүч:</strong> {{ $tnews->HyzErchim }}</p>
</div>
@endsection
