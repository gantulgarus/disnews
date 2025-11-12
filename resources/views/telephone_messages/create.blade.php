@extends('layouts.admin')

@section('content')
    <div class="page">
        <div class="container-xl">
            <div class="row my-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Шинэ телефон мэдээ илгээх</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('telephone_messages.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Хүлээн авагч байгууллагууд</label>
                                    <select name="receiver_org_ids[]" class="form-select" multiple>
                                        @foreach ($organizations as $org)
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint">Ctrl (Cmd) дарж олон байгууллага сонгоно.</div>
                                    @error('receiver_org_ids')
                                        <div class="form-feedback mt-1 text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Мэдээний агуулга</label>
                                    <textarea name="content" rows="6" class="form-control" required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="form-feedback mt-1 text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Хавсралт (PDF / JPG / PNG)</label>
                                    <input type="file" name="attachment" class="form-control" />
                                    @error('attachment')
                                        <div class="form-feedback mt-1 text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('telephone_messages.index') }}" class="btn btn-link me-2">Буцах</a>
                                    <button class="btn btn-primary">Илгээх</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
