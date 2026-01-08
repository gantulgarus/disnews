@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="mb-4">Түлшний мэдээ [засах]</h2>

        {{-- show all validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('dis_coal.update', $disCoal->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow-sm p-4 mb-4">
                <div class="row g-3">
                    {{-- Date --}}
                    <div class="col-md-6">
                        <label class="form-label">Өдөр <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', $disCoal->date) }}" readonly>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Power Plant --}}
                    <div class="col-md-6">
                        <label class="form-label">Цахилгаан станц <span class="text-danger">*</span></label>
                        <select name="power_plant_id" class="form-select @error('power_plant_id') is-invalid @enderror"
                            required>
                            @foreach ($powerPlants as $plant)
                                <option value="{{ $plant->id }}"
                                    {{ old('power_plant_id', $disCoal->power_plant_id) == $plant->id ? 'selected' : '' }}>
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
                            class="form-control @error('CAME_TRAIN') is-invalid @enderror"
                            value="{{ old('CAME_TRAIN', $disCoal->CAME_TRAIN) }}">
                        @error('CAME_TRAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Буусан</label>
                        <input type="number" name="UNLOADING_TRAIN"
                            class="form-control @error('UNLOADING_TRAIN') is-invalid @enderror"
                            value="{{ old('UNLOADING_TRAIN', $disCoal->UNLOADING_TRAIN) }}">
                        @error('UNLOADING_TRAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Үлдсэн</label>
                        <input type="number" name="ULDSEIN_TRAIN"
                            class="form-control @error('ULDSEIN_TRAIN') is-invalid @enderror"
                            value="{{ old('ULDSEIN_TRAIN', $disCoal->ULDSEIN_TRAIN) }}">
                        @error('ULDSEIN_TRAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Нүүрсний мэдээ --}}
                <h5 class="text-primary mb-3">Нүүрсний мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Орлого</label>
                        <input type="number" step="0.01" name="COAL_INCOME"
                            class="form-control @error('COAL_INCOME') is-invalid @enderror"
                            value="{{ old('COAL_INCOME', $disCoal->COAL_INCOME) }}">
                        @error('COAL_INCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Зарлага</label>
                        <input type="number" step="0.01" name="COAL_OUTCOME"
                            class="form-control @error('COAL_OUTCOME') is-invalid @enderror"
                            value="{{ old('COAL_OUTCOME', $disCoal->COAL_OUTCOME) }}">
                        @error('COAL_OUTCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Үлдэгдэл</label>
                        <input type="number" step="0.01" name="COAL_REMAIN"
                            class="form-control @error('COAL_REMAIN') is-invalid @enderror"
                            value="{{ old('COAL_REMAIN', $disCoal->COAL_REMAIN) }}">
                        @error('COAL_REMAIN')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Мазутын мэдээ --}}
                <h5 class="text-primary mb-3">Мазутын мэдээ</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Орлого</label>
                        <input type="number" step="0.01" name="MAZUT_INCOME"
                            class="form-control @error('MAZUT_INCOME') is-invalid @enderror"
                            value="{{ old('MAZUT_INCOME', $disCoal->MAZUT_INCOME) }}">
                        @error('MAZUT_INCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Зарлага</label>
                        <input type="number" step="0.01" name="MAZUT_OUTCOME"
                            class="form-control @error('MAZUT_OUTCOME') is-invalid @enderror"
                            value="{{ old('MAZUT_OUTCOME', $disCoal->MAZUT_OUTCOME) }}">
                        @error('MAZUT_OUTCOME')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Үлдэгдэл</label>
                        <input type="number" step="0.01" name="MAZUT_REMAIN"
                            class="form-control @error('MAZUT_REMAIN') is-invalid @enderror"
                            value="{{ old('MAZUT_REMAIN', $disCoal->MAZUT_REMAIN) }}">
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
                            value="{{ old('BAGANUUR_MINING_COAL_D', $disCoal->BAGANUUR_MINING_COAL_D) }}">
                        @error('BAGANUUR_MINING_COAL_D')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Шарын голын уурхай</label>
                        <input type="number" name="SHARINGOL_MINING_COAL_D"
                            class="form-control @error('SHARINGOL_MINING_COAL_D') is-invalid @enderror"
                            value="{{ old('SHARINGOL_MINING_COAL_D', $disCoal->SHARINGOL_MINING_COAL_D) }}">
                        @error('SHARINGOL_MINING_COAL_D')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Шивээ овоогийн уурхай</label>
                        <input type="number" name="SHIVEEOVOO_MINING_COAL"
                            class="form-control @error('SHIVEEOVOO_MINING_COAL') is-invalid @enderror"
                            value="{{ old('SHIVEEOVOO_MINING_COAL', $disCoal->SHIVEEOVOO_MINING_COAL) }}">
                        @error('SHIVEEOVOO_MINING_COAL')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Бусад</label>
                        <input type="number" name="OTHER_MINIG_COAL_SUPPLY"
                            class="form-control @error('OTHER_MINIG_COAL_SUPPLY') is-invalid @enderror"
                            value="{{ old('OTHER_MINIG_COAL_SUPPLY', $disCoal->OTHER_MINIG_COAL_SUPPLY) }}">
                        @error('OTHER_MINIG_COAL_SUPPLY')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Шинэчлэх</button>
                <a href="{{ route('dis_coal.index') }}" class="btn btn-secondary">Буцах</a>
            </div>
        </form>
    </div>
@endsection
