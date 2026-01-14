@extends('layouts.admin')

@section('content')
    <div class="container">
        <h3>Захиалгын журнал засварлах</h3>

        <form action="{{ route('order-journals.update', $orderJournal->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Байгууллага --}}
            <div class="mb-3">
                <label class="form-label">Байгууллага</label>
                <select class="form-select" name="organization_id" required>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}"
                            {{ $orderJournal->organization_id == $org->id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Захиалгын төрөл --}}
            <div class="mb-3">
                <label class="form-label">Захиалгын төрөл</label>
                <select name="order_type" class="form-select" required>
                    <option value="Энгийн" {{ $orderJournal->order_type === 'Энгийн' ? 'selected' : '' }}>Энгийн</option>
                    <option value="Аваарын" {{ $orderJournal->order_type === 'Аваарын' ? 'selected' : '' }}>Аваарын</option>
                </select>
            </div>

            {{-- Ажлын агуулга --}}
            <div class="mb-3">
                <label class="form-label">Засварын ажлын агyулга</label>
                <textarea name="content" class="form-control" rows="3" required>{{ $orderJournal->content }}</textarea>
            </div>

            {{-- Төлөвлөсөн хугацаа --}}
            <div class="mb-3 row">
                <div class="col">
                    <label class="form-label">Эхлэх хугацаа</label>
                    <input type="datetime-local" name="planned_start_date" class="form-control"
                        value="{{ optional($orderJournal->planned_start_date)->format('Y-m-d\TH:i') }}">
                </div>
                <div class="col">
                    <label class="form-label">Дуусах хугацаа</label>
                    <input type="datetime-local" name="planned_end_date" class="form-control"
                        value="{{ optional($orderJournal->planned_end_date)->format('Y-m-d\TH:i') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Хэрэглэгч таслах эсэх</label>
                <select name="is_cut" class="form-select">
                    <option value="0" {{ $orderJournal->is_cut ? '' : 'selected' }}>Үгүй</option>
                    <option value="1" {{ $orderJournal->is_cut ? 'selected' : '' }}>Тийм</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Таслах шалтгаан</label>
                <textarea name="cut_description" class="form-control" rows="3">{{ $orderJournal->cut_description }}</textarea>
            </div>

            {{-- Баталсан --}}
            <div class="mb-3">
                <label class="form-label">Баталсан хүний нэр</label>
                <input type="text" name="approver_name" class="form-control" value="{{ $orderJournal->approver_name }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Баталсан хүний албан тушаал</label>
                <input type="text" name="approver_position" class="form-control"
                    value="{{ $orderJournal->approver_position }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Бүртгэсэн диспетчер</label>
                <input type="text" name="tze_dis_name" class="form-control" value="{{ $orderJournal->tze_dis_name }}">
            </div>

            {{-- Action --}}
            <button type="submit" class="btn btn-success">Засварлах</button>
            <a href="{{ route('order-journals.index') }}" class="btn btn-secondary">Буцах</a>
        </form>
    </div>
@endsection
