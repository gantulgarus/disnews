<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>ДҮТ ТӨХХК - Хоногийн үйл ажиллагааны мэдээллийн систем</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- CSS files -->
    <link href="{{ asset('assets/dist/css/tabler.min.css?1692870487') }}" rel="stylesheet" />
    <link href="{{ asset('assets/dist/css/tabler-flags.min.css?1692870487') }}" rel="stylesheet" />
    <link href="{{ asset('assets/dist/css/tabler-payments.min.css?1692870487') }}" rel="stylesheet" />
    <link href="{{ asset('assets/dist/css/tabler-vendors.min.css?1692870487') }}" rel="stylesheet" />
    <link href="{{ asset('assets/dist/css/demo.min.css?1692870487') }}" rel="stylesheet" />
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    @yield('style')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <script src="{{ asset('assets/dist/js/demo-theme.min.js?1692870487') }}"></script>
    <div class="page">
        <!-- Sidebar -->
        @include('layouts.sidebar')
        <!-- Navbar -->
        @include('layouts.header')

        <div class="page-wrapper">
            <!-- Page header -->
            {{-- <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <!-- Page pre-title -->
                            <div class="page-pretitle">
                                Overview
                            </div>
                            <h2 class="page-title">
                                Combo layout
                            </h2>
                        </div>
                        <!-- Page title actions -->
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <span class="d-none d-sm-inline">
                                    <a href="#" class="btn">
                                        New view
                                    </a>
                                </span>
                                <a href="#" class="btn btn-primary d-none d-sm-inline-block"
                                    data-bs-toggle="modal" data-bs-target="#modal-report">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Create new report
                                </a>
                                <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                                    data-bs-target="#modal-report" aria-label="Create new report">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <!-- Page body -->
            <div class="page-body">
                @yield('content')
            </div>

            @include('layouts.footer')
        </div>
    </div>

    <!-- Libs JS -->
    <script src="{{ asset('assets/dist/libs/apexcharts/dist/apexcharts.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('assets/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('assets/dist/libs/jsvectormap/dist/maps/world.js?1692870487') }}" defer></script>
    <script src="{{ asset('assets/dist/libs/jsvectormap/dist/maps/world-merc.js?1692870487') }}" defer></script>
    <!-- Tabler Core -->
    <script src="{{ asset('assets/dist/js/tabler.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('assets/dist/js/demo.min.js?1692870487') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('scripts')
</body>

</html>
