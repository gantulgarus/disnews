<!doctype html>
<html lang="mn">

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

        @font-face {
            font-family: "Geist";
            src: url("https://assets.codepen.io/605876/GeistVF.ttf") format("truetype");
        }

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --size: 20px;
        }

        body {
            display: grid;
            place-items: center;
            min-height: 100vh;
            background: #ffffff;
            /* цайвар фон */
            overflow: hidden;
        }

        /* Conic-gradient background animation */
        .el {
            position: fixed;
            inset: 0;
            background: conic-gradient(from 180deg at 50% 70%,
                    #e0f7ff 0deg,
                    /* цайвар цэнхэр */
                    #60a5fa 72deg,
                    /* тод цэнхэр */
                    #2563eb 144deg,
                    /* гүн хөх */
                    #7c3aed 216deg,
                    /* нил ягаан */
                    #38bdf8 288deg,
                    /* sky blue */
                    #e0f7ff 1turn
                    /* буцаж цайвар цэнхэр */
                );
            width: 100%;
            height: 100%;
            mask:
                radial-gradient(circle at 50% 50%, white 2px, transparent 2.5px) 50% 50% / var(--size) var(--size),
                url("https://assets.codepen.io/605876/noise-mask.png") 256px 50% / 256px 256px;
            mask-composite: intersect;
            animation: flicker 20s infinite linear;
            opacity: 0.55;
            /* фон бага зэрэг бүдэг */
        }


        @keyframes flicker {
            to {
                mask-position: 50% 50%, 0 50%;
            }
        }

        .card {
            position: relative;
            z-index: 2;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            filter: url($turbulent-displace);
            border: 2.5px solid var(--electric-border-color);
        }

        h2 {
            font-family: "Geist", sans-serif;
        }
    </style>

</head>


<body class="d-flex flex-column">
    <div class="el"></div>

    <script src="{{ asset('assets/dist/js/demo-theme.min.js?1692870487') }}"></script>

    <div class="page page-center">
        <div class="container container-tight py-4">
            <!-- Logo Section -->
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('images/ndc-logo.png') }}" alt="Ndc" class="img-fluid"
                        style="height: 100px;">

                </a>
            </div>

            <!-- Title Section -->
            <div class="text-center mb-3">
                <h2 class="fw-semibold fs-4 text-muted">Шуурхай ажиллагааны мэдээллийн систем</h2>
            </div>

            <!-- Login Form -->
            <div class="card card-md">
                <div class="card-body">
                    <form action="{{ route('login') }}" method="POST" autocomplete="off" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Имэйл хаяг') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                autocomplete="off">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-2">
                            <label class="form-label">{{ __('Нууц үг') }}</label>
                            <input type="password" name="password" class="form-control" autocomplete="off">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">{{ __('Нэвтрэх') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('assets/dist/js/tabler.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('assets/dist/js/demo.min.js?1692870487') }}" defer></script>
</body>

</html>
