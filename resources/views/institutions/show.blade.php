@extends('layouts.app')

@section('title', $institution->name)

@section('page-heading', $institution->name)

@section('page-description')
    Detail instansi dan customer terkait.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    {{ $institution->name }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $institution->customers->count() }} customer
                </p>
            </div>

            <div class="flex gap-2">

                <a href="{{ route('institutions.edit', $institution) }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Edit
                </a>

                <form method="POST" action="{{ route('institutions.destroy', $institution) }}"
                    onsubmit="return confirm('Yakin ingin menghapus instansi ini?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">
                        Hapus
                    </button>
                </form>

                <a href="{{ route('institutions.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>

            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <h3 class="text-base font-semibold text-slate-900">
                    Informasi Instansi
                </h3>

                <dl class="mt-6 space-y-4">

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Jenis
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            @switch($institution->type)
                                @case('government')
                                    Pemerintah
                                @break

                                @case('school')
                                    Sekolah
                                @break

                                @case('university')
                                    Perguruan Tinggi
                                @break

                                @case('company')
                                    Perusahaan
                                @break

                                @case('foundation')
                                    Yayasan
                                @break

                                @case('institution')
                                    Lembaga
                                @break

                                @default
                                    Lainnya
                            @endswitch
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Email
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $institution->email ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Telepon
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $institution->phone ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Lokasi
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $institution->city ?: '-' }}

                            @if ($institution->province)
                                , {{ $institution->province }}
                            @endif
                        </dd>
                    </div>

                </dl>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

                <div class="border-b border-slate-200 px-6 py-5">

                    <h3 class="text-base font-semibold text-slate-900">
                        Customer
                    </h3>

                </div>

                <div class="divide-y divide-slate-200">

                    @forelse ($institution->customers as $customer)
                        <div class="flex items-center justify-between gap-4 px-6 py-5">

                            <div>

                                <div class="font-semibold text-slate-900">
                                    {{ $customer->name }}
                                </div>

                                <div class="mt-1 text-sm text-slate-500">
                                    {{ $customer->email ?: 'Email tidak tersedia' }}
                                </div>

                            </div>

                            <a href="{{ route('customers.show', $customer) }}"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Detail
                            </a>

                        </div>

                    @empty

                        <div class="px-6 py-12 text-center text-sm text-slate-500">
                            Belum ada customer pada instansi ini.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

    </div>

@endsection
