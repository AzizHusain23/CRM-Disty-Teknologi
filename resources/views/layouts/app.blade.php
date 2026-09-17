<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @hasSection('title')
            @yield('title') - CRM Disty Akademi
        @else
            CRM Disty Akademi
        @endif
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sidebar remains fixed and non-scrollable; compact vertical density at short viewports / browser zoom. */
        .crm-sidebar {
            --sidebar-brand-h: 82px;
            --sidebar-nav-py: 12px;
            --sidebar-group-gap: 12px;
            --sidebar-item-py: 8px;
            --sidebar-icon: 28px;
            --sidebar-user-pad: 12px;
        }

        @media (max-height: 900px) {
            .crm-sidebar {
                --sidebar-brand-h: 72px;
                --sidebar-nav-py: 8px;
                --sidebar-group-gap: 8px;
                --sidebar-item-py: 6px;
                --sidebar-icon: 26px;
                --sidebar-user-pad: 10px;
            }
            .crm-sidebar-brand { height: var(--sidebar-brand-h) !important; padding-left: 20px !important; padding-right: 20px !important; }
            .crm-sidebar-brand img { height: 42px !important; }
            .crm-sidebar-nav { padding-top: var(--sidebar-nav-py) !important; padding-bottom: var(--sidebar-nav-py) !important; }
            .crm-sidebar-group { margin-bottom: var(--sidebar-group-gap) !important; }
            .crm-sidebar-item { padding-top: var(--sidebar-item-py) !important; padding-bottom: var(--sidebar-item-py) !important; }
            .crm-sidebar-icon { width: var(--sidebar-icon) !important; height: var(--sidebar-icon) !important; }
            .crm-sidebar-user { padding: var(--sidebar-user-pad) !important; }
            .crm-sidebar-user-card { padding: 10px !important; border-radius: 14px !important; }
            .crm-sidebar-user-row { gap: 10px !important; }
            .crm-sidebar-user-row > div:first-child { width: 34px !important; height: 34px !important; border-radius: 10px !important; }
        }

        @media (max-height: 760px) {
            .crm-sidebar {
                --sidebar-brand-h: 62px;
                --sidebar-nav-py: 6px;
                --sidebar-group-gap: 5px;
                --sidebar-item-py: 4px;
                --sidebar-icon: 23px;
                --sidebar-user-pad: 7px;
            }
            .crm-sidebar-brand { padding-left: 16px !important; padding-right: 16px !important; }
            .crm-sidebar-brand img { height: 34px !important; max-width: 145px !important; }
            .crm-sidebar-brand .hidden.min-w-0 { display: none !important; }
            .crm-sidebar-group-label { margin-bottom: 2px !important; font-size: 8px !important; }
            .crm-sidebar-item { gap: 8px !important; font-size: 12px !important; border-radius: 10px !important; }
            .crm-sidebar-icon { border-radius: 7px !important; }
            .crm-sidebar-user-card { padding: 7px !important; }
            .crm-sidebar-user-row { gap: 8px !important; }
            .crm-sidebar-user-row > div:first-child { width: 30px !important; height: 30px !important; border-radius: 9px !important; font-size: 12px !important; }
            .crm-sidebar-user-row .text-sm { font-size: 12px !important; }
            .crm-sidebar-user-row .text-xs { font-size: 10px !important; }
        }

        @media (max-height: 640px) {
            .crm-sidebar-brand { height: 56px !important; }
            .crm-sidebar-brand img { height: 30px !important; }
            .crm-sidebar-group-label { display: none !important; }
            .crm-sidebar-group { margin-bottom: 3px !important; }
            .crm-sidebar-item { padding-top: 3px !important; padding-bottom: 3px !important; }
            .crm-sidebar-user-card > div { min-height: 0 !important; }
            .crm-sidebar-user-row > div:nth-child(2) { display: none !important; }
        }
    </style>

</head>

