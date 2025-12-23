@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="mb-4">Түлшний мэдээ бүртгэх</h2>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dis_coal.store') }}" method="POST">
            @csrf

            <div class="card shadow-sm p-4 mb-4">
                <div class="row g-3">
                    {{-- Date --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Өдөр <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', now()->toDateString()) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Power plant --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Станц <span class="text-danger">*</span>
                        </label>
                        <select name="power_plant_id" class="form-select @error('power_plant_id') is-invalid @enderror"
                            required>
                            <option value="">-- Станц сонгох --</option>
                            @foreach ($powerPlants as $plant)
                                <option value="{{ $plant->id }}"
                                    {{ old('power_plant_id') == $plant->id ? 'selected' : '' }}>
                                    {{ $plant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('power_plant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Вагоны мэдээ --}}
                <h5 class="text-primary mb-3">Вагоны мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Ирсэн</label>
                        <input type="number" name="CAME_TRAIN"
                            class="form-control @error('CAME_TRAIN') is-invalid @enderror" value="{{ old('CAME_TRAIN') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Буусан</label>
                        <input type="number" name="UNLOADING_TRAIN"
                            class="form-control @error('UNLOADING_TRAIN') is-invalid @enderror"
                            value="{{ old('UNLOADING_TRAIN') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Үлдсэн</label>
                        <input type="number" name="ULDSEIN_TRAIN"
                            class="form-control @error('ULDSEIN_TRAIN') is-invalid @enderror"
                            value="{{ old('ULDSEIN_TRAIN') }}">
                    </div>
                </div>

                <hr class="my-4">

                {{-- Нүүрсний мэдээ --}}
                <h5 class="text-primary mb-3">Нүүрсний мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Орлого</label>
                        <input type="number" name="COAL_INCOME" class="form-control" value="{{ old('COAL_INCOME') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Зарлага</label>
                        <input type="number" name="COAL_OUTCOME" class="form-control" value="{{ old('COAL_OUTCOME') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Вагоны тоо</label>
                        <input type="number" step="0.01" name="COAL_TRAIN_QUANTITY" class="form-control"
                            value="{{ old('COAL_TRAIN_QUANTITY') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Үлдэгдэл</label>
                        <input type="number" name="COAL_REMAIN" class="form-control" value="{{ old('COAL_REMAIN') }}">
                    </div>
                </div>

                <hr class="my-4">

                {{-- Мазут --}}
                <h5 class="text-primary mb-3">Мазутын мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Орлого</label>
                        <input type="number" name="MAZUT_INCOME" class="form-control" value="{{ old('MAZUT_INCOME') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Зарлага</label>
                        <input type="number" name="MAZUT_OUTCOME" class="form-control" value="{{ old('MAZUT_OUTCOME') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Тоо</label>
                        <input type="number" name="MAZUT_TRAIN_QUANTITY" class="form-control"
                            value="{{ old('MAZUT_TRAIN_QUANTITY') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Үлдэгдэл</label>
                        <input type="number" name="MAZUT_REMAIN" class="form-control" value="{{ old('MAZUT_REMAIN') }}">
                    </div>
                </div>

                <hr class="my-4">

                {{-- Нүүрс нийлүүлэлт --}}
                <h5 class="text-primary mb-3">Нүүрс нийлүүлэлтийн мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Багануур</label>
                        <input type="number" name="BAGANUUR_MINING_COAL_D" class="form-control"
                            value="{{ old('BAGANUUR_MINING_COAL_D') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Шарын гол</label>
                        <input type="number" name="SHARINGOL_MINING_COAL_D" class="form-control"
                            value="{{ old('SHARINGOL_MINING_COAL_D') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Шивээ-Овоо</label>
                        <input type="number" name="SHIVEEOVOO_MINING_COAL" class="form-control"
                            value="{{ old('SHIVEEOVOO_MINING_COAL') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Бусад</label>
                        <input type="number" name="OTHER_MINIG_COAL_SUPPLY" class="form-control"
                            value="{{ old('OTHER_MINIG_COAL_SUPPLY') }}">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Хадгалах</button>
                <a href="{{ route('dis_coal.index') }}" class="btn btn-secondary">Буцах</a>
            </div>
        </form>
    </div>
@endsection
