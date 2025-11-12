@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Шинээр журнал бүртгэх</h2>

        <form action="{{ route('daily-balance-journals.store') }}" method="POST">
            @csrf
            @include('daily_balance_journals.form')
            <button type="submit" class="btn btn-primary">Хадгалах</button>
            <a href="{{ route('daily-balance-journals.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
