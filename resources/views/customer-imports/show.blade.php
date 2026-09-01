@extends('layouts.app')

@section('title', 'Detail Import')

@section('page-heading', 'Preview Import')

@section('page-description')
    Periksa data sebelum dimasukkan ke CRM.
@endsection

@section('content')

    @php
        /*
        |--------------------------------------------------------------------------
        | Statistik Import
        |--------------------------------------------------------------------------
        |
        | importedRows dihitung langsung dari import_rows.status = imported.
        | Jadi angka "Berhasil" benar-benar menunjukkan data yang sudah masuk.
        |
        */

        $importedRows = $importBatch->rows()
            ->where('status', 'imported')
            ->count();

        $readyRows = (int) $importBatch->ready_rows;

        $duplicateRows = (int) $importBatch->duplicate_rows;

        $invalidRows = (int) $importBatch->invalid_rows;

        $totalRows = (int) $importBatch->total_rows;

        $remainingRows = $importBatch->rows()
            ->where('status', 'ready')
            ->count();

        $progressPercentage = $readyRows > 0
            ? round(($importedRows / $readyRows) * 100, 2)
            : ($importBatch->status === 'completed' ? 100 : 0);
    @endphp


    <div class="space-y-6">

        {{-- =========================================================
             HEADER
        ========================================================== --}}

        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    {{ $importBatch->original_filename }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Dibuat {{ $importBatch->created_at->format('d M Y H:i') }}
                </p>

            </div>

            <a
                href="{{ route('customer-imports.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                Kembali
            </a>

        </div>


        {{-- =========================================================
             ERROR
        ========================================================== --}}

        @if ($importBatch->status === 'failed')

            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

                <h3 class="font-semibold text-red-900">
                    Import gagal diproses
                </h3>

                @if ($importBatch->error_message)

                    <p class="mt-2 text-sm leading-6 text-red-700">
                        {{ $importBatch->error_message }}
                    </p>

                @endif

            </div>

        @endif


        {{-- =========================================================
             PROGRESS CARD
        ========================================================== --}}

        <div
            id="import-progress-card"
            data-execute-url="{{ route('customer-imports.execute', $importBatch) }}"
            data-status="{{ $importBatch->status }}"
            data-ready="{{ $readyRows }}"
            data-imported="{{ $importedRows }}"
            data-remaining="{{ $remainingRows }}"
            data-total="{{ $totalRows }}"
            data-duplicate="{{ $duplicateRows }}"
            data-invalid="{{ $invalidRows }}"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >

            {{-- Header Progress --}}

            <div class="flex flex-wrap items-start justify-between gap-4">

                <div>

                    <h3 class="text-lg font-semibold text-slate-900">
                        Progress Import
                    </h3>

                    <p
                        id="progress-message"
                        class="mt-1 text-sm text-slate-500"
                    >
                        @if ($importBatch->status === 'completed')
                            Import selesai.
                        @elseif ($importBatch->status === 'processing')
                            Import sedang berjalan.
                        @elseif ($importBatch->status === 'ready')
                            File siap diimport.
                        @else
                            Menunggu proses import.
                        @endif
                    </p>

                </div>


                {{-- Tombol --}}

                @if ($importBatch->status === 'completed')

                    <span class="inline-flex items-center rounded-lg bg-green-100 px-5 py-2.5 text-sm font-semibold text-green-700">
                        Import Selesai
                    </span>

                @elseif ($importBatch->status === 'ready')

                    <button
                        id="import-button"
                        type="button"
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
                    >
                        Import Data
                    </button>

                @elseif ($importBatch->status === 'processing')

                    <button
                        id="import-button"
                        type="button"
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
                    >
                        Lanjutkan Import
                    </button>

                @elseif ($importBatch->status === 'failed')

                    <button
                        id="import-button"
                        type="button"
                        class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700"
                    >
                        Lanjutkan Import
                    </button>

                @endif

            </div>


            {{-- =====================================================
                 PROGRESS BAR
            ====================================================== --}}

            <div class="mt-6">

                <div class="mb-2 flex items-center justify-between">

                    <span
                        id="progress-percent"
                        class="text-sm font-bold text-blue-700"
                    >
                        {{ number_format($progressPercentage, 2) }}%
                    </span>

                    <span
                        id="progress-count"
                        class="text-sm font-medium text-slate-600"
                    >
                        {{ number_format($importedRows) }}
                        /
                        {{ number_format($readyRows) }}
                    </span>

                </div>


                <div class="h-4 overflow-hidden rounded-full bg-slate-200">

                    <div
                        id="progress-bar"
                        class="h-full rounded-full bg-blue-700 transition-all duration-300"
                        style="width: {{ min(100, $progressPercentage) }}%"
                    ></div>

                </div>

            </div>


            {{-- =====================================================
                 STATISTICS
            ====================================================== --}}

            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- TOTAL --}}

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                    <p class="text-sm text-slate-500">
                        Total
                    </p>

                    <p
                        id="stat-total"
                        class="mt-2 text-2xl font-bold text-slate-900"
                    >
                        {{ number_format($totalRows) }}
                    </p>

                </div>


                {{-- IMPORTED --}}

                <div class="rounded-xl border border-green-200 bg-green-50 p-5">

                    <p class="text-sm text-green-700">
                        Berhasil
                    </p>

                    <p
                        id="stat-imported"
                        class="mt-2 text-2xl font-bold text-green-800"
                    >
                        {{ number_format($importedRows) }}
                    </p>

                </div>


                {{-- DUPLICATE --}}

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">

                    <p class="text-sm text-amber-700">
                        Duplicate
                    </p>

                    <p
                        id="stat-duplicate"
                        class="mt-2 text-2xl font-bold text-amber-800"
                    >
                        {{ number_format($duplicateRows) }}
                    </p>

                </div>


                {{-- INVALID --}}

                <div class="rounded-xl border border-red-200 bg-red-50 p-5">

                    <p class="text-sm text-red-700">
                        Invalid
                    </p>

                    <p
                        id="stat-invalid"
                        class="mt-2 text-2xl font-bold text-red-800"
                    >
                        {{ number_format($invalidRows) }}
                    </p>

                </div>

            </div>


            {{-- =====================================================
                 STATUS MESSAGE
            ====================================================== --}}

            <div
                id="progress-status"
                class="mt-6 rounded-xl px-5 py-4 text-sm
                @if ($importBatch->status === 'completed')
                    bg-green-50 text-green-800
                @elseif ($importBatch->status === 'failed')
                    bg-red-50 text-red-800
                @else
                    bg-blue-50 text-blue-800
                @endif
                "
            >

                @if ($importBatch->status === 'completed')

                    {{ number_format($importedRows) }}
                    customer berhasil dimasukkan ke CRM.

                @elseif ($importBatch->status === 'processing')

                    {{ number_format($remainingRows) }}
                    data masih menunggu diproses.

                @elseif ($importBatch->status === 'ready')

                    {{ number_format($readyRows) }}
                    customer valid siap diimport.

                @elseif ($importBatch->status === 'failed')

                    {{ $importBatch->error_message ?: 'Import gagal dilakukan.' }}

                @endif

            </div>

        </div>


        {{-- =========================================================
             PREVIEW DATA
        ========================================================== --}}

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

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Sheet
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Row
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Nama
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Email
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Telepon
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Instansi
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Jenis Instansi
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Keterangan
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-200">

                        @forelse ($rows as $row)

                            <tr class="hover:bg-slate-50">

                                <td class="px-4 py-4 text-sm text-slate-700">
                                    {{ $row->sheet_name }}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-500">
                                    {{ $row->row_number }}
                                </td>

                                <td class="px-4 py-4 text-sm font-semibold text-slate-900">
                                    {{ $row->name ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-700">
                                    {{ $row->email ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-700">
                                    {{ $row->phone ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-700">
                                    {{ $row->institution_name ?: '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-700">
                                    {{ $row->institution_type ?: '-' }}
                                </td>

                                <td class="px-4 py-4">

                                    @php
                                        $rowStatusClass = match ($row->status) {
                                            'ready' =>
                                                'bg-green-100 text-green-700',

                                            'duplicate' =>
                                                'bg-amber-100 text-amber-700',

                                            'invalid' =>
                                                'bg-red-100 text-red-700',

                                            'imported' =>
                                                'bg-blue-100 text-blue-700',

                                            default =>
                                                'bg-slate-100 text-slate-600',
                                        };
                                    @endphp

                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold {{ $rowStatusClass }}"
                                    >
                                        {{ ucfirst($row->status) }}
                                    </span>

                                </td>

                                <td class="max-w-sm px-4 py-4 text-sm text-slate-600">

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

                                <td
                                    colspan="9"
                                    class="px-6 py-12 text-center text-sm text-slate-500"
                                >
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


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const container =
            document.getElementById(
                'import-progress-card'
            );


        const button =
            document.getElementById(
                'import-button'
            );


        if (!container) {
            return;
        }


        const executeUrl =
            container.dataset.executeUrl;


        const csrfToken =
            @json(csrf_token());


        const progressBar =
            document.getElementById(
                'progress-bar'
            );


        const progressPercent =
            document.getElementById(
                'progress-percent'
            );


        const progressCount =
            document.getElementById(
                'progress-count'
            );


        const progressMessage =
            document.getElementById(
                'progress-message'
            );


        const progressStatus =
            document.getElementById(
                'progress-status'
            );


        const statTotal =
            document.getElementById(
                'stat-total'
            );


        const statImported =
            document.getElementById(
                'stat-imported'
            );


        const statDuplicate =
            document.getElementById(
                'stat-duplicate'
            );


        const statInvalid =
            document.getElementById(
                'stat-invalid'
            );


        let running = false;


        function formatNumber(
            value
        ) {

            return new Intl.NumberFormat(
                'id-ID'
            ).format(
                value ?? 0
            );

        }


        function renderProgress(
            data
        ) {

            const percentage =
                Number(
                    data.percentage
                    ?? data.overall_percentage
                    ?? 0
                );


            progressBar.style.width =
                Math.min(
                    100,
                    percentage
                ) + '%';


            progressPercent.textContent =
                percentage.toFixed(
                    2
                ) + '%';


            progressCount.textContent =
                formatNumber(
                    data.imported
                )
                + ' / '
                + formatNumber(
                    data.ready_total
                );


            statTotal.textContent =
                formatNumber(
                    data.total
                );


            statImported.textContent =
                formatNumber(
                    data.imported
                );


            statDuplicate.textContent =
                formatNumber(
                    data.duplicate
                );


            statInvalid.textContent =
                formatNumber(
                    data.invalid
                );


            /*
            |--------------------------------------------------------------------------
            | COMPLETED
            |--------------------------------------------------------------------------
            */

            if (
                data.status ===
                'completed'
            ) {

                progressMessage.textContent =
                    'Import selesai.';


                progressStatus.textContent =
                    formatNumber(
                        data.imported
                    )
                    + ' customer berhasil dimasukkan ke CRM.';


                progressStatus.className =
                    'mt-6 rounded-xl bg-green-50 px-5 py-4 text-sm text-green-800';


                progressBar.className =
                    'h-full rounded-full bg-green-600 transition-all duration-300';


                if (button) {

                    button.disabled =
                        true;

                    button.textContent =
                        'Import Selesai';

                    button.className =
                        'rounded-lg bg-green-100 px-5 py-2.5 text-sm font-semibold text-green-700';

                }


                running =
                    false;

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | FAILED
            |--------------------------------------------------------------------------
            */

            if (
                data.status ===
                'failed'
            ) {

                progressMessage.textContent =
                    'Import berhenti karena terjadi kesalahan.';


                progressStatus.textContent =
                    data.error_message
                    || 'Import gagal dilakukan.';


                progressStatus.className =
                    'mt-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-800';


                if (button) {

                    button.disabled =
                        false;

                    button.textContent =
                        'Lanjutkan Import';

                }


                running =
                    false;

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | PROCESSING
            |--------------------------------------------------------------------------
            */

            progressMessage.textContent =
                formatNumber(
                    data.imported
                )
                + ' customer sudah masuk dari '
                + formatNumber(
                    data.ready_total
                )
                + ' data yang siap diimport.';


            progressStatus.textContent =
                formatNumber(
                    data.remaining
                )
                + ' data masih menunggu diproses.';


            progressStatus.className =
                'mt-6 rounded-xl bg-blue-50 px-5 py-4 text-sm text-blue-800';

        }


        async function processNextChunk() {

            if (!running) {
                return;
            }


            try {

                const response =
                    await fetch(
                        executeUrl,
                        {
                            method:
                                'POST',

                            headers: {

                                'Accept':
                                    'application/json',

                                'Content-Type':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    csrfToken,

                                'X-Requested-With':
                                    'XMLHttpRequest',

                            },

                            body:
                                JSON.stringify({}),

                        }
                    );


                const data =
                    await response.json();


                if (
                    !response.ok
                ) {

                    throw new Error(
                        data.error_message
                        || data.message
                        || 'Import gagal.'
                    );

                }


                renderProgress(
                    data
                );


                /*
                |--------------------------------------------------------------------------
                | LANJUT KE CHUNK BERIKUTNYA
                |--------------------------------------------------------------------------
                |
                | Selama masih ada row READY,
                | request berikutnya otomatis dijalankan.
                |
                */

                if (
                    data.status ===
                        'processing'
                    && Number(
                        data.remaining
                    ) > 0
                    && running
                ) {

                    setTimeout(
                        function () {

                            processNextChunk();

                        },
                        100
                    );

                }

            } catch (
                error
            ) {

                console.error(
                    'Customer Import Error:',
                    error
                );


                running =
                    false;


                progressMessage.textContent =
                    'Import berhenti sementara.';


                progressStatus.textContent =
                    'Koneksi terputus. Klik "Lanjutkan Import" untuk meneruskan.';


                progressStatus.className =
                    'mt-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-800';


                if (button) {

                    button.disabled =
                        false;

                    button.textContent =
                        'Lanjutkan Import';

                }

            }

        }


        function startImport() {

            if (running) {
                return;
            }


            running =
                true;


            if (button) {

                button.disabled =
                    true;

                button.textContent =
                    'Importing...';

                button.className =
                    'rounded-lg bg-slate-400 px-5 py-2.5 text-sm font-semibold text-white cursor-not-allowed';

            }


            progressMessage.textContent =
                'Import sedang diproses...';


            progressStatus.textContent =
                'Memproses data customer secara bertahap.';


            progressStatus.className =
                'mt-6 rounded-xl bg-blue-50 px-5 py-4 text-sm text-blue-800';


            processNextChunk();

        }


        /*
        |--------------------------------------------------------------------------
        | Tombol Import
        |--------------------------------------------------------------------------
        */

        if (button) {

            button.addEventListener(
                'click',
                function () {

                    startImport();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | AUTO RESUME
        |--------------------------------------------------------------------------
        |
        | Kalau user membuka kembali halaman ketika batch masih
        | processing, import otomatis dilanjutkan.
        |
        */

        const initialStatus =
            container.dataset.status;


        if (
            initialStatus ===
            'processing'
        ) {

            startImport();

        }


        /*
        |--------------------------------------------------------------------------
        | INITIAL COMPLETED STATE
        |--------------------------------------------------------------------------
        */

        if (
            initialStatus ===
            'completed'
        ) {

            renderProgress({

                status:
                    'completed',

                total:
                    Number(
                        container.dataset.total
                    ),

                ready_total:
                    Number(
                        container.dataset.ready
                    ),

                imported:
                    Number(
                        container.dataset.imported
                    ),

                remaining:
                    0,

                duplicate:
                    Number(
                        container.dataset.duplicate
                    ),

                invalid:
                    Number(
                        container.dataset.invalid
                    ),

                percentage:
                    Number(
                        {{ $progressPercentage }}
                    ),

            });

        }

    });

</script>

@endsection
