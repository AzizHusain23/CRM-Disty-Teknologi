@extends('layouts.app')

@section('title', 'Perusahaan')

@section('page-heading', 'Perusahaan')

@section('page-description')
    Kelola data perusahaan atau institusi customer.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Daftar Perusahaan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $companies->total() }} perusahaan terdaftar.
                </p>
            </div>

            <a href="{{ route('companies.create') }}"
                class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                + Tambah Perusahaan
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Perusahaan
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Lokasi
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Customer
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($companies as $company)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">
                                        {{ $company->name }}
                                    </div>

                                    @if ($company->email)
                                        <div class="mt-1 text-sm text-slate-500">
                                            {{ $company->email }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $company->city ?: '-' }}
                                    @if ($company->province)
                                        , {{ $company->province }}
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $company->customers_count }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('companies.show', $company) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('companies.edit', $company) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <p class="text-sm font-medium text-slate-700">
                                        Belum ada perusahaan.
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Tambahkan perusahaan pertama.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $companies->links() }}
            </div>

        </div>

    </div>

@endsection
