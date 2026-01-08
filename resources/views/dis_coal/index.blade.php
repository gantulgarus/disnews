@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <a href="{{ route('dis_coal.create') }}" class="btn btn-primary mb-3">
            + мэдээ оруулах
        </a>

        <div class="card mt-4">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h4 class="card-title mb-2 mb-md-0">Түлшний мэдээ</h4>

                <form method="GET" action="{{ route('dis_coal.index') }}" class="row g-2 align-items-end mb-0">
                    <div class="col-12 col-md-auto">
                        <input type="date" name="date" value="{{ $date }}" class="form-control">
                    </div>

                    {{-- ДҮТ --}}
                    @if ($userOrgId == 5)
                        <div class="col-12 col-md-auto">
                            <select name="power_plant_id" class="form-select">
                                <option value="">Бүгд станц</option>
                                @foreach ($powerPlants as $plant)
                                    <option value="{{ $plant->id }}"
                                        {{ request('power_plant_id') == $plant->id ? 'selected' : '' }}>
                                        {{ $plant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary w-100">
                            Хайх
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="text-center">
                            <tr>
                                <th rowspan="2">Станц</th>
                                <th colspan="3">Вагон буулгалт</th>
                                <th colspan="6">Нүүрс /тонн/</th>
                                <th colspan="3">Мазут /тонн/</th>
                                <th colspan="4">Нүүрсний нийлүүлэлт /вагон/</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>Ирсэн</th>
                                <th>Буусан</th>
                                <th>Үлдсэн</th>

                                <th>Орлого</th>
                                <th>Зарлага</th>
                                <th>Вагоны тоо</th>
                                <th>Үлдэгдэл</th>
                                <th>Хоногийн нөөц</th>
                                <th>Өвлийн нөөц</th>

                                <th>Орлого</th>
                                <th>Зарлага</th>
                                <th>Үлдэгдэл</th>

                                <th>Багануур</th>
                                <th>Шарын гол</th>
                                <th>Шивээ-Овоо</th>
                                <th>Бусад</th>

                                <th width="120">Үйлдэл</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($disCoals as $disCoal)
                                <tr>
                                    <td class="text-center" style="font-size:12px; white-space:nowrap;">
                                        {{ $disCoal->powerPlant?->name }}
                                    </td>

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
                                        <a href="{{ route('dis_coal.edit', $disCoal->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                        <form action="{{ route('dis_coal.destroy', $disCoal->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Устгах уу?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </tbody>

                        <tfoot class="table-info">
                            <tr>
                                <td class="text-center"><strong>Нийт:</strong></td>

                                <td class="text-center"><strong>{{ $disCoals->sum('CAME_TRAIN') }}</strong></td>
                                <td class="text-center table-secondary">
                                    <strong>{{ $disCoals->sum('UNLOADING_TRAIN') }}</strong>
                                </td>
                                <td class="text-center"><strong>{{ $disCoals->sum('ULDSEIN_TRAIN') }}</strong></td>

                                <td class="text-center table-secondary">
                                    <strong>{{ $disCoals->sum('COAL_INCOME') }}</strong>
                                </td>
                                <td class="text-center table-secondary">
                                    <strong>{{ $disCoals->sum('COAL_OUTCOME') }}</strong>
                                </td>
                                <td class="text-center"><strong>{{ $disCoals->sum('COAL_TRAIN_QUANTITY') }}</strong></td>
                                <td class="text-center table-secondary">
                                    <strong>{{ $disCoals->sum('COAL_REMAIN') }}</strong>
                                </td>
                                <td class="text-center table-secondary">
                                    <strong>{{ $disCoals->sum('COAL_REMAIN_BYDAY') }}</strong>
                                </td>
                                <td class="text-center"><strong>{{ $disCoals->sum('COAL_REMAIN_BYWINTERDAY') }}</strong>
                                </td>

                                <td class="text-center"><strong>{{ $disCoals->sum('MAZUT_INCOME') }}</strong></td>
                                <td class="text-center"><strong>{{ $disCoals->sum('MAZUT_OUTCOME') }}</strong></td>
                                <td class="text-center table-secondary">
                                    <strong>{{ $disCoals->sum('MAZUT_REMAIN') }}</strong>
                                </td>

                                <td class="text-center"><strong>{{ $disCoals->sum('BAGANUUR_MINING_COAL_D') }}</strong>
                                </td>
                                <td class="text-center"><strong>{{ $disCoals->sum('SHARINGOL_MINING_COAL_D') }}</strong>
                                </td>
                                <td class="text-center"><strong>{{ $disCoals->sum('SHIVEEOVOO_MINING_COAL') }}</strong>
                                </td>
                                <td class="text-center"><strong>{{ $disCoals->sum('OTHER_MINIG_COAL_SUPPLY') }}</strong>
                                </td>

                                <td></td>
                            </tr>
                        </tfoot>

                    </table>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
