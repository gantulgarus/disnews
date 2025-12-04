@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('dis_coal.create') }}" class="btn btn-primary mb-3">+ мэдээ оруулах</a>

        <div class="card mt-4">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h4 class="card-title mb-2 mb-md-0">Түлшний мэдээ</h4>

                <form method="GET" action="{{ route('dis_coal.index') }}" class="row g-2 align-items-end mb-0">
                    <div class="col-12 col-md-auto">
                        <label class="form-label visually-hidden" for="date">Огноо</label>
                        <input type="date" id="date" name="date"
                            value="{{ request('date', now()->toDateString()) }}" class="form-control" placeholder="Огноо">
                    </div>

                    @if ($userOrgId == 5)
                        <div class="col-12 col-md-auto">
                            <label class="form-label visually-hidden" for="organization_id">Байгууллага</label>
                            <select name="organization_id" id="organization_id" class="form-select">
                                <option value="">Бүгд</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->id }}"
                                        {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                                        {{ $org->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary w-100">Хайх</button>
                    </div>
                </form>
            </div>


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
                                <th></th>
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
                            @foreach ($disCoals as $disCoal)
                                <tr>

                                    <td class="text-center" style="font-size: 12px; white-space: nowrap;">
                                        {{ $disCoal->organization->name }}</td>
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

                                    <td class="text-center">
                                        <div class="btn-list justify-content-center" style="white-space: nowrap;">
                                            <a href="{{ route('dis_coal.edit', $disCoal->id) }}"
                                                class="btn btn-warning btn-sm btn-icon" title="Засах">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('dis_coal.destroy', $disCoal->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Устгах уу?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm btn-icon" title="Устгах">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
