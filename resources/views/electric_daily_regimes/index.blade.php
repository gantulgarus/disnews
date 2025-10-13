@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-3">Хоногийн горим төлөвлөлт</h1>

        <a href="{{ route('electric_daily_regimes.create') }}" class="btn btn-primary mb-3">Нэмэх</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Станц</th>
                        <th>TPmax</th>
                        <th>TPmin</th>
                        <th>Pmax</th>
                        <th>Pmin</th>
                        @for ($i = 1; $i <= 24; $i++)
                            <th>{{ $i }}</th>
                        @endfor
                        <th>Нийт</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($regimes as $regime)
                        <tr>
                            <td>{{ $regime->powerPlant->name ?? '' }}</td>
                            <td>{{ number_format($regime->technical_pmax, 0) }}</td>
                            <td>{{ number_format($regime->technical_pmin, 0) }}</td>
                            <td>{{ number_format($regime->pmax, 0) }}</td>
                            <td>{{ number_format($regime->pmin, 0) }}</td>

                            @for ($i = 1; $i <= 24; $i++)
                                <td>{{ number_format($regime->{'hour_' . $i}, 0) }}</td>
                            @endfor
                            <td>{{ number_format($regime->total_mwh, 0) }}</td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Үйлдэл
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" style="">
                                            <a href="{{ route('electric_daily_regimes.edit', $regime->id) }}"
                                                class="dropdown-item">Засах</a>
                                            <form action="{{ route('electric_daily_regimes.destroy', $regime->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item"
                                                    onclick="return confirm('Устгах уу?')">Устгах</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-2">
            {{ $regimes->links() }}
        </div>
    </div>
@endsection
