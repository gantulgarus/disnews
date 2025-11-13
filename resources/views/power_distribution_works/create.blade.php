@extends('layouts.admin')

@section('content')
    <div class="container-xl mt-4">
        <div class="page-header mb-3">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Шинэ захиалгат ажил нэмэх</h2>
                    <div class="text-muted">Энд шинэ захиалгат ажлыг бүртгэнэ үү</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('power-distribution-works.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">

                        <!-- ТЗЭ -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="tze" class="form-control" id="tze" placeholder="ТЗЭ"
                                    required>
                                <label for="tze">ТЗЭ</label>
                            </div>
                        </div>

                        <!-- Засварын ажлын утга -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="repair_work" class="form-control" id="repair_work"
                                    placeholder="Засварын ажлын утга" required>
                                <label for="repair_work">Засварын ажлын утга</label>
                            </div>
                        </div>

                        <!-- Тайлбар -->
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="description" class="form-control" id="description" placeholder="Тайлбар" style="height:100px"></textarea>
                                <label for="description">Тайлбар</label>
                            </div>
                        </div>

                        <!-- Хязгаарласан эрчим хүч -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" name="restricted_energy" class="form-control"
                                    id="restricted_energy" placeholder="Хязгаарласан эрчим хүч">
                                <label for="restricted_energy">Хязгаарласан эрчим хүч</label>
                            </div>
                        </div>

                        <!-- Огноо -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" name="date" class="form-control" id="date" placeholder="Огноо"
                                    required value="{{ date('Y-m-d') }}">
                                <label for="date">Огноо</label>
                            </div>
                        </div>

                        <!-- Телеграм -->
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="send_telegram" name="send_telegram"
                                    value="1">
                                <label class="form-check-label" for="send_telegram">Телеграм илгээх</label>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4 d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-success d-flex align-items-center justify-content-center">
                            <i class="ti ti-plus me-1"></i> Нэмэх
                        </button>
                        <a href="{{ route('power-distribution-works.index') }}" class="btn btn-outline-secondary">
                            Буцах
                        </a>
                    </div>


                </form>
            </div>
        </div>
    </div>
@endsection
