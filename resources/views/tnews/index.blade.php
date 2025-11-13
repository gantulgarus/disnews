@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none mb-3">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">⚡ Тасралтын мэдээ</h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('tnews.create') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Мэдээ оруулах
                    </a>
                </div>
            </div>
        </div>

        {{-- Амжилтын мэдэгдэл --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ti ti-check me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Тасралтын мэдээний жагсаалт</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead class="text-muted">
                        <tr>
                            <th>#</th>
                            <th>Огноо</th>
                            <th>Цаг</th>
                            <th>ТЗЭ</th>
                            <th>Тасралт</th>
                            <th>Тайлбар</th>
                            <th>Дутуу түгээсэн ЦЭХ (кВт)</th>
                            <th>Телеграм</th>
                            <th class="text-center">Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($Tnews as $index => $news)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($news->date)->format('Y.m.d') }}</td>
                                <td>{{ $news->time }}</td>
                                <td>{{ $news->TZE }}</td>
                                <td>{{ Str::limit($news->tasralt, 60) }}</td>
                                <td class="text-muted">{{ $news->ArgaHemjee ?: '—' }}</td>
                                <td>{{ $news->HyzErchim ?: '—' }}</td>

                                <td>
                                    @if ($news->send_telegram)
                                        <span class="badge bg-success text-white">Илгээгдсэн</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Илгээгдээгүй</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="btn-list justify-content-center">
                                        <a href="{{ route('tnews.edit', $news) }}"
                                            class="btn btn-sm btn-warning d-inline-flex align-items-center">
                                            <i class="ti ti-edit me-1"></i>
                                        </a>

                                        <form action="{{ route('tnews.destroy', $news) }}" method="POST"
                                            onsubmit="return confirm('Энэ мэдээг устгах уу?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-danger d-inline-flex align-items-center">
                                                <i class="ti ti-trash me-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ti ti-info-circle me-1"></i> Одоогоор бүртгэл байхгүй байна.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
