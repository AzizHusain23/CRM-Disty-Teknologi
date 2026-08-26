@extends('layouts.app')

@section('title', 'Import Customer')

@section('page-heading', 'Import Customer')

@section('page-description')
    Upload data customer menggunakan format Excel standar CRM.
@endsection

@section('content')

    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Import Data Customer
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Semua file Excel yang akan dimasukkan ke CRM harus mengikuti
                    format standar agar data dapat diproses secara konsisten.
                </p>
            </div>

            <a href="{{ route('customer-imports.template') }}"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Download Template Excel
            </a>

        </div>

        {{-- Standard Format --}}
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6">

            <div class="flex items-start gap-4">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                    <span class="text-lg font-bold">i</span>
                </div>

                <div>

                    <h3 class="text-base font-semibold text-blue-900">
                        Format Excel Standar CRM
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-blue-800">
                        Gunakan satu sheet dengan format kolom yang sudah ditentukan.
                        Jangan mengubah nama kolom agar sistem dapat membaca data dengan benar.
                    </p>

                </div>

            </div>

        </div>

        {{-- Excel Structure --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-semibold text-slate-900">
                    Struktur File Excel
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Ketentuan file yang dapat diproses oleh CRM.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                No.
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Nama Kolom
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Wajib
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Keterangan
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Contoh
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                1
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Nama
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Wajib
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Nama lengkap customer.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Budi Santoso
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                2
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Email
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Opsional
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Email customer yang valid.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                budi@gmail.com
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                3
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Nomor Telepon
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Opsional
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Nomor telepon atau WhatsApp customer.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                081234567890
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                4
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Nomor Dokumen
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Opsional
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Nomor identitas atau nomor dokumen yang tersedia.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                1234567890
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                5
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Nama Instansi
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Opsional
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Nama perusahaan, sekolah, kampus, dinas, yayasan,
                                atau lembaga customer.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Universitas XYZ
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                6
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Jenis Instansi
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Opsional
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Jenis instansi customer.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Perguruan Tinggi
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                7
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Kota
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Opsional
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Kota tempat customer atau instansi berada.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Surabaya
                            </td>
                        </tr>

                        <tr>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                8
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">
                                Provinsi
                            </td>

                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Opsional
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Provinsi customer atau instansi.
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                Jawa Timur
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Allowed Values --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <h3 class="text-base font-semibold text-slate-900">
                Nilai Jenis Instansi
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Gunakan salah satu nilai berikut pada kolom
                <strong>Jenis Instansi</strong>.
            </p>

            <div class="mt-5 flex flex-wrap gap-2">

                @foreach (['Pemerintah', 'Sekolah', 'Perguruan Tinggi', 'Perusahaan', 'Yayasan', 'Lembaga', 'Lainnya'] as $type)
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700">
                        {{ $type }}
                    </span>
                @endforeach

            </div>

        </div>

        {{-- Example --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-semibold text-slate-900">
                    Contoh Format
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Baris pertama adalah header. Data customer dimulai dari baris kedua.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-[1100px] divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Nama
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Email
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Nomor Telepon
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Nomor Dokumen
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Nama Instansi
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Jenis Instansi
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Kota
                            </th>

                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-500">
                                Provinsi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        <tr>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Budi Santoso
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                budi@gmail.com
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                081234567890
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                123456
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                PT ABC
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Perusahaan
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Surabaya
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Jawa Timur
                            </td>

                        </tr>

                        <tr>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Siti Aminah
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                siti@gmail.com
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                081298765432
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                654321
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Dinas Pendidikan
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Pemerintah
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Surabaya
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Jawa Timur
                            </td>

                        </tr>

                        <tr>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Andi Pratama
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                andi@gmail.com
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                081277788999
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                112233
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Universitas XYZ
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Perguruan Tinggi
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Malang
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-700">
                                Jawa Timur
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        {{-- Rules --}}
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">

            <h3 class="text-base font-semibold text-amber-900">
                Aturan Sebelum Upload
            </h3>

            <div class="mt-4 space-y-3 text-sm leading-6 text-amber-900">

                <div>
                    <strong>1.</strong>
                    Gunakan hanya satu sheet dengan nama
                    <strong>customers</strong>.
                </div>

                <div>
                    <strong>2.</strong>
                    Baris pertama wajib berisi nama kolom.
                </div>

                <div>
                    <strong>3.</strong>
                    Jangan menggabungkan cell pada bagian header.
                </div>

                <div>
                    <strong>4.</strong>
                    Jangan menambahkan judul, logo, atau informasi lain di atas header.
                </div>

                <div>
                    <strong>5.</strong>
                    Jangan mengubah nama kolom standar.
                </div>

                <div>
                    <strong>6.</strong>
                    Satu baris mewakili satu customer.
                </div>

                <div>
                    <strong>7.</strong>
                    Jangan menggunakan formula yang menghasilkan error.
                </div>

                <div>
                    <strong>8.</strong>
                    Data kosong diperbolehkan untuk kolom opsional.
                </div>

            </div>

        </div>

        {{-- Upload --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">

            <div class="mb-6">

                <h3 class="text-lg font-semibold text-slate-900">
                    Upload File
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Setelah memastikan format sesuai standar, upload file Excel di bawah.
                </p>

            </div>

            @if ($errors->any())

                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">

                    <ul class="space-y-1">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            <form method="POST" action="{{ route('customer-imports.store') }}" enctype="multipart/form-data"
                class="space-y-6">

                @csrf

                <div>

                    <label for="file" class="mb-2 block text-sm font-medium text-slate-700">
                        File Excel *
                    </label>

                    <input id="file" type="file" name="file" accept=".xlsx,.xls" required
                        class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-900 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800">

                    <p class="mt-2 text-xs text-slate-500">
                        Format yang diterima: XLSX atau XLS. Maksimal 20 MB.
                    </p>

                </div>

                <div class="flex justify-end gap-3">

                    <a href="{{ route('customer-imports.index') }}"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                        Periksa & Analisis File
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
