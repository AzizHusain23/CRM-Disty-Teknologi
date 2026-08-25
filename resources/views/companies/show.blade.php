@extends('layouts.app')

@section('title', $company->name)

@section('page-heading', $company->name)

@section('page-description')
    Detail perusahaan dan customer terkait.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Informasi Perusahaan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $company->customers->count() }} customer
                </p>
            </div>

            <div class="flex gap-2">

                <a href="{{ route('companies.edit', $company) }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Edit
                </a>

                <a href="{{ route('companies.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>

            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            <div class="lg:col-span-1 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <h3 class="text-base font-semibold text-slate-900">
                    Informasi
                </h3>

                <dl class="mt-6 space-y-4">

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Nama
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $company->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Email
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $company->email ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Telepon
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $company->phone ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Lokasi
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $company->city ?: '-' }}
                            @if ($company->province)
                                , {{ $company->province }}
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Industri
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $company->industry ?: '-' }}
                        </dd>
                    </div>

                </dl>

            </div>

            <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">
                    <h3 class="text-base font-semibold text-slate-900">
                        Customer
                    </h3>
                </div>

                <div class="divide-y divide-slate-200">

                    @forelse ($company->customers as $customer)
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
                            Belum ada customer pada perusahaan ini.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

    </div>

@endsection
