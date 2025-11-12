@extends('layouts.admin')

@section('content')
    <div class="page">
        <div class="container-xl">
            <div class="row my-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Мэдээний дэлгэрэнгүй</h3>
                            <div class="card-actions">
                                <form action="{{ route('telephone_messages.destroy', $telephoneMessage) }}" method="POST"
                                    class="d-inline-block" onsubmit="return confirm('Мэдээг устгах уу?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Устгах</button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Төлөв</dt>
                                <dd class="col-sm-9">{{ $telephoneMessage->status }}</dd>

                                <dt class="col-sm-3">Илгээсэн байгууллага</dt>
                                <dd class="col-sm-9">
                                    {{ optional($telephoneMessage->senderOrganization)->name ?? $telephoneMessage->sender_org_id }}
                                </dd>

                                <dt class="col-sm-3">Хүлээн авагч</dt>
                                <dd class="col-sm-9">
                                    @if (is_array($telephoneMessage->receiver_org_ids))
                                        @foreach ($telephoneMessage->receiver_org_ids as $rid)
                                            <span
                                                class="badge bg-light text-dark">{{ \App\Models\Organization::find($rid)->name ?? $rid }}</span>
                                        @endforeach
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Агуулга</dt>
                                <dd class="col-sm-9">{{ $telephoneMessage->content }}</dd>

                                <dt class="col-sm-3">Хавсралт</dt>
                                <dd class="col-sm-9">
                                    @if ($telephoneMessage->attachment)
                                        @php
                                            $ext = pathinfo($telephoneMessage->attachment, PATHINFO_EXTENSION);
                                        @endphp

                                        <a href="{{ asset('storage/' . $telephoneMessage->attachment) }}" target="_blank"
                                            class="link mb-2 d-block">
                                            Файл харах / татах
                                        </a>

                                        @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                            <!-- Зураг бол шууд үзүүлэх -->
                                            <img src="{{ asset('storage/' . $telephoneMessage->attachment) }}"
                                                alt="Attachment" class="img-fluid rounded mt-2" style="max-height:300px;">
                                        @elseif(strtolower($ext) === 'pdf')
                                            <!-- PDF бол iframe-д үзүүлэх -->
                                            <iframe src="{{ asset('storage/' . $telephoneMessage->attachment) }}"
                                                style="width:100%; height:500px; border:1px solid #ccc;"
                                                class="mt-2"></iframe>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Огноо</dt>
                                <dd class="col-sm-9">{{ $telephoneMessage->created_at->format('Y-m-d H:i') }}</dd>
                            </dl>
                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ route('telephone_messages.index') }}" class="btn btn-link">Буцах</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
