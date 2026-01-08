@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Шинэ захиалгын журнал үүсгэх</h3>

        <form action="{{ route('order-journals.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="organization_id" class="form-label">Байгууллага</label>
                <select class="form-select" name="organization_id" id="organization_id" required>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="mb-3">
                <label for="order_type" class="form-label">Захиалгын төрөл</label>
                <select name="order_type" id="order_type" class="form-select" required>
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
                    <label class="form-label">Эхлэх хугацаа</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        <input type="text" name="planned_start_date" class="form-control datetime"
                            placeholder="Огноо сонгох">
                    </div>
                </div>
                <div class="col">
                    <label class="form-label">Дуусах хугацаа</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        <input type="text" name="planned_end_date" class="form-control datetime"
                            placeholder="Огноо сонгох">
                    </div>
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

            <div class="mb-3">
                <label class="form-label">Бүртгэсэн диспетчер</label>
                <input type="text" name="tze_dis_name" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Хадгалах</button>
            <a href="{{ route('order-journals.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // Document бэлэн болтол хүлээх
        document.addEventListener('DOMContentLoaded', function() {
            // Огноо + цаг
            initFlatpickr(".datetime");
        });
    </script>
@endsection
