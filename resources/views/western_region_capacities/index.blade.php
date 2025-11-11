@extends('layouts.admin')

@section('title', 'Баруун бүсийн системийн чадлын жагсаалт')

@section('content')
    <div class="container">

        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Баруун бүсийн системийн чадлын жагсаалт
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('western_region_capacities.create') }}" class="btn btn-primary">
                        Шинээр нэмэх
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>P Max</th>
                            <th>P Min</th>
                            <th>Импорт авсан</th>
                            <th>Импорт түгээсэн</th>
                            <th>Огноо</th>
                            <th>Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($capacities as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->p_max }}</td>
                                <td>{{ $item->p_min }}</td>
                                <td>{{ $item->import_received }}</td>
                                <td>{{ $item->import_distributed }}</td>
                                <td>{{ $item->date }}</td>
                                <td>
                                    <a href="{{ route('western_region_capacities.edit', $item->id) }}"
                                        class="btn btn-sm btn-warning">Засах</a>
                                    <form action="{{ route('western_region_capacities.destroy', $item->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Устгах уу?')">Устгах</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $capacities->links() }}
            </div>
        </div>
    </div>
@endsection
