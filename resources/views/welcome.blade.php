@extends('layouts.admin')

@section('content')
    <div class="container-xl">
        <div class="card">
            <div class="card-body text-center py-6">
                <h2 class="mb-3">Тавтай морилно уу 👋</h2>
                <p class="text-muted mb-4">
                    Та системд амжилттай нэвтэрлээ.<br>
                    Танд хамаарах цэсийг зүүн талаас сонгоно уу.
                </p>

                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icon-tabler-hand-wave mx-auto text-muted">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 4v10" />
                    <path d="M16 5v9" />
                    <path d="M8 6v8" />
                    <path d="M20 8v5" />
                </svg>
            </div>
        </div>
    </div>
@endsection
