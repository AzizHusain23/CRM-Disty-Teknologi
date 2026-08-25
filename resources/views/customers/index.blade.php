@extends('layouts.app')

@section('title', 'Customer')

@section('page-heading', 'Customer')

@section('page-description')
    Kelola seluruh data customer CRM.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Daftar Customer
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $customers->total() }} customer ditemukan.
                </p>
            </div>

            <a href="{{ route('customers.create') }}"
                class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                + Tambah Customer
            </a>

        </div>

        <form method="GET" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="grid gap-4 lg:grid-cols-4">

                <div class="lg:col-span-2">

                    <label for="search" class="mb-2 block text-sm font-medium text-slate-700">
                        Cari
                    </label>

                    <input id="search" type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, email, telepon, customer code, perusahaan..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

                </div>

                <div>

                    <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
                        Status
                    </label>

                    <select id="status" name="status"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>
                            Active
                        </option>
                        <option value="prospect" @selected(request('status') === 'prospect')>
                            Prospect
                        </option>
                        <option value="inactive" @selected(request('status') === 'inactive')>
                            Inactive
                        </option>
                        <option value="repeat" @selected(request('status') === 'repeat')>
                            Repeat
                        </option>
                    </select>

                </div>

                <div>

                    <label for="company_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Perusahaan
                    </label>

                    <select id="company_id" name="company_id"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
                        <option value="">Semua Perusahaan</option>

                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>

                </div>

            </div>

            <div class="mt-4 flex justify-end gap-2">

                <a href="{{ route('customers.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Reset
                </a>

                <button type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Terapkan Filter
                </button>

            </div>

        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Customer
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Perusahaan
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Kontak
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($customers as $customer)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">
                                        {{ $customer->name }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $customer->customer_code }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $customer->company?->name ?: '-' }}
                                </td>

                                <td class="px-6 py-4">

                                    <div class="text-sm text-slate-700">
                                        {{ $customer->email ?: '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $customer->phone ?: 'No phone' }}
                                    </div>

                                </td>

                                <td class="px-6 py-4">

                                    @php
                                        $statusClasses = match ($customer->status) {
                                            'active' => 'bg-green-100 text-green-700',
                                            'prospect' => 'bg-amber-100 text-amber-700',
                                            'inactive' => 'bg-slate-100 text-slate-600',
                                            'repeat' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp

                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('customers.show', $customer) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('customers.edit', $customer) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-sm font-medium text-slate-700">
                                        Customer tidak ditemukan.
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Coba ubah kata pencarian atau filter.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $customers->links() }}
            </div>

        </div>

    </div>

@endsection
