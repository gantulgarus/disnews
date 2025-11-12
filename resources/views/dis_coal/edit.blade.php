@extends('layouts.admin')

@section('content')

<div class="container">
    <h2>Түлшний мэдээ [засах]</h2>

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

        <div class="row">
            {{-- Date --}}
            <div>
                <label>Огноо</label>
                <input type="date" name="date" value="{{ $disCoal->date }}" class="form-control" readonly>
        </div>

            {{-- Train Fields --}}
            <div>
                <P class="text-primary"><strong>Вагоны мэдээ</strong></P>
                <label>Nрсэн</label>
                <input type="number" name="CAME_TRAIN" value="{{ $disCoal->CAME_TRAIN }}" class="form-control">
            </div>
            <div>
                <label>Буусан</label>
                <input type="number" name="UNLOADING_TRAIN" value="{{ $disCoal->UNLOADING_TRAIN }}" class="form-control">
            </div>
            <div>
                <label>Үлдсэн</label>
                <input type="number" name="ULDSEIN_TRAIN" value="{{ $disCoal->ULDSEIN_TRAIN }}" class="form-control">
            </div>

            {{-- Coal --}}
            <div>
                <br>
                <P class="text-primary"><strong>Нүүрсний мэдээ</strong></P>
                <label>Орлого</label>
                <input type="number" name="COAL_INCOME" value="{{ $disCoal->COAL_INCOME }}" class="form-control">
            </div>
            <div>
                <label>Зарлага</label>
                <input type="number" name="COAL_OUTCOME" value="{{ $disCoal->COAL_OUTCOME }}" class="form-control">
            </div>
            <div>
                <label>Вагоны тоо</label>
                <input type="number" step="0.01" name="COAL_TRAIN_QUANTITY" value="{{ $disCoal->COAL_TRAIN_QUANTITY }}" class="form-control">
            </div>
            <div>
                <label>Үлдэгдэл</label>
                <input type="number" name="COAL_REMAIN" value="{{ $disCoal->COAL_REMAIN }}" class="form-control">
            </div>
           

            {{-- Mazut --}}
            <div>
                <br>
                <P class="text-primary"><strong>Мазутын мэдээ</strong></P>
                <label>Орлого</label>
                <input type="number" name="MAZUT_INCOME" value="{{ $disCoal->MAZUT_INCOME }}" class="form-control">
            </div>
            <div>
                <label>Зарлага</label>
                <input type="number" name="MAZUT_OUTCOME" value="{{ $disCoal->MAZUT_OUTCOME }}" class="form-control">
            </div>
           
            <div>
                <label>Үлдэгдэл</label>
                <input type="number" name="MAZUT_REMAIN" value="{{ $disCoal->MAZUT_REMAIN }}" class="form-control">
            </div>

            {{-- Mining Supply --}}
            <div>
                <label>Багануурын уурхай</label>
                <input type="number" name="BAGANUUR_MINING_COAL_D" value="{{ $disCoal->BAGANUUR_MINING_COAL_D }}" class="form-control">
            </div>
            <div>
                <label>Шарын голын уурхай</label>
                <input type="number" name="SHARINGOL_MINING_COAL_D" value="{{ $disCoal->SHARINGOL_MINING_COAL_D }}" class="form-control">
            </div>
            <div>
                <label>Шивээ овоогийн уурхай</label>
                <input type="number" name="SHIVEEOVOO_MINING_COAL" value="{{ $disCoal->SHIVEEOVOO_MINING_COAL }}" class="form-control">
            </div>
            <div>
                <label>Бусад</label>
                <input type="number" name="OTHER_MINIG_COAL_SUPPLY" value="{{ $disCoal->OTHER_MINIG_COAL_SUPPLY }}" class="form-control">
            </div>

            {{-- Employees --}}
            

            <div>
                <label>Станц нэр</label> 
                <input type="text" name="ORG_NAME" value="{{ $disCoal->ORG_NAME }}" class="form-control" readonly>
            </div>


            {{-- Organization --}}

        </div>
        <br>
        <button type="submit" class="btn btn-success">Шинэчлэх</button>
        <a href="{{ route('dis_coal.index') }}" class="btn btn-secondary">Буцах</a>
    </form>
</div>
@endsection
