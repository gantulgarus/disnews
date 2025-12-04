@extends('layouts.admin')


@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">Хоногийн ачааллын графикын цагуудын мэдээ</h3>

        <form method="GET" class="mb-4 row g-2 align-items-end">
            <div class="col-auto">
                {{-- <label for="date" class="form-label">Огноо:</label> --}}
                <input type="date" name="date" id="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Харах</button>
            </div>
        </form>

    </div>
@endsection
