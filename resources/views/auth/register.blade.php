<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CRM Disty Akademi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-7 text-center">
                <div class="mx-auto flex h-20 items-center justify-center">
                    <img src="{{ asset('images/disty-akademi-logo.png') }}" alt="Disty Akademi" class="h-20 w-auto max-w-[220px] object-contain">
                </div>
                <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-900">Buat Akun CRM</h1>
                <p class="mt-1 text-sm text-slate-500">Daftarkan akun untuk mengakses CRM Disty Akademi.</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60 sm:p-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5"
                            placeholder="Nama lengkap">
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5"
                            placeholder="nama@distyakademi.com">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5"
                            placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5"
                            placeholder="Ulangi password">
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl bg-slate-900 px-4 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-900/10">
                        Buat Akun
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-100 pt-6 text-center text-sm text-slate-500">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-bold text-slate-900 hover:underline">Masuk ke CRM</a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">© {{ date('Y') }} Disty Akademi</p>
        </div>
    </div>
</body>
</html>
