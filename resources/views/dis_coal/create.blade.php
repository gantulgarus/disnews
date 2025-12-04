@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="mb-4">Түлшний мэдээ бүртгэх</h2>

        {{-- show all validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
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
                        <label class="form-label">Өдөр <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', now()->toDateString()) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Organization --}}
                    <div class="col-md-6">
                        <label class="form-label">Станц <span class="text-danger">*</span></label>
                        <select name="organization_id" class="form-select @error('organization_id') is-invalid @enderror"
                            required>
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}"
                                    {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organization_id')
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
                        @error('CAME_TRAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Буусан</label>
                        <input type="number" name="UNLOADING_TRAIN"
                            class="form-control @error('UNLOADING_TRAIN') is-invalid @enderror"
                            value="{{ old('UNLOADING_TRAIN') }}">
                        @error('UNLOADING_TRAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Үлдсэн</label>
                        <input type="number" name="ULDSEIN_TRAIN"
                            class="form-control @error('ULDSEIN_TRAIN') is-invalid @enderror"
                            value="{{ old('ULDSEIN_TRAIN') }}">
                        @error('ULDSEIN_TRAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Нүүрсний мэдээ --}}
                <h5 class="text-primary mb-3">Нүүрсний мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Орлого</label>
                        <input type="number" name="COAL_INCOME"
                            class="form-control @error('COAL_INCOME') is-invalid @enderror"
                            value="{{ old('COAL_INCOME') }}">
                        @error('COAL_INCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Зарлага</label>
                        <input type="number" name="COAL_OUTCOME"
                            class="form-control @error('COAL_OUTCOME') is-invalid @enderror"
                            value="{{ old('COAL_OUTCOME') }}">
                        @error('COAL_OUTCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Вагоны тоо</label>
                        <input type="number" step="0.01" name="COAL_TRAIN_QUANTITY"
                            class="form-control @error('COAL_TRAIN_QUANTITY') is-invalid @enderror"
                            value="{{ old('COAL_TRAIN_QUANTITY') }}">
                        @error('COAL_TRAIN_QUANTITY')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Үлдэгдэл</label>
                        <input type="number" name="COAL_REMAIN"
                            class="form-control @error('COAL_REMAIN') is-invalid @enderror"
                            value="{{ old('COAL_REMAIN') }}">
                        @error('COAL_REMAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Мазутын мэдээ --}}
                <h5 class="text-primary mb-3">Мазутын мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Орлого</label>
                        <input type="number" name="MAZUT_INCOME"
                            class="form-control @error('MAZUT_INCOME') is-invalid @enderror"
                            value="{{ old('MAZUT_INCOME') }}">
                        @error('MAZUT_INCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Зарлага</label>
                        <input type="number" name="MAZUT_OUTCOME"
                            class="form-control @error('MAZUT_OUTCOME') is-invalid @enderror"
                            value="{{ old('MAZUT_OUTCOME') }}">
                        @error('MAZUT_OUTCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Тоо</label>
                        <input type="number" name="MAZUT_TRAIN_QUANTITY"
                            class="form-control @error('MAZUT_TRAIN_QUANTITY') is-invalid @enderror"
                            value="{{ old('MAZUT_TRAIN_QUANTITY') }}">
                        @error('MAZUT_TRAIN_QUANTITY')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Үлдэгдэл</label>
                        <input type="number" name="MAZUT_REMAIN"
                            class="form-control @error('MAZUT_REMAIN') is-invalid @enderror"
                            value="{{ old('MAZUT_REMAIN') }}">
                        @error('MAZUT_REMAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Нүүрс нийлүүлэлт --}}
                <h5 class="text-primary mb-3">Нүүрс нийлүүлэлтийн мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Багануурын уурхай</label>
                        <input type="number" name="BAGANUUR_MINING_COAL_D"
                            class="form-control @error('BAGANUUR_MINING_COAL_D') is-invalid @enderror"
                            value="{{ old('BAGANUUR_MINING_COAL_D') }}">
                        @error('BAGANUUR_MINING_COAL_D')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Шарын голын уурхай</label>
                        <input type="number" name="SHARINGOL_MINING_COAL_D"
                            class="form-control @error('SHARINGOL_MINING_COAL_D') is-invalid @enderror"
                            value="{{ old('SHARINGOL_MINING_COAL_D') }}">
                        @error('SHARINGOL_MINING_COAL_D')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Шивээ овоогийн уурхай</label>
                        <input type="number" name="SHIVEEOVOO_MINING_COAL"
                            class="form-control @error('SHIVEEOVOO_MINING_COAL') is-invalid @enderror"
                            value="{{ old('SHIVEEOVOO_MINING_COAL') }}">
                        @error('SHIVEEOVOO_MINING_COAL')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Бусад</label>
                        <input type="number" name="OTHER_MINIG_COAL_SUPPLY"
                            class="form-control @error('OTHER_MINIG_COAL_SUPPLY') is-invalid @enderror"
                            value="{{ old('OTHER_MINIG_COAL_SUPPLY') }}">
                        @error('OTHER_MINIG_COAL_SUPPLY')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Хадгалах</button>
                <a href="{{ route('dis_coal.index') }}" class="btn btn-secondary">Буцах</a>
            </div>
        </form>
    </div>
@endsection
