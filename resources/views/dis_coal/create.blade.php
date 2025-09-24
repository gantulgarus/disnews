@extends('layouts.admin')

@section('content')

<div class="container">
    <h2>Түлшний мэдээ</h2>

    <form action="{{ route('dis_coal.store') }}" method="POST">
        @csrf

        <div class="row">
            {{-- Date --}}
            <div class="col-md-4 mb-3">
                <label>Өдөр</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            {{-- Train Fields --}}
            <div class="col-md-4 mb-3">
                <label>Вагон ирсэн</label>
                <input type="number" name="CAME_TRAIN" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Буусан</label>
                <input type="number" name="UNLOADING_TRAIN" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Үлдсэн</label>
                <input type="number" name="ULDSEIN_TRAIN" class="form-control">
            </div>

            {{-- Coal --}}
            <div class="col-md-4 mb-3">
                <label>Нүүрс Орлого</label>
                <input type="number" name="COAL_INCOME" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Зарлага</label>
                <input type="number" name="COAL_OUTCOME" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Вагоны тоо</label>
                <input type="number" step="0.01" name="COAL_TRAIN_QUANTITY" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Үлдэгдэл</label>
                <input type="number" name="COAL_REMAIN" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Хоногийн нөөц</label>
                <input type="number" step="0.01" name="COAL_REMAIN_BYDAY" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Өвлийн их ачааллын нөөц</label>
                <input type="number" name="COAL_REMAIN_BYWINTERDAY" class="form-control">
            </div>

            {{-- Mazut --}}
            <div class="col-md-4 mb-3">
                <label>Мазут Орлого</label>
                <input type="number" name="MAZUT_INCOME" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Зарлага</label>
                <input type="number" name="MAZUT_OUTCOME" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Тоо</label>
                <input type="number" name="MAZUT_TRAIN_QUANTITY" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Үлдэгдэл</label>
                <input type="number" name="MAZUT_REMAIN" class="form-control">
            </div>

            {{-- Mining Supply --}}
            <div class="col-md-4 mb-3">
                <label>Багануурын уурхай</label>
                <input type="number" name="BAGANUUR_MINING_COAL_D" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Шарын голын уурхай</label>
                <input type="number" name="SHARINGOL_MINING_COAL_D" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Шивээ овоогийн уурхай</label>
                <input type="number" name="SHIVEEOVOO_MINING_COAL" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Бусад</label>
                <input type="number" name="OTHER_MINIG_COAL_SUPPLY" class="form-control">
            </div>

            {{-- Organization --}}
            <div class="col-md-4 mb-3">
                <label>станц код</label>
                <input type="number" name="ORG_CODE" class="form-control" required>
            </div>
            <div class="col-md-8 mb-3">
                <label>Станц</label>
                <input type="text" name="ORG_NAME" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Хадгалах</button>
        <a href="{{ route('dis_coal.index') }}" class="btn btn-secondary">Буцах</a>
    </form>
</div>
@endsection
