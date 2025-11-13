@extends('layouts.admin')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Тасралтын мэдээ бүртгэх</h2>
                        <div class="text-muted mt-1">ТЗЭ-ээс ирүүлсэн тасралтын мэдээллийг бүртгэнэ</div>
                    </div>
                </div>
            </div>

            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('tnews.store') }}">
                        @csrf
                        <div class="row g-3">
                            <!-- Огноо -->
                            <div class="col-md-4">
                                <label class="form-label required">Огноо</label>
                                <input type="date" name="date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <!-- Цаг -->
                            <div class="col-md-4">
                                <label class="form-label required">Цаг</label>
                                <input type="time" name="time" class="form-control" required>
                            </div>

                            <!-- ТЗЭ -->
                            <div class="col-md-4">
                                <label class="form-label required">ТЗЭ</label>
                                <input type="text" name="TZE" class="form-control" placeholder="Жишээ: УБЦТС ТӨХК"
                                    required>
                            </div>

                            <!-- Тасралтын мэдээлэл -->
                            <div class="col-12">
                                <label class="form-label required">Тасралтын мэдээлэл</label>
                                <textarea name="tasralt" rows="3" class="form-control"
                                    placeholder="Тасралтын шалтгаан, газар, цаг зэргийг бичнэ..." required></textarea>
                            </div>

                            <!-- Тайлбар -->
                            <div class="col-12">
                                <label class="form-label">Тайлбар / Арга хэмжээ</label>
                                <textarea name="ArgaHemjee" rows="2" class="form-control" placeholder="Хийгдсэн арга хэмжээ, нэмэлт тайлбар"></textarea>
                            </div>

                            <!-- Хязгаарлагдсан эрчим хүч -->
                            <div class="col-12">
                                <label class="form-label">Хязгаарлагдсан эрчим хүч</label>
                                <input type="text" name="HyzErchim" class="form-control" placeholder="Жишээ: 2500 кВт·ц">
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="send_telegram" id="send_telegram" class="form-check-input"
                                    value="1">
                                <label class="form-check-label" for="send_telegram">Телеграм илгээх</label>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('tnews.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left"></i> Буцах
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i> Хадгалах
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
