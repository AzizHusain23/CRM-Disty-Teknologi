@extends('layouts.app')

@section('title', 'Detail Import')

@section('page-heading', 'Preview Import')

@section('page-description')
    Periksa data sebelum dimasukkan ke CRM.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    {{ $importBatch->original_filename }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Dibuat {{ $importBatch->created_at->format('d M Y H:i') }}
                </p>

            </div>

            <a href="{{ route('customer-imports.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Kembali
            </a>

        </div>

        @if ($importBatch->status === 'failed')

            <div class="rounded-xl border border-red-200 bg-red-50 p-5">

                <h3 class="font-semibold text-red-800">
                    Import gagal diproses
                </h3>

                @if ($importBatch->error_message)
                    <p class="mt-2 text-sm leading-6 text-red-700">
                        {{ $importBatch->error_message }}
                    </p>
                @endif

            </div>

        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <p class="text-sm text-slate-500">
                    Total
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-900">
                    {{ number_format($importBatch->total_rows) }}
                </p>

            </div>

            <div class="rounded-2xl border border-green-200 bg-green-50 p-6 shadow-sm">

                <p class="text-sm text-green-700">
                    Siap Import
                </p>

                <p class="mt-2 text-3xl font-bold text-green-800">
                    {{ number_format($importBatch->ready_rows) }}
                </p>

            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">

                <p class="text-sm text-amber-700">
                    Duplicate
                </p>

                <p class="mt-2 text-3xl font-bold text-amber-800">
                    {{ number_format($importBatch->duplicate_rows) }}
                </p>

            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm">

                <p class="text-sm text-red-700">
                    Invalid
                </p>

                <p class="mt-2 text-3xl font-bold text-red-800">
                    {{ number_format($importBatch->invalid_rows) }}
                </p>

            </div>

        </div>

        @if ($importBatch->status === 'ready')

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

                <div class="flex flex-wrap items-center justify-between gap-4">

                    <div>

                        <h3 class="font-semibold text-blue-900">
                            File siap diimport
                        </h3>

                        <p class="mt-1 text-sm text-blue-800">
                            {{ number_format($importBatch->ready_rows) }}
                            customer valid akan dibuat di CRM.
                        </p>

                    </div>

                    @if ($importBatch->ready_rows > 0)
                        <form method="POST" action="{{ route('customer-imports.execute', $importBatch) }}"
                            onsubmit="return confirm('Import {{ number_format($importBatch->ready_rows) }} customer ke CRM sekarang?')">

                            @csrf

                            <button type="submit"
                                class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
                                Import Data
                            </button>

                        </form>
                    @endif

                </div>

            </div>

        @endif

        @if ($importBatch->status === 'completed')
            <div class="rounded-2xl border border-green-200 bg-green-50 p-5">

                <h3 class="font-semibold text-green-900">
                    Import selesai
                </h3>

                <p class="mt-1 text-sm text-green-800">
                    Data valid sudah dimasukkan ke CRM.
                </p>

            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-semibold text-slate-900">
                    Preview Data
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Menampilkan maksimal 50 baris per halaman.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-[1200px] divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Sheet
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Row
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Nama
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Email
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Telepon
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Instansi
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Jenis Instansi
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Status
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                                Keterangan
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($rows as $row)
                            <tr class="hover:bg-slate-50">

                                <td class="px-4 py-4 text-sm">
                                    {{ $row->sheet_name }}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-500">
                                    {{ $row->row_number }}
                                </td>

                                <td class="px-4 py-4 text-sm font-semibold text-slate-900">
                                    {{ $row->name ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    {{ $row->email ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    {{ $row->phone ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    {{ $row->institution_name ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    {{ $row->institution_type ?: '-' }}
                                </td>

                                <td class="px-4 py-4">

                                    @php
                                        $rowStatusClass = match ($row->status) {
                                            'ready' => 'bg-green-100 text-green-700',
                                            'duplicate' => 'bg-amber-100 text-amber-700',
                                            'invalid' => 'bg-red-100 text-red-700',
                                            'imported' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp

                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $rowStatusClass }}">
                                        {{ ucfirst($row->status) }}
                                    </span>

                                </td>

                                <td class="max-w-xs px-4 py-4 text-sm text-slate-600">

                                    @if ($row->duplicate_reason)
                                        {{ $row->duplicate_reason }}
                                    @elseif ($row->error_message)
                                        {{ $row->error_message }}
                                    @else
                                        -
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Tidak ada data.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $rows->links() }}
            </div>

        </div>

    </div>

@endsection
