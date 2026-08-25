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
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:block">

            <div class="flex h-16 items-center border-b border-slate-200 px-6">
                <div>
                    <div class="text-lg font-bold tracking-tight text-slate-900">
                        Disty Akademi
                    </div>

                    <div class="text-xs text-slate-500">
                        CRM System
                    </div>
                </div>
            </div>

            <nav class="space-y-1 p-4">

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    Dashboard
                </a>

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    Customer
                </a>

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    Perusahaan
                </a>

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    Pelatihan
                </a>

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    Follow Up
                </a>

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    Email Marketing
                </a>

                <a
                    href="#"
                    class="flex items-center rounded-lg px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    Laporan
                </a>

            </nav>

        </aside>

        {{-- Main --}}
        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Header --}}
            <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-6">

                <div>
                    <h1 class="text-lg font-semibold">
                        @yield('page-heading', 'Dashboard')
                    </h1>

                    @hasSection('page-description')
                        <p class="text-sm text-slate-500">
                            @yield('page-description')
                        </p>
                    @endif
                </div>

                <div class="flex items-center gap-4">

                    @auth
                        <div class="text-right">
                            <div class="text-sm font-semibold text-slate-800">
                                {{ auth()->user()->name }}
                            </div>

                            <div class="text-xs text-slate-500">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                            >
                                Logout
                            </button>
                        </form>
                    @endauth

                </div>

            </header>

            {{-- Content --}}
            <main class="flex-1 p-6">

                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')

            </main>

        </div>

    </div>

</body>
</html>