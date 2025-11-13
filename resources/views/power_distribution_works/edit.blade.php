@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none mb-3">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Захиалгат ажлыг засах</h2>
                    <div class="text-muted mt-1">Бүртгэлтэй ажлыг шинэчлэх</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('power-distribution-works.update', $powerDistributionWork->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label required">ТЗЭ</label>
                            <input type="text" name="tze" class="form-control"
                                value="{{ old('tze', $powerDistributionWork->tze) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Засварын ажлын утга</label>
                            <input type="text" name="repair_work" class="form-control"
                                value="{{ old('repair_work', $powerDistributionWork->repair_work) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Тайлбар</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $powerDistributionWork->description) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Хязгаарласан эрчим хүч</label>
                            <input type="number" step="0.01" name="restricted_energy" class="form-control"
                                value="{{ old('restricted_energy', $powerDistributionWork->restricted_energy) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Огноо</label>
                            <input type="date" name="date" class="form-control"
                                value="{{ old('date', $powerDistributionWork->date) }}" required>
                        </div>

                        <div class="col-12">
                            <div class="form-check mt-2">
                                <input type="checkbox" name="send_telegram" id="send_telegram" class="form-check-input"
                                    value="1">
                                <label class="form-check-label" for="send_telegram">Телеграм илгээх</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                            <i class="ti ti-device-floppy me-1"></i> Шинэчлэх
                        </button>
                        <a href="{{ route('power-distribution-works.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Буцах
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
