@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Журнал засах</h2>

        <form action="{{ route('daily-balance-journals.update', $dailyBalanceJournal) }}" method="POST">
            @csrf @method('PUT')
            @include('daily_balance_journals.form', ['journal' => $dailyBalanceJournal])
            <button type="submit" class="btn btn-success">Шинэчлэх</button>
            <a href="{{ route('daily-balance-journals.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
