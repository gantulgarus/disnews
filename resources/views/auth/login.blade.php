<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta20
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Sign in - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
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
            background-size: 200px;
            background-repeat: repeat;
            background-image: url("{{ asset('images/ ') }}");
        }
    </style>
</head>

<body class=" d-flex flex-column">
    <script src="{{ asset('assets/dist/js/demo-theme.min.js?1692870487') }}"></script>
    <div class="page page-center">
        <div class="container container-tight py-4">


            <!-- Logo Section -->
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('images/logoMN.png') }}" alt="Ndc" class="img-fluid" style="height: 100px;">
                </a>
            </div>
            <!-- Title Section -->
            <div class="text-center mb-3">
                <h2 class="fw-semibold fs-4 text-muted">Хоногийн үйл ажиллагааны мэдээллийн систем</h2>
            </div>

            <div class="card card-md">
                <div class="card-body">
                    <form action="{{ route('login') }}" method="POST" autocomplete="off" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Имэйл хаяг') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                placeholder="your@email.com" autocomplete="off">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                {{ __('Нууц үг') }}
                            </label>
                            <div class="input-group input-group-flat">
                                <input type="password" name="password" class="form-control" placeholder="Your password"
                                    autocomplete="off">

                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password"
                                        data-bs-toggle="tooltip"><!-- Download SVG icon from http://tabler-icons.io/i/eye -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        {{-- <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" />
                                <span class="form-check-label">{{ __('Remember me') }}</span>
                            </label>
                        </div> --}}
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">{{ __('Нэвтрэх') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- <div class="text-center text-secondary mt-3">
                Don't have account yet? <a href="./sign-up.html" tabindex="-1">Sign up</a>
            </div> --}}
        </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="{{ asset('assets/dist/js/tabler.min.js?1692870487') }}" defer></script>
    <script src="{{ asset('assets/dist/js/demo.min.js?1692870487') }}" defer></script>
</body>

</html>
