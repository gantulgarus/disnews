@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Шинэ захиалгын журнал үүсгэх</h3>

        <form action="{{ route('order-journals.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Дугаар</label>
                <input type="text" name="order_number" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Төлөв</label>
                <select name="status" class="form-select" required>
                    @foreach (\App\Models\OrderJournal::$STATUS_NAMES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="organization_id" class="form-label">Байгууллага</label>
                <select class="form-select" name="organization_id" id="organization_id" required>
                    <option value="">Сонгоно уу</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="mb-3">
                <label for="order_type" class="form-label">Захиалгын төрөл</label>
                <select name="order_type" id="order_type" class="form-select" required>
                    <option value="">Сонгох...</option>
                    <option value="Энгийн">Энгийн</option>
                    <option value="Аваарын">Аваарын</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Засварын ажлын агyулга</label>
                <textarea name="content" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3 row">
                <div class="col">
                    <label class="form-label">Төлөвлөсөн эхлэх</label>
                    <input type="datetime-local" name="planned_start_date" class="form-control">
                </div>
                <div class="col">
                    <label class="form-label">Төлөвлөсөн дуусах</label>
                    <input type="datetime-local" name="planned_end_date" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Баталсан хүний нэр</label>
                <input type="text" name="approver_name" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Баталсан хүний албан тушаал</label>
                <input type="text" name="approver_position" class="form-control">
            </div>

            <div class="mb-3 row">
                <div class="col">
                    <label class="form-label">Бодит эхлэх огноо</label>
                    <input type="datetime-local" name="real_start_date" class="form-control">
                </div>
                <div class="col">
                    <label class="form-label">Бодит дуусах огноо</label>
                    <input type="datetime-local" name="real_end_date" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Хадгалах</button>
            <a href="{{ route('order-journals.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
