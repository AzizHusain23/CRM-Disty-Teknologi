@extends('layouts.app')

@section('title', 'Import Customer')

@section('page-heading', 'Import Customer')

@section('page-description')
    Kelola batch import data customer dari Excel.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Import Customer
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Upload dan validasi data customer sebelum masuk CRM.
                </p>
            </div>

            <a href="{{ route('customer-imports.create') }}"
                class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                + Import Excel
            </a>

        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                File
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Total
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Ready
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Duplicate
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Invalid
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

                        @forelse ($batches as $batch)
                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-slate-900">
                                        {{ $batch->original_filename }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $batch->created_at->format('d M Y H:i') }}
                                    </div>

                                </td>

                                <td class="px-6 py-4 text-sm">
                                    {{ number_format($batch->total_rows) }}
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-green-700">
                                    {{ number_format($batch->ready_rows) }}
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-amber-700">
                                    {{ number_format($batch->duplicate_rows) }}
                                </td>

                                <td class="px-6 py-4 text-sm font-semibold text-red-700">
                                    {{ number_format($batch->invalid_rows) }}
                                </td>

                                <td class="px-6 py-4">

                                    @php
                                        $statusClass = match ($batch->status) {
                                            'completed' => 'bg-green-100 text-green-700',
                                            'ready' => 'bg-blue-100 text-blue-700',
                                            'processing' => 'bg-amber-100 text-amber-700',
                                            'failed' => 'bg-red-100 text-red-700',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp

                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($batch->status) }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('customer-imports.show', $batch) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Detail
                                        </a>

                                        @if ($batch->status !== 'completed')
                                            <form method="POST" action="{{ route('customer-imports.destroy', $batch) }}"
                                                onsubmit="return confirm('Hapus batch import ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                                    Hapus
                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-12 text-center">

                                    <p class="text-sm font-medium text-slate-700">
                                        Belum ada riwayat import.
                                    </p>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Upload file Excel untuk memulai.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $batches->links() }}
            </div>

        </div>

    </div>

@endsection
