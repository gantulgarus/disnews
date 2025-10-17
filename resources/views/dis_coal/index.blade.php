@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4">Түлшний мэдээ</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('dis_coal.create') }}" class="btn btn-primary mb-3">+ мэдээ оруулах</a>

    <div class="card mt-4">    
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="text-center">
                <tr>
                    <th rowspan="2">Станц</th>
                    <th colspan="3" class="text-center">Вагон буулгалт</th>
                    <th colspan="6" class="text-center">Нүүрс /тонн/</th>
                    <th colspan="3" class="text-center">Мазут /тонн/</th>
                    <th colspan="4" class="text-center">ДЦС-уудад нүүрсний нийлүүлэлт /вагон/</th>
                </tr>
                <tr>
                    
                    <th>Ирсэн</th>
                    <th>Буусан</th>
                    <th>Үлдсэн</th>

                    <th>Орлого</th>
                    <th>Зарлага</th>
                    <th>Вагоны <br>тоо</th>
                    <th>Үлдэгдэл</th>
                    <th>Хоногийн <br>нөөц</th>
                    <th>Өвлийн их <br>ачааллын<br>нөөц</th>

                    <th>Орлого</th>
                    <th>Зарлага</th>
                    <th>Үлдэгдэл</th>

                    <th>Багануурын <br>уурхай</th>
                    <th>Шарын <br>голын <br> уурхай</th>
                    <th>Шивээ <br>овоогийн <br> уурхай</th>
                    <th>Бусад</th>

                    <th width="120px">Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                @foreach($disCoals as $disCoal)
                    <tr>
                        
                        <td class="text-center">{{ $disCoal->ORG_NAME }}</td>
                        <td class="text-center">{{ $disCoal->CAME_TRAIN }}</td>
                        <td class="text-center table-secondary">{{ $disCoal->UNLOADING_TRAIN }}</td>
                        <td class="text-center">{{ $disCoal->ULDSEIN_TRAIN }}</td>
                        <td class="text-center table-secondary">{{ $disCoal->COAL_INCOME }}</td>
                        <td class="text-center table-secondary">{{ $disCoal->COAL_OUTCOME }}</td>
                        <td class="text-center">{{ $disCoal->COAL_TRAIN_QUANTITY }}</td>
                        <td class="text-center table-secondary">{{ $disCoal->COAL_REMAIN }}</td>
                        <td class="text-center table-secondary">{{ $disCoal->COAL_REMAIN_BYDAY }}</td>
                        <td class="text-center">{{ $disCoal->COAL_REMAIN_BYWINTERDAY }}</td>
                        <td class="text-center">{{ $disCoal->MAZUT_INCOME }}</td>
                        <td class="text-center">{{ $disCoal->MAZUT_OUTCOME }}</td>
                        <td class="text-center table-secondary">{{ $disCoal->MAZUT_REMAIN }}</td>
                        <td class="text-center">{{ $disCoal->BAGANUUR_MINING_COAL_D }}</td>
                        <td class="text-center">{{ $disCoal->SHARINGOL_MINING_COAL_D }}</td>
                        <td class="text-center">{{ $disCoal->SHIVEEOVOO_MINING_COAL }}</td>
                        <td class="text-center">{{ $disCoal->OTHER_MINIG_COAL_SUPPLY }}</td>
                        
                        <td>
                            <a href="{{ route('dis_coal.edit', $disCoal->id) }}" class="btn btn-sm btn-warning">засах</a>

                            <form action="{{ route('dis_coal.destroy', $disCoal->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Устгахдаа итгэлтэй байна уу?')">
                                    устгах
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination (if enabled in controller) --}}

    <div class="mt-3">
        {{ $disCoals->links() }}
    </div>
      </div>
</div>
@endsection
