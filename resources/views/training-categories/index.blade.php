@extends('layouts.app')

@section('title', 'Kategori Pelatihan')

@section('page-heading', 'Kategori Pelatihan')

@section('page-description')
    Kelola kategori pelatihan yang tersedia di Disty Akademi.
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
                    Daftar Kategori
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Kategori digunakan untuk mengelompokkan pelatihan.
                </p>

            </div>


            <a href="{{ route('training-categories.create') }}"
                class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                + Tambah Kategori
            </a>

        </div>


        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Kategori', 'column' => 'name'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Deskripsi', 'column' => 'description'])
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                @include('components.table-sort', ['label' => 'Pelatihan', 'column' => 'trainings_count'])
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

                        @forelse ($categories as $category)
                            <tr class="hover:bg-slate-50">

                                <td class="px-6 py-5">

                                    <div class="font-semibold text-slate-900">
                                        {{ $category->name }}
                                    </div>

                                </td>


                                <td class="max-w-md px-6 py-5 text-sm text-slate-600">

                                    {{ $category->description ?: '-' }}

                                </td>


                                <td class="px-6 py-5 text-sm text-slate-700">

                                    {{ number_format($category->trainings_count) }}

                                    pelatihan

                                </td>


                                <td class="px-6 py-5">

                                    @if ($category->is_active)
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

                                        <a href="{{ route('training-categories.edit', $category) }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>


                                        @if ($category->trainings_count === 0)
                                            <form method="POST"
                                                action="{{ route('training-categories.destroy', $category) }}"
                                                onsubmit="return confirm('Hapus kategori {{ addslashes($category->name) }}?')">

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

                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                    Belum ada kategori pelatihan.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="border-t border-slate-200 px-6 py-4">

                {{ $categories->links() }}

            </div>

        </div>

    </div>

@endsection
