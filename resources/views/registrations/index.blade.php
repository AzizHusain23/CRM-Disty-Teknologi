@extends('layouts.app')

@section('title', 'Pendaftaran Training')
@section('page-heading', 'Pendaftaran Training')
@section('page-description')
    Catat customer yang mengikuti pelatihan dan kelola riwayat pendaftarannya.
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

        <div class="flex flex-wrap items-end justify-between gap-4">
            <form method="GET" class="grid gap-3 sm:grid-cols-[minmax(280px,1fr)_180px_auto]">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Cari</label>
                    <input type="search" name="search" value="{{ request('search') }}"
                        placeholder="Nomor registrasi, customer, atau pelatihan..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <select name="status" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                        <option value="">Semua status</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="self-end rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Filter
                </button>
            </form>

            <a href="{{ route('registrations.create') }}"
                class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                + Tambah Pendaftaran
            </a>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-5 py-4">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $registrations->firstItem() ?? 0 }}–{{ $registrations->lastItem() ?? 0 }} dari {{ number_format($registrations->total()) }} pendaftaran.
            </p>
            <form method="GET" class="flex items-center gap-2 text-sm text-slate-600">
                @foreach (request()->except('per_page', 'page') as $key => $value)
                    @if (is_scalar($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="per_page">Tampilkan</label>
                <select id="per_page" name="per_page" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'No. Registrasi', 'column' => 'registration_number'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Customer', 'column' => 'customer'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Pelatihan', 'column' => 'training'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Tanggal', 'column' => 'training_date'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Status', 'column' => 'status'])
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Amount', 'column' => 'amount'])
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($registrations as $registration)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-5 text-sm font-semibold text-slate-900">{{ $registration->registration_number ?: '-' }}</td>
                                <td class="px-6 py-5">
                                    <a href="{{ route('customers.show', $registration->customer) }}" class="font-semibold text-slate-900 hover:underline">
                                        {{ $registration->customer->name }}
                                    </a>
                                    <div class="mt-1 text-xs text-slate-400">{{ $registration->customer->customer_code }}</div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-700">{{ $registration->training->name }}</td>
                                <td class="px-6 py-5 text-sm text-slate-700">{{ $registration->training_date?->format('d M Y') ?: '-' }}</td>
                                <td class="px-6 py-5">
                                    @php
                                        $statusClasses = match ($registration->status) {
                                            'completed' => 'bg-green-100 text-green-700',
                                            'confirmed' => 'bg-blue-100 text-blue-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            default => 'bg-amber-100 text-amber-700',
                                        };
                                    @endphp
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                        {{ $statusLabels[$registration->status] ?? ucfirst($registration->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-700">
                                    {{ $registration->amount !== null ? 'Rp '.number_format((float) $registration->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('customers.show', $registration->customer) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Customer</a>
                                        <a href="{{ route('registrations.edit', $registration) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                                        <form method="POST" action="{{ route('registrations.destroy', $registration) }}" onsubmit="return confirm('Hapus pendaftaran {{ addslashes($registration->registration_number ?: 'ini') }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada data pendaftaran training.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($registrations->hasPages())
            <div class="rounded-xl border border-slate-200 bg-white px-5 py-4">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
@endsection
