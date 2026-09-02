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

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <p class="text-sm text-slate-500">Klik judul kolom untuk mengurutkan.</p>

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
                    <span class="text-slate-500">batch</span>
                </form>
            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'File', 'column' => 'original_filename'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Progress', 'column' => 'imported_rows'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Total', 'column' => 'total_rows'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Duplicate', 'column' => 'duplicate_rows'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Invalid', 'column' => 'invalid_rows'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Status', 'column' => 'status'])
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($batches as $batch)
                            @php
                                $importedRows = (int) ($batch->imported_rows ?? 0);
                                $remainingRows = (int) ($batch->remaining_rows ?? 0);
                                $readyRows = (int) $batch->ready_rows;

                                $progressPercentage =
                                    $readyRows > 0
                                        ? round(($importedRows / $readyRows) * 100, 2)
                                        : ($batch->status === 'completed'
                                            ? 100
                                            : 0);
                            @endphp

                            <tr class="hover:bg-slate-50" data-import-batch data-batch-id="{{ $batch->id }}"
                                data-execute-url="{{ route('customer-imports.execute', $batch) }}">

                                <td class="px-6 py-5">

                                    <div class="font-semibold text-slate-900">
                                        {{ $batch->original_filename }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $batch->created_at->format('d M Y H:i') }}
                                    </div>

                                </td>

                                <td class="px-6 py-5">

                                    <div class="w-64 max-w-full">

                                        <div class="mb-2 flex items-center justify-between text-xs">

                                            <span data-progress-percent class="font-semibold text-slate-700">
                                                {{ number_format($progressPercentage, 2) }}%
                                            </span>

                                            <span data-progress-count class="text-slate-500">
                                                {{ number_format($importedRows) }} / {{ number_format($readyRows) }}
                                            </span>

                                        </div>

                                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">

                                            <div data-progress-bar
                                                class="h-full rounded-full bg-indigo-600 transition-all duration-500"
                                                style="width: {{ min(100, $progressPercentage) }}%"></div>

                                        </div>

                                        <div data-progress-status class="mt-2 text-xs text-slate-500">
                                            @if ($batch->status === 'processing')
                                                Sedang memproses...
                                            @elseif ($batch->status === 'completed')
                                                Import selesai.
                                            @elseif ($batch->status === 'ready')
                                                Siap diimport.
                                            @elseif ($batch->status === 'failed')
                                                Import gagal.
                                            @else
                                                {{ ucfirst($batch->status) }}
                                            @endif
                                        </div>

                                    </div>

                                </td>

                                <td class="px-6 py-5 text-sm">

                                    <div class="space-y-1">

                                        <div>
                                            <span class="font-semibold text-slate-700">
                                                Total:
                                            </span>

                                            {{ number_format($batch->total_rows) }}
                                        </div>

                                        <div class="text-green-700">
                                            <span class="font-semibold">
                                                Imported:
                                            </span>

                                            <span data-stat-imported>
                                                {{ number_format($importedRows) }}
                                            </span>
                                        </div>

                                        <div class="text-blue-700">
                                            <span class="font-semibold">
                                                Ready:
                                            </span>

                                            <span data-stat-remaining>
                                                {{ number_format($remainingRows) }}
                                            </span>
                                        </div>

                                    </div>

                                </td>

                                <td class="px-6 py-5 text-sm font-semibold text-amber-700">
                                    <span data-stat-duplicate>
                                        {{ number_format($batch->duplicate_rows) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-sm font-semibold text-red-700">
                                    <span data-stat-invalid>
                                        {{ number_format($batch->invalid_rows) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5">

                                    <span data-status-badge @class([
                                        'rounded-full px-3 py-1 text-xs font-semibold',
                                        'bg-green-100 text-green-700' => $batch->status === 'completed',
                                        'bg-blue-100 text-blue-700' => $batch->status === 'ready',
                                        'bg-amber-100 text-amber-700' => $batch->status === 'processing',
                                        'bg-red-100 text-red-700' => $batch->status === 'failed',
                                        'bg-slate-100 text-slate-700' => !in_array(
                                            $batch->status,
                                            ['completed', 'ready', 'processing', 'failed'],
                                            true),
                                    ])>
                                        {{ ucfirst($batch->status) }}
                                    </span>

                                </td>

                                <td class="px-6 py-5">

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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const batchRows = document.querySelectorAll('[data-import-batch]');

            if (!batchRows.length) {
                return;
            }

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            if (!csrfToken) {
                console.error('CSRF token tidak ditemukan.');
                return;
            }

            const activeRequests = new Set();

            function formatNumber(value) {
                return new Intl.NumberFormat('id-ID').format(value ?? 0);
            }

            function updateBatchRow(row, data) {
                const progressPercent = row.querySelector('[data-progress-percent]');
                const progressCount = row.querySelector('[data-progress-count]');
                const progressBar = row.querySelector('[data-progress-bar]');
                const progressStatus = row.querySelector('[data-progress-status]');

                const statImported = row.querySelector('[data-stat-imported]');
                const statRemaining = row.querySelector('[data-stat-remaining]');
                const statDuplicate = row.querySelector('[data-stat-duplicate]');
                const statInvalid = row.querySelector('[data-stat-invalid]');

                const badge = row.querySelector('[data-status-badge]');

                const percentage = Number(
                    data.percentage ?? data.overall_percentage ?? 0
                );

                if (progressPercent) {
                    progressPercent.textContent =
                        `${percentage.toFixed(2)}%`;
                }

                if (progressCount) {
                    progressCount.textContent =
                        `${formatNumber(data.imported)} / ${formatNumber(data.ready_total)}`;
                }

                if (progressBar) {
                    progressBar.style.width =
                        `${Math.min(100, percentage)}%`;
                }

                if (statImported) {
                    statImported.textContent =
                        formatNumber(data.imported);
                }

                if (statRemaining) {
                    statRemaining.textContent =
                        formatNumber(data.remaining);
                }

                if (statDuplicate) {
                    statDuplicate.textContent =
                        formatNumber(data.duplicate);
                }

                if (statInvalid) {
                    statInvalid.textContent =
                        formatNumber(data.invalid);
                }

                if (badge) {
                    badge.textContent =
                        data.status ?
                        data.status.charAt(0).toUpperCase() +
                        data.status.slice(1) :
                        'Unknown';

                    badge.className =
                        'rounded-full px-3 py-1 text-xs font-semibold';

                    if (data.status === 'completed') {
                        badge.classList.add(
                            'bg-green-100',
                            'text-green-700'
                        );
                    } else if (data.status === 'processing') {
                        badge.classList.add(
                            'bg-amber-100',
                            'text-amber-700'
                        );
                    } else if (data.status === 'failed') {
                        badge.classList.add(
                            'bg-red-100',
                            'text-red-700'
                        );
                    } else {
                        badge.classList.add(
                            'bg-blue-100',
                            'text-blue-700'
                        );
                    }
                }

                if (progressStatus) {
                    if (data.status === 'completed') {
                        progressStatus.textContent =
                            'Import selesai.';
                    } else if (data.status === 'failed') {
                        progressStatus.textContent =
                            data.error_message ||
                            'Import gagal.';
                    } else if (data.status === 'processing') {
                        progressStatus.textContent =
                            `Sedang memproses. ${formatNumber(data.remaining)} data tersisa...`;
                    } else {
                        progressStatus.textContent =
                            'Siap diimport.';
                    }
                }
            }

            async function processBatch(row) {
                const batchId =
                    row.dataset.batchId;

                const executeUrl =
                    row.dataset.executeUrl;

                if (
                    !batchId ||
                    !executeUrl
                ) {
                    return;
                }

                if (
                    activeRequests.has(batchId)
                ) {
                    return;
                }

                activeRequests.add(batchId);

                try {
                    const response =
                        await fetch(
                            executeUrl, {
                                method: 'POST',

                                headers: {
                                    'Accept': 'application/json',

                                    'Content-Type': 'application/json',

                                    'X-CSRF-TOKEN': csrfToken,

                                    'X-Requested-With': 'XMLHttpRequest',
                                },

                                body: JSON.stringify({}),
                            }
                        );

                    const data =
                        await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.error ||
                            data.message ||
                            'Import gagal.'
                        );
                    }

                    updateBatchRow(
                        row,
                        data
                    );

                    if (
                        data.status === 'processing' &&
                        Number(data.remaining) > 0
                    ) {
                        setTimeout(
                            () => processBatch(row),
                            150
                        );
                    }

                } catch (error) {
                    console.error(
                        `Import batch #${batchId}:`,
                        error
                    );

                    const status =
                        row.querySelector(
                            '[data-progress-status]'
                        );

                    if (status) {
                        status.textContent =
                            'Koneksi terputus. Buka Detail lalu klik Lanjutkan Import.';
                    }

                } finally {
                    activeRequests.delete(
                        batchId
                    );
                }
            }

            batchRows.forEach((row) => {
                const badge =
                    row.querySelector(
                        '[data-status-badge]'
                    );

                const currentStatus =
                    badge?.textContent
                    ?.trim()
                    ?.toLowerCase();

                if (
                    currentStatus ===
                    'processing'
                ) {
                    processBatch(row);
                }
            });
        });
    </script>
@endpush
