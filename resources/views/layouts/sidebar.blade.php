<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand w-100 text-center mb-0">
            <a href="/"
                class="d-inline-flex align-items-center justify-content-center w-100 gap-2 text-decoration-none">
                <img src="{{ asset('images/ndc.min.svg') }}" alt="NDC Logo" class="navbar-brand-image"
                    style="height: 60px; width: auto; object-fit: contain;">
                <span class="fw-bold text-white fs-4">ДҮТ ТӨХХК</span>
            </a>
        </h1>


        @php
            $orgId = auth()->user()->organization_id;
        @endphp


        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="/">
                        <span
                            class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-layout-dashboard">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                                <path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                                <path
                                    d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                                <path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Хянах самбар
                        </span>
                    </a>
                </li>

                @php
                    $operationRoutes = [
                        'daily-equipment-report.index',
                        'daily-balance-journals.index',
                        'daily-balance-journals.report',
                        'order-journals.index',
                        'tnews.index',
                        'reports.dailyReport',
                        'reports.dailyPowerEquipment',
                        'reports.localDailyReport',
                        'power-distribution-works.index',
                        'telephone_messages.index',
                        'daily_power_hour_reports.index',
                        'daily_power_hour_reports.report',
                        'sms.index',
                    ];
                    $isOperationActive = request()->routeIs(...$operationRoutes);
                @endphp



                <li class="nav-item dropdown {{ $isOperationActive ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span
                            class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/package -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                                <path d="M12 12l8 -4.5" />
                                <path d="M12 12l0 9" />
                                <path d="M12 12l-8 -4.5" />
                                <path d="M16 5.25l-8 4.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Шуурхай ажиллагаа</span>
                    </a>

                    <div class="dropdown-menu {{ $isOperationActive ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if ($orgId == 5)
                                    <a class="dropdown-item {{ request()->routeIs('reports.dailyReport') ? 'active' : '' }}"
                                        href="{{ route('reports.dailyReport') }}">
                                        Хоногийн мэдээ
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('reports.localDailyReport') ? 'active' : '' }}"
                                        href="{{ route('reports.localDailyReport') }}">
                                        Орон нутаг мэдээ
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('tnews.index') ? 'active' : '' }}"
                                        href="{{ route('tnews.index') }}">
                                        Тасралтын мэдээ
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('power-distribution-works.index') ? 'active' : '' }}"
                                        href="{{ route('power-distribution-works.index') }}">
                                        Захиалгат ажил
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('sms.index') ? 'active' : '' }}"
                                        href="{{ route('sms.index') }}">
                                        СМС илгээх
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('daily-balance-journals.report') ? 'active' : '' }}"
                                        href="{{ route('daily-balance-journals.report') }}">
                                        Тооцооны журнал
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('daily_power_hour_reports.report') ? 'active' : '' }}"
                                        href="{{ route('daily_power_hour_reports.report') }}">
                                        Ачааллын график
                                    </a>
                                @endif
                                <a class="dropdown-item {{ request()->routeIs('order-journals.index') ? 'active' : '' }}"
                                    href="{{ route('order-journals.index') }}">
                                    Захиалгын журнал
                                </a>
                                @if ($orgId != 5)
                                    <a class="dropdown-item {{ request()->routeIs('daily-balance-journals.index') ? 'active' : '' }}"
                                        href="{{ route('daily-balance-journals.index') }}">
                                        Тооцооны журнал
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('daily_power_hour_reports.index') ? 'active' : '' }}"
                                        href="{{ route('daily_power_hour_reports.index') }}">
                                        Ачааллын график
                                    </a>
                                @endif

                                <a class="dropdown-item {{ request()->routeIs('telephone_messages.index') ? 'active' : '' }}"
                                    href="{{ route('telephone_messages.index') }}">
                                    Телефон мэдээ
                                </a>

                                <a class="dropdown-item {{ request()->routeIs('dis_coal.index') ? 'active' : '' }}"
                                    href="{{ route('dis_coal.index') }}">
                                    Түлшний мэдээ
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

                @php
                    $regimeRoutes = [
                        'electric_daily_regimes.index',
                        'electric_daily_regimes.report',
                        'electric_daily_regimes',
                        'thermo-daily-regimes.index',
                        'thermo-daily-regimes.report',
                        'station_thermo.news',
                        'reports.powerPlantReport',
                    ];
                    $isRegimeActive = request()->routeIs(...$regimeRoutes);
                @endphp


                <li class="nav-item dropdown {{ $isRegimeActive ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-mobiledata">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M16 12v-8" />
                                <path d="M8 20v-8" />
                                <path d="M13 7l3 -3l3 3" />
                                <path d="M5 17l3 3l3 -3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Горим төлөвлөлт</span>
                    </a>

                    <div class="dropdown-menu {{ $isRegimeActive ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if ($orgId == 5)
                                    <a class="dropdown-item {{ request()->routeIs('electric_daily_regimes.report') ? 'active' : '' }}"
                                        href="{{ route('electric_daily_regimes.report') }}">
                                        Цахилгааны горим
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('station_thermo.report') ? 'active' : '' }}"
                                        href="{{ route('thermo-daily-regimes.report') }}">
                                        Дулааны горим
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('station_thermo.news') ? 'active' : '' }}"
                                        href="{{ route('station_thermo.news') }}">
                                        Дулааны мэдээ
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        Импорт, Экспорт
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('reports.powerPlantReport') ? 'active' : '' }}"
                                        href="{{ route('reports.powerPlantReport') }}">
                                        СЭХ-ний горим, гүйцэтгэл
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        ДЦС-ын горим, гүйцэтгэл
                                    </a>
                                @endif
                                @if ($orgId != 5)
                                    <a class="dropdown-item {{ request()->routeIs('electric_daily_regimes.index') ? 'active' : '' }}"
                                        href="{{ route('electric_daily_regimes.index') }}">
                                        Цахилгааны горим
                                    </a>

                                    <a class="dropdown-item {{ request()->routeIs('thermo-daily-regimes.index') ? 'active' : '' }}"
                                        href="{{ route('thermo-daily-regimes.index') }}">
                                        Дулааны горим
                                    </a>
                                @endif


                            </div>
                        </div>
                    </div>
                </li>

                @php
                    $reportRoutes = ['reports.index'];
                    $isReportsActive = request()->routeIs(...$reportRoutes);
                @endphp

                @if ($orgId == 5)
                    <li class="nav-item dropdown {{ $isReportsActive ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-analytics">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M9 17l0 -5" />
                                    <path d="M12 17l0 -1" />
                                    <path d="M15 17l0 -3" />
                                </svg>
                            </span>
                            <span class="nav-link-title">Тайлан</span>
                        </a>

                        <div class="dropdown-menu {{ $isReportsActive ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item {{ request()->routeIs('reports.index') ? 'active' : '' }}"
                                        href="{{ route('reports.index') }}">
                                        Тайлан
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                @php
                    $settingRoutes = [
                        'power-plants.index',
                        'equipments.index',
                        'organizations.index',
                        'divisions.index',
                        'permission_levels.index',
                        'users.index',
                    ];
                    $isSettingsActive = request()->routeIs(...$settingRoutes);
                @endphp

                @if ($orgId == 5)
                    <li class="nav-item dropdown {{ $isSettingsActive ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-settings-bolt">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M13.256 20.473c-.855 .907 -2.583 .643 -2.931 -.79a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.07 .26 1.488 1.29 1.254 2.15" />
                                    <path d="M19 16l-2 3h4l-2 3" />
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">Тохиргоо</span>
                        </a>

                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item {{ request()->routeIs('power-plants.index') ? 'active' : '' }}"
                                        href="{{ route('power-plants.index') }}">
                                        Эх үүсвэр
                                    </a>
                                    <a class="dropdown-item {{ request()->routeIs('equipments.index') ? 'active' : '' }}"
                                        href="{{ route('equipments.index') }}">
                                        Тоноглол
                                    </a>

                                    <a class="dropdown-item {{ request()->routeIs('daily_power_equipments.index') ? 'active' : '' }}"
                                        href="{{ route('daily_power_equipments.index') }}">
                                        Ачааллын тоноглол
                                    </a>


                                    <a class="dropdown-item {{ request()->routeIs('organizations.index') ? 'active' : '' }}"
                                        href="{{ route('organizations.index') }}">
                                        Байгууллага
                                    </a>

                                    <a class="dropdown-item {{ request()->routeIs('divisions.index') ? 'active' : '' }}"
                                        href="{{ route('divisions.index') }}">
                                        Албан тушаал
                                    </a>

                                    <a class="dropdown-item {{ request()->routeIs('permission_levels.index') ? 'active' : '' }}"
                                        href="{{ route('permission_levels.index') }}">
                                        Эрхийн түвшин
                                    </a>

                                    <a class="dropdown-item {{ request()->routeIs('users.index') ? 'active' : '' }}"
                                        href="{{ route('users.index') }}">
                                        Хэрэглэгч
                                    </a>

                                </div>
                            </div>
                        </div>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</aside>
