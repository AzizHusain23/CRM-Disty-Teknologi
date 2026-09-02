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

        $validatedRows = $importBatch->rows()->count();

        $validationPercentage = $totalRows > 0
            ? round(($validatedRows / $totalRows) * 100, 2)
            : 0;

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
            data-validation-url="{{ route('customer-imports.validate', $importBatch) }}"
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

                    <h3 id="progress-title" class="text-lg font-semibold text-slate-900">
                        {{ $importBatch->status === 'validating' ? 'Progress Validasi' : 'Progress Import' }}
                    </h3>

                    <p
                        id="progress-message"
                        class="mt-1 text-sm text-slate-500"
                    >
                        @if ($importBatch->status === 'validating')
                            File sedang divalidasi secara bertahap.
                        @elseif ($importBatch->status === 'completed')
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

                @if ($importBatch->status === 'validating')

                    <button
                        id="import-button"
                        type="button"
                        class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
                    >
                        Lanjutkan Validasi
                    </button>

                @elseif ($importBatch->status === 'completed')

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
                        {{ number_format($importBatch->status === 'validating' ? $validationPercentage : $progressPercentage, 2) }}%
                    </span>

                    <span
                        id="progress-count"
                        class="text-sm font-medium text-slate-600"
                    >
                        @if ($importBatch->status === 'validating')
                            {{ number_format($validatedRows) }} / {{ number_format($totalRows) }}
                        @else
                            {{ number_format($importedRows) }} / {{ number_format($readyRows) }}
                        @endif
                    </span>

                </div>


                <div class="h-4 overflow-hidden rounded-full bg-slate-200">

                    <div
                        id="progress-bar"
                        class="h-full rounded-full bg-blue-700 transition-all duration-300"
                        style="width: {{ min(100, $importBatch->status === 'validating' ? $validationPercentage : $progressPercentage) }}%"
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

                @if ($importBatch->status === 'validating')

                    {{ number_format($validatedRows) }} dari {{ number_format($totalRows) }} baris sudah divalidasi.

                @elseif ($importBatch->status === 'completed')

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
                    Gunakan filter status, sorting, dan jumlah baris untuk memeriksa hasil validasi.
                </p>

            </div>

            <div class="border-b border-slate-200 px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-4">

                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $statusFilters = [
                                '' => 'All ' . number_format($totalRows),
                                'ready' => 'Ready ' . number_format($readyRows),
                                'duplicate' => 'Duplicate ' . number_format($duplicateRows),
                                'invalid' => 'Invalid ' . number_format($invalidRows),
                            ];
                        @endphp

                        @foreach ($statusFilters as $value => $label)
                            <a href="{{ request()->fullUrlWithQuery(['status' => $value, 'page' => 1]) }}"
                                class="rounded-lg px-3 py-2 text-sm font-semibold transition
                                    {{ request('status', '') === $value
                                        ? 'bg-slate-900 text-white'
                                        : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50' }}">
                                {{ $label }}
                            </a>
                        @endforeach

                        @if ($invalidRows > 0)
                            <span class="text-xs text-red-600">
                                {{ number_format($invalidRows) }} data perlu diperiksa.
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-slate-500">Tampilkan</span>
                        <form method="GET" class="inline-flex items-center gap-2">
                            @foreach (request()->except('per_page', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="per_page" onchange="this.form.submit()"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                @foreach ($perPageOptions as $option)
                                    <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </form>
                        <span class="text-slate-500">baris</span>
                    </div>

                </div>
            </div>


            <div class="overflow-x-auto">

                <table class="min-w-[1200px] divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Sheet', 'column' => 'sheet_name'])
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Row', 'column' => 'row_number'])
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Nama', 'column' => 'name'])
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Email', 'column' => 'email'])
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Telepon', 'column' => 'phone'])
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Instansi', 'column' => 'institution_name'])
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Jenis Instansi', 'column' => 'institution_type'])
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Status', 'column' => 'status'])
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

                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold {{ $rowStatusClass }}"
                                        >
                                            {{ ucfirst($row->status) }}
                                        </span>

                                        @if ($row->status === 'invalid')
                                            <button
                                                type="button"
                                                class="edit-import-row rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                                data-target="edit-import-row-{{ $row->id }}"
                                            >
                                                Edit
                                            </button>
                                        @endif
                                    </div>

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

                            @if ($row->status === 'invalid')
                                <tr id="edit-import-row-{{ $row->id }}" class="hidden bg-slate-50">
                                    <td colspan="9" class="px-6 py-5">
                                        <form method="POST" action="{{ route('customer-imports.rows.update', [$importBatch, $row]) }}" class="edit-import-form space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama *</label>
                                                    <input name="name" value="{{ $row->name }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                                    <input type="email" name="email" value="{{ $row->email }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">No. Telepon</label>
                                                    <input name="phone" value="{{ $row->phone }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">No. Dokumen</label>
                                                    <input name="document_number" value="{{ $row->document_number }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Instansi</label>
                                                    <input name="institution_name" value="{{ $row->institution_name }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jenis Instansi</label>
                                                    <select name="institution_type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                        <option value="">-- Kosong --</option>
                                                        @foreach (['Pemerintah', 'Sekolah', 'Perguruan Tinggi', 'Perusahaan', 'Yayasan', 'Lembaga', 'Lainnya'] as $type)
                                                            <option value="{{ $type }}" @selected($row->institution_type === $type)>{{ $type }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kota</label>
                                                    <input name="city" value="{{ $row->raw_data['kota'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Provinsi</label>
                                                    <input name="province" value="{{ $row->raw_data['provinsi'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                <p class="text-xs text-slate-500">Setelah disimpan, baris akan divalidasi ulang.</p>
                                                <div class="flex gap-2">
                                                    <button type="button" class="cancel-edit-import-row rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-white">Batal</button>
                                                    <button type="submit" class="save-edit-import-row rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Simpan & Validasi</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif

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
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('import-progress-card');
    if (!container) return;

    const button = document.getElementById('import-button');
    const executeUrl = container.dataset.executeUrl;
    const validationUrl = container.dataset.validationUrl;
    const csrfToken = @json(csrf_token());

    const title = document.getElementById('progress-title');
    const progressBar = document.getElementById('progress-bar');
    const progressPercent = document.getElementById('progress-percent');
    const progressCount = document.getElementById('progress-count');
    const progressMessage = document.getElementById('progress-message');
    const progressStatus = document.getElementById('progress-status');
    const statTotal = document.getElementById('stat-total');
    const statImported = document.getElementById('stat-imported');
    const statDuplicate = document.getElementById('stat-duplicate');
    const statInvalid = document.getElementById('stat-invalid');

    let running = false;
    const formatNumber = value => new Intl.NumberFormat('id-ID').format(value ?? 0);

    function renderValidation(data) {
        const percentage = Number(data.percentage ?? 0);
        title.textContent = 'Progress Validasi';
        progressBar.style.width = Math.min(100, percentage) + '%';
        progressBar.className = 'h-full rounded-full bg-blue-700 transition-all duration-300';
        progressPercent.textContent = percentage.toFixed(2) + '%';
        progressCount.textContent = formatNumber(data.validated) + ' / ' + formatNumber(data.total);
        statTotal.textContent = formatNumber(data.total);
        statImported.textContent = formatNumber(data.ready);
        statDuplicate.textContent = formatNumber(data.duplicate);
        statInvalid.textContent = formatNumber(data.invalid);

        if (data.status === 'ready') {
            progressMessage.textContent = 'Validasi selesai.';
            progressStatus.textContent = 'File selesai dianalisis. Anda sekarang bisa memperbaiki data invalid sebelum import.';
            progressStatus.className = 'mt-6 rounded-xl bg-green-50 px-5 py-4 text-sm text-green-800';
            progressBar.className = 'h-full rounded-full bg-green-600 transition-all duration-300';
            running = false;
            window.location.reload();
            return;
        }

        if (data.status === 'failed') {
            progressMessage.textContent = 'Validasi gagal.';
            progressStatus.textContent = data.error_message || data.message || 'Validasi gagal dilakukan.';
            progressStatus.className = 'mt-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-800';
            if (button) {
                button.disabled = false;
                button.textContent = 'Lanjutkan Validasi';
            }
            running = false;
            return;
        }

        progressMessage.textContent = 'Validasi berjalan bertahap agar request tidak timeout.';
        progressStatus.textContent = formatNumber(data.remaining) + ' baris masih menunggu divalidasi.';
        progressStatus.className = 'mt-6 rounded-xl bg-blue-50 px-5 py-4 text-sm text-blue-800';
    }

    async function processValidation() {
        if (!running) return;
        try {
            const response = await fetch(validationUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await response.json();
            renderValidation(data);
            if (!response.ok || data.status === 'failed') {
                throw new Error(data.error_message || data.message || 'Validasi gagal.');
            }
            if (data.status !== 'ready' && running) setTimeout(processValidation, 100);
        } catch (error) {
            console.error('Validation error:', error);
            running = false;
            progressMessage.textContent = 'Validasi berhenti sementara.';
            progressStatus.textContent = error.message || 'Koneksi terputus. Klik tombol untuk melanjutkan.';
            progressStatus.className = 'mt-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-800';
            if (button) {
                button.disabled = false;
                button.textContent = 'Lanjutkan Validasi';
            }
        }
    }

    async function processImport() {
        if (!running) return;
        try {
            const response = await fetch(executeUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({}),
            });
            const data = await response.json();
            const percentage = Number(data.percentage ?? data.overall_percentage ?? 0);
            progressBar.style.width = Math.min(100, percentage) + '%';
            progressBar.className = 'h-full rounded-full bg-blue-700 transition-all duration-300';
            progressPercent.textContent = percentage.toFixed(2) + '%';
            progressCount.textContent = formatNumber(data.imported) + ' / ' + formatNumber(data.ready_total);
            statTotal.textContent = formatNumber(data.total);
            statImported.textContent = formatNumber(data.imported);
            statDuplicate.textContent = formatNumber(data.duplicate);
            statInvalid.textContent = formatNumber(data.invalid);

            if (!response.ok) throw new Error(data.error_message || data.message || 'Import gagal.');

            if (data.status === 'completed') {
                title.textContent = 'Progress Import';
                progressMessage.textContent = 'Import selesai.';
                progressStatus.textContent = formatNumber(data.imported) + ' customer berhasil dimasukkan ke CRM.';
                progressStatus.className = 'mt-6 rounded-xl bg-green-50 px-5 py-4 text-sm text-green-800';
                progressBar.className = 'h-full rounded-full bg-green-600 transition-all duration-300';
                if (button) {
                    button.disabled = true;
                    button.textContent = 'Import Selesai';
                }
                running = false;
                window.location.reload();
                return;
            }

            title.textContent = 'Progress Import';
            progressMessage.textContent = formatNumber(data.imported) + ' customer sudah masuk dari ' + formatNumber(data.ready_total) + ' data yang siap diimport.';
            progressStatus.textContent = formatNumber(data.remaining) + ' data masih menunggu diproses.';
            progressStatus.className = 'mt-6 rounded-xl bg-blue-50 px-5 py-4 text-sm text-blue-800';
            if (running) setTimeout(processImport, 100);
        } catch (error) {
            console.error('Customer Import Error:', error);
            running = false;
            progressMessage.textContent = 'Import berhenti sementara.';
            progressStatus.textContent = error.message || 'Koneksi terputus. Klik tombol untuk meneruskan.';
            progressStatus.className = 'mt-6 rounded-xl bg-red-50 px-5 py-4 text-sm text-red-800';
            if (button) {
                button.disabled = false;
                button.textContent = 'Lanjutkan Import';
            }
        }
    }

    function startValidation() {
        if (running) return;
        running = true;
        if (button) {
            button.disabled = true;
            button.textContent = 'Validating...';
        }
        processValidation();
    }

    function startImport() {
        if (running) return;
        running = true;
        if (button) {
            button.disabled = true;
            button.textContent = 'Importing...';
        }
        processImport();
    }

    if (button) {
        button.addEventListener('click', function () {
            if (container.dataset.status === 'validating') startValidation();
            else startImport();
        });
    }

    const initialStatus = container.dataset.status;
    if (initialStatus === 'validating') startValidation();
    if (initialStatus === 'processing') startImport();

    document.querySelectorAll('.edit-import-row').forEach(function (editButton) {
        editButton.addEventListener('click', function () {
            const target = document.getElementById(editButton.dataset.target);
            if (target) target.classList.toggle('hidden');
        });
    });

    document.querySelectorAll('.cancel-edit-import-row').forEach(function (cancelButton) {
        cancelButton.addEventListener('click', function () {
            const row = cancelButton.closest('tr');
            if (row) row.classList.add('hidden');
        });
    });

    document.querySelectorAll('.edit-import-form').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const saveButton = form.querySelector('.save-edit-import-row');
            saveButton.disabled = true;
            saveButton.textContent = 'Menyimpan...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || data.error_message || 'Baris gagal diperbarui.');
                }
                window.location.reload();
            } catch (error) {
                alert(error.message || 'Baris gagal diperbarui.');
                saveButton.disabled = false;
                saveButton.textContent = 'Simpan & Validasi';
            }
        });
    });
});
</script>

@endsection
