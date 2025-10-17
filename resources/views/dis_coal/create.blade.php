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
           
            <div>
                <P class="text-primary"><strong>Вагоны мэдээ</strong></P>
                <label>Ирсэн</label>
                <input type="number" name="CAME_TRAIN" class="form-control">
            </div>
            <div >
                <label>Буусан</label>
                <input type="number" name="UNLOADING_TRAIN" class="form-control">
            </div>
            <div>
                <label>Үлдсэн</label>
                <input type="number" name="ULDSEIN_TRAIN" class="form-control">
            </div>



            {{-- Coal --}}
            <div>
                <br>
                <P class="text-primary"><strong>Нүүрсний мэдээ</strong></P>
                <label>Орлого</label>
                <input type="number" name="COAL_INCOME" class="form-control">
            </div>
            <div>
                <label>Зарлага</label>
                <input type="number" name="COAL_OUTCOME" class="form-control">
            </div>
            <div>
                <label>Вагоны тоо</label>
                <input type="number" step="0.01" name="COAL_TRAIN_QUANTITY" class="form-control">
            </div>
            <div>
                <label>Үлдэгдэл</label>
                <input type="number" name="COAL_REMAIN" class="form-control">
            </div>
           
            

            {{-- Mazut --}}
            <div>
                <br>
                <P class="text-primary"><strong>Мазутын мэдээ</strong></P>
                <label>Орлого</label>
                <input type="number" name="MAZUT_INCOME" class="form-control">
            </div>
            <div>
                <label>Зарлага</label>
                <input type="number" name="MAZUT_OUTCOME" class="form-control">
            </div>
            <div>
                <label>Тоо</label>
                <input type="number" name="MAZUT_TRAIN_QUANTITY" class="form-control">
            </div>
            <div>
                <label>Үлдэгдэл</label>
                <input type="number" name="MAZUT_REMAIN" class="form-control">
            </div>

            {{-- Mining Supply --}}
            <div>
                <br>
                <P class="text-primary"><strong>Нүүрс нийлүүлэлтийн мэдээ</strong></P>
                <label>Багануурын уурхай</label>
                <input type="number" name="BAGANUUR_MINING_COAL_D" class="form-control">
            </div>
            <div>
                <label>Шарын голын уурхай</label>
                <input type="number" name="SHARINGOL_MINING_COAL_D" class="form-control">
            </div>
            <div>
                <label>Шивээ овоогийн уурхай</label>
                <input type="number" name="SHIVEEOVOO_MINING_COAL" class="form-control">
            </div>
            <div>
                <label>Бусад</label>
                <input type="number" name="OTHER_MINIG_COAL_SUPPLY" class="form-control">
            </div>

            {{-- Organization --}}
           
            <div>
                <label>Станц</label>
                <input type="text" name="ORG_NAME" class="form-control" required>
            </div>
        </div>
        <br>
        <br>
        <button type="submit" class="btn btn-success">Хадгалах</button>
        <a href="{{ route('dis_coal.index') }}" class="btn btn-secondary">Буцах</a>
    </form>
</div>
@endsection
