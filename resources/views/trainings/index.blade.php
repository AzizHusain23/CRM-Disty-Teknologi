@extends('layouts.app')

@section('title', 'Pelatihan')

@section('page-heading', 'Pelatihan')

@section('page-description')
    Kelola program pelatihan Disty Akademi.
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


        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>

                <h2 class="text-xl font-semibold text-slate-900">
                    Daftar Pelatihan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola program, kategori, harga, dan durasi pelatihan.
                </p>

            </div>


            <div class="flex flex-wrap gap-2">

                <a href="{{ route('training-categories.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Kategori
                </a>

                <a href="{{ route('trainings.create') }}"
                    class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    + Tambah Pelatihan
                </a>

            </div>

        </div>


        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Pelatihan', 'column' => 'name'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Kategori', 'column' => 'category'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Harga', 'column' => 'price'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Durasi', 'column' => 'duration_hours'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Peserta', 'column' => 'registrations_count'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Status', 'column' => 'is_active'])
                            </th>

                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-200">

                        @forelse ($trainings as $training)
                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-5">

                                    <div class="font-semibold text-slate-900">
                                        {{ $training->name }}
                                    </div>

                                    @if ($training->description)
                                        <div class="mt-1 max-w-md truncate text-sm text-slate-500">
                                            {{ $training->description }}
                                        </div>
                                    @endif

                                </td>


                                <td class="px-6 py-5 text-sm text-slate-700">

                                    {{ $training->category?->name ?: '-' }}

                                </td>


                                <td class="px-6 py-5 text-sm font-medium text-slate-700">

                                    @if ($training->price !== null)
                                        Rp {{ number_format((float) $training->price, 0, ',', '.') }}
                                    @else
                                        Gratis / belum ditentukan
                                    @endif

                                </td>


                                <td class="px-6 py-5 text-sm text-slate-700">

                                    @if ($training->duration_hours)
                                        {{ number_format($training->duration_hours) }}
                                        jam
                                    @else
                                        -
                                    @endif

                                </td>


                                <td class="px-6 py-5 text-sm text-slate-700">

                                    {{ number_format($training->registrations_count) }}

                                </td>


                                <td class="px-6 py-5">

                                    @if ($training->is_active)
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            Nonaktif
                                        </span>
                                    @endif

                                </td>


                                <td class="px-6 py-5">

                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('trainings.show', $training) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Detail
                                        </a>

                                        <a href="{{ route('trainings.edit', $training) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>

                                        @if ($training->registrations_count === 0)
                                            <form method="POST" action="{{ route('trainings.destroy', $training) }}"
                                                onsubmit="return confirm('Hapus pelatihan {{ addslashes($training->name) }}?')">

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

                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada pelatihan.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="border-t border-slate-200 px-6 py-4">

                {{ $trainings->links() }}

            </div>

        </div>

    </div>

@endsection
