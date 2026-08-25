<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - CRM Disty Akademi</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">

    <div class="flex min-h-screen items-center justify-center px-4">

        <div class="w-full max-w-md">

            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    Buat Akun
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    CRM Disty Akademi
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
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
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                            Nama
                        </label>

                        <input id="name" type="text" name="name" value="{{ old('name') }}" required
                            autofocus autocomplete="name"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
                            placeholder="Nama lengkap">
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
                            Email
                        </label>

                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
                            placeholder="nama@distyakademi.com">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
                            Password
                        </label>

                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
                            placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">
                            Konfirmasi Password
                        </label>

                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
                            placeholder="Ulangi password">
                    </div>

                    <button type="submit"
                        class="w-full rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Buat Akun
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-slate-500">
                    Sudah memiliki akun?

                    <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">
                        Login
                    </a>
                </div>

            </div>

        </div>

    </div>

</body>

</html>
