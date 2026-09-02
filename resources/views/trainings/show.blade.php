@extends('layouts.app')

@section('title', $training->name)

@section('page-heading', $training->name)

@section('page-description')
    Detail program dan histori peserta.
@endsection

@section('content')

    <div class="space-y-6">

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif


        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif


        {{-- HEADER --}}

        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>

                <div class="flex flex-wrap items-center gap-3">

                    <h2 class="text-xl font-semibold text-slate-900">
                        {{ $training->name }}
                    </h2>

                    @if ($training->is_active)
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Aktif
                        </span>
                    @else
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            Nonaktif
                        </span>
                    @endif

                </div>

                <p class="mt-1 text-sm text-slate-500">

                    {{ $training->category?->name ?: 'Tanpa kategori' }}

                </p>

            </div>


            <div class="flex flex-wrap gap-2">

                @if ($training->is_active)
                    <a href="{{ route('registrations.create', ['training_id' => $training->id]) }}"
                        class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                        + Daftarkan Customer
                    </a>
                @endif

                <a href="{{ route('trainings.edit', $training) }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Edit
                </a>

                <a href="{{ route('trainings.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>

            </div>

        </div>


        {{-- INFORMASI --}}

        <div class="grid gap-6 lg:grid-cols-3">


            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">

                <h3 class="text-base font-semibold text-slate-900">
                    Informasi Pelatihan
                </h3>


                <dl class="mt-6 space-y-5">

                    <div>

                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Kategori
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $training->category?->name ?: '-' }}
                        </dd>

                    </div>


                    <div>

                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Harga
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-800">

                            @if ($training->price !== null)
                                Rp {{ number_format((float) $training->price, 0, ',', '.') }}
                            @else
                                Gratis / belum ditentukan
                            @endif

                        </dd>

                    </div>


                    <div>

                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Durasi
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">

                            @if ($training->duration_hours)
                                {{ number_format($training->duration_hours) }}
                                jam
                            @else
                                -
                            @endif

                        </dd>

                    </div>


                    <div>

                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Total Registrasi
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-800">
                            {{ number_format($training->registrations->count()) }}
                        </dd>

                    </div>


                    <div>

                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Deskripsi
                        </dt>

                        <dd class="mt-1 text-sm leading-6 text-slate-700">
                            {{ $training->description ?: '-' }}
                        </dd>

                    </div>

                </dl>

            </div>


            {{-- PESERTA --}}

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

                <div class="border-b border-slate-200 px-6 py-5">

                    <h3 class="text-base font-semibold text-slate-900">
                        Peserta / Registration
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Histori customer yang terdaftar pada pelatihan ini.
                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">

                            <tr>

                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Customer
                                </th>

                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Instansi
                                </th>

                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Tanggal
                                </th>

                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-200">

                            @forelse ($training->registrations as $registration)
                                <tr class="hover:bg-slate-50">

                                    <td class="px-6 py-5">

                                        @if ($registration->customer)
                                            <a href="{{ route('customers.show', $registration->customer) }}"
                                                class="font-semibold text-slate-900 hover:underline">
                                                {{ $registration->customer->name }}
                                            </a>

                                            <div class="mt-1 text-xs text-slate-400">
                                                {{ $registration->customer->customer_code }}
                                            </div>
                                        @else
                                            -
                                        @endif

                                    </td>


                                    <td class="px-6 py-5 text-sm text-slate-700">

                                        {{ $registration->customer?->institution?->name ?: '-' }}

                                    </td>


                                    <td class="px-6 py-5 text-sm text-slate-700">

                                        {{ $registration->training_date?->format('d M Y') ?: '-' }}

                                    </td>


                                    <td class="px-6 py-5">

                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ ucfirst($registration->status) }}
                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                        Belum ada peserta pada pelatihan ini.

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection
