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
                <h2 class="text-xl font-semibold text-slate-900">Daftar Customer</h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ number_format($customers->total()) }} customer ditemukan.
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
                    <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, Email, Telepon, Customer Code, Instansi..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="prospect" @selected(request('status') === 'prospect')>Prospect</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                        <option value="repeat" @selected(request('status') === 'repeat')>Repeat</option>
                    </select>
                </div>

                <div>
                    <label for="institution_id" class="mb-2 block text-sm font-medium text-slate-700">Instansi</label>
                    <select id="institution_id" name="institution_id"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">
                        <option value="">Semua Instansi</option>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}" @selected((string) request('institution_id') === (string) $institution->id)>
                                {{ $institution->name }}
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
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <p class="text-sm text-slate-500">
                    Klik judul kolom untuk mengurutkan.
                </p>

                <form method="GET" class="flex items-center gap-2 text-sm">
                    @foreach (request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page" class="text-slate-500">Tampilkan</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <span class="text-slate-500">data</span>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Customer', 'column' => 'name'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Customer Code', 'column' => 'customer_code'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Instansi', 'column' => 'institution'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Email', 'column' => 'email'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Telepon', 'column' => 'phone'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Status', 'column' => 'status'])
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse ($customers as $customer)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $customer->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $customer->customer_code }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $customer->institution?->name ?: '-' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $customer->email ?: '-' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $customer->phone ?: '-' }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = match ($customer->status) {
                                            'active' => 'bg-green-100 text-green-700',
                                            'prospect' => 'bg-amber-100 text-amber-700',
                                            'inactive' => 'bg-slate-100 text-slate-600',
                                            'repeat' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                        $statusLabels = [
                                            'active' => 'Active',
                                            'prospect' => 'Prospect',
                                            'inactive' => 'Inactive',
                                            'repeat' => 'Repeat Customer',
                                        ];
                                    @endphp
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ $statusLabels[$customer->status] ?? ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('customers.show', $customer) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Detail</a>
                                        <a href="{{ route('customers.edit', $customer) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus customer {{ addslashes($customer->name) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-sm font-medium text-slate-700">Customer tidak ditemukan.</p>
                                    <p class="mt-1 text-sm text-slate-500">Coba ubah kata pencarian atau filter.</p>
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
