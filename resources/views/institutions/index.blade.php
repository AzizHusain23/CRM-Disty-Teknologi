@extends('layouts.app')

@section('title', 'Instansi')

@section('page-heading', 'Instansi')

@section('page-description')
    Kelola data instansi customer.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Daftar Instansi
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $institutions->total() }} instansi terdaftar.
                </p>
            </div>

            <a href="{{ route('institutions.create') }}"
                class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                + Tambah Instansi
            </a>

        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Instansi', 'column' => 'name'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Jenis', 'column' => 'type'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Kota', 'column' => 'city'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                @include('components.table-sort', ['label' => 'Customer', 'column' => 'customers_count'])
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @forelse ($institutions as $institution)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-slate-900">
                                        {{ $institution->name }}
                                    </div>

                                    @if ($institution->email)
                                        <div class="mt-1 text-sm text-slate-500">
                                            {{ $institution->email }}
                                        </div>
                                    @endif

                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @switch($institution->type)
                                        @case('government')
                                            Pemerintah
                                        @break

                                        @case('school')
                                            Sekolah
                                        @break

                                        @case('university')
                                            Perguruan Tinggi
                                        @break

                                        @case('company')
                                            Perusahaan
                                        @break

                                        @case('foundation')
                                            Yayasan
                                        @break

                                        @case('institution')
                                            Lembaga
                                        @break

                                        @default
                                            Lainnya
                                    @endswitch
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $institution->city ?: '-' }}

                                    @if ($institution->province)
                                        , {{ $institution->province }}
                                    @endif
                                </td>

                                <td class="px-6 py-4">

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $institution->customers_count }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('institutions.show', $institution) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('institutions.edit', $institution) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('institutions.destroy', $institution) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus instansi ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada instansi.
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Tambahkan instansi pertama.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $institutions->links() }}
                </div>

            </div>

        </div>

    @endsection