@php
    $navGroups = [
        [
            'label' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'grid'],
            ],
        ],
        [
            'label' => 'Data Utama',
            'items' => [
                ['label' => 'Customer', 'route' => 'customers.index', 'icon' => 'users'],
                ['label' => 'Instansi', 'route' => 'institutions.index', 'icon' => 'building'],
                ['label' => 'Import Customer', 'route' => 'customer-imports.index', 'icon' => 'upload'],
            ],
        ],
        [
            'label' => 'Pelatihan',
            'items' => [
                ['label' => 'Pelatihan', 'route' => 'trainings.index', 'icon' => 'book'],
                ['label' => 'Jadwal Pelatihan', 'route' => 'training-schedules.index', 'icon' => 'calendar'],
                ['label' => 'Trainer', 'route' => 'trainers.index', 'icon' => 'chalkboard'],
                ['label' => 'Pendaftaran Training', 'route' => 'registrations.index', 'icon' => 'clipboard'],
            ],
        ],
        [
            'label' => 'Aktivitas',
            'items' => [
                ['label' => 'Aktivitas', 'route' => 'activities.index', 'icon' => 'activity'],
                ['label' => 'Follow Up', 'route' => 'follow-ups.index', 'icon' => 'clock'],
                ['label' => 'Laporan', 'route' => 'reports.index', 'icon' => 'chart'],
            ],
        ],
    ];
@endphp

<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">

    <div class="min-h-screen">

        {{-- Sidebar desktop --}}
        <aside class="crm-sidebar fixed inset-y-0 left-0 z-40 hidden h-screen w-72 flex-col overflow-hidden border-r border-slate-200 bg-white lg:flex">
            <div class="crm-sidebar-brand flex h-[82px] shrink-0 items-center border-b border-slate-200 px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/disty-akademi-logo.png') }}" alt="Disty Akademi" class="h-12 w-auto max-w-[180px] object-contain">
                    <div class="hidden min-w-0 xl:block">
                        <div class="truncate text-sm font-bold tracking-tight text-slate-900">CRM Management</div>
                        <div class="text-xs font-medium text-slate-500">Disty Akademi</div>
                    </div>
                </a>
            </div>

            <nav class="crm-sidebar-nav min-h-0 flex-1 px-3 py-3">
                @foreach ($navGroups as $group)
                    <div class="crm-sidebar-group mb-3 last:mb-0">
                        <p class="crm-sidebar-group-label mb-1.5 px-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            {{ $group['label'] }}
                        </p>

                        <div class="crm-sidebar-items space-y-0.5">
                            @foreach ($group['items'] as $item)
                                @php
                                    $active = request()->routeIs($item['route']);
                                @endphp

                                <a href="{{ route($item['route']) }}"
                                    class="crm-sidebar-item group flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold transition {{ $active ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <span class="crm-sidebar-icon flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $active ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500 group-hover:text-slate-800' }}">
                                        @include('components.nav-icon', ['icon' => $item['icon']])
                                    </span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            <div class="crm-sidebar-user shrink-0 border-t border-slate-200 p-3">
                <div class="crm-sidebar-user-card rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-200">
                    <div class="crm-sidebar-user-row flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="truncate text-xs text-slate-500">{{ auth()->user()->email ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex min-h-screen min-w-0 flex-col lg:pl-72">

            {{-- Top header --}}
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
                <div class="flex min-h-[82px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="shrink-0 lg:hidden">
                            <img src="{{ asset('images/disty-akademi-logo.png') }}" alt="Disty Akademi" class="h-10 w-auto max-w-[130px] object-contain">
                        </a>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="hidden text-xs font-semibold uppercase tracking-[0.12em] text-slate-400 sm:inline">Disty Akademi</span>
                                <span class="hidden text-slate-300 sm:inline">/</span>
                                <h1 class="truncate text-base font-bold text-slate-900 sm:text-lg">
                                    @yield('page-heading', 'Dashboard')
                                </h1>
                            </div>

                            @hasSection('page-description')
                                <p class="mt-0.5 hidden max-w-2xl truncate text-sm text-slate-500 sm:block">
                                    @yield('page-description')
                                </p>
                            @endif
                        </div>
                    </div>

                    @auth
                        <div class="flex shrink-0 items-center gap-3">
                            <div class="hidden text-right md:block">
                                <div class="text-sm font-semibold text-slate-800">
                                    {{ auth()->user()->name }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-bold text-white shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="mx-auto w-full max-w-[1600px]">
                    @if (session('success'))
                        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
                            <div class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></div>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                            <div class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-red-500"></div>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>

    </div>

</body>

</html>
