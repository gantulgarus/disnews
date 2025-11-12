@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Station Thermo Data</h2>
        <a href="{{ route('station_thermo.create') }}" class="btn btn-primary">Нэмэх</a>
    </div>
    <!-- Filter form -->
    <form action="{{ route('station_thermo.index') }}" method="GET" class="row g-2 m-2">
        <div class="col-auto">
            <input type="date" name="date" class="form-control" value="{{ $date }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Хайх</button>
        </div>
    </form>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive m-2">
        <table class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Огноо</th>
                    <th>Цаг</th>
                    <th>pp2p1</th>
                    <th>pp4p1</th>
                    <th>pp4p2</th>
                    <th>pp4t1</th>
                    <th>pp4700t2</th>
                    <th>pp41000t2</th>
                    <th>pp41200t2</th>
                    <th>pp4y700t2</th>
                    <th>pp4700g1</th>
                    <th>pp41000g1</th>
                    <th>pp41200g1</th>
                    <th>pp4y700g1</th>
                    <th>pp4700g2</th>
                    <th>pp41000g2</th>
                    <th>pp41200g2</th>
                    <th>pp4y700g2</th>
                    <th>pp4gn</th>
                    <th>pp4g</th>
                    <th>pp4210t2</th>
                    <th>pp4210g1</th>
                    <th>pp4210g2</th>
                    {{-- <th>Actions</th> --}}
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->infodate }}</td>
                        <td>{{ $row->infotime }}</td>
                        <td>{{ $row->pp2p1 }}</td>
                        <td>{{ $row->pp4p1 }}</td>
                        <td>{{ $row->pp4p2 }}</td>
                        <td>{{ $row->pp4t1 }}</td>
                        <td>{{ $row->pp4700t2 }}</td>
                        <td>{{ $row->pp41000t2 }}</td>
                        <td>{{ $row->pp41200t2 }}</td>
                        <td>{{ $row->pp4y700t2 }}</td>
                        <td>{{ $row->pp4700g1 }}</td>
                        <td>{{ $row->pp41000g1 }}</td>
                        <td>{{ $row->pp41200g1 }}</td>
                        <td>{{ $row->pp4y700g1 }}</td>
                        <td>{{ $row->pp4700g2 }}</td>
                        <td>{{ $row->pp41000g2 }}</td>
                        <td>{{ $row->pp41200g2 }}</td>
                        <td>{{ $row->pp4y700g2 }}</td>
                        <td>{{ $row->pp4gn }}</td>
                        <td>{{ $row->pp4g }}</td>
                        <td>{{ $row->pp4210t2 }}</td>
                        <td>{{ $row->pp4210g1 }}</td>
                        <td>{{ $row->pp4210g2 }}</td>
                        {{-- <td>
                            <a href="{{ route('station_thermo.edit', $row->id) }}" class="btn btn-sm btn-warning">Засах</a>
                            <form action="{{ route('station_thermo.destroy', $row->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Устгах уу?')">Устгах</button>
                            </form>
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $data->links('pagination::bootstrap-5') }}
@endsection
