@extends('layouts.app')

@section('title', 'Tambah Kategori Pelatihan')

@section('page-heading', 'Tambah Kategori Pelatihan')

@section('page-description')
    Buat kategori baru untuk mengelompokkan pelatihan.
@endsection

@section('content')

    <div class="mx-auto max-w-3xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            @if ($errors->any())

                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-5">

                    <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            @endif


            <form method="POST" action="{{ route('training-categories.store') }}" class="space-y-6">

                @csrf


                <div>

                    <label for="name" class="block text-sm font-semibold text-slate-700">
                        Nama Kategori
                    </label>

                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        placeholder="Contoh: Microsoft Office">

                </div>


                <div>

                    <label for="description" class="block text-sm font-semibold text-slate-700">
                        Deskripsi
                    </label>

                    <textarea id="description" name="description" rows="5"
                        class="mt-2 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        placeholder="Deskripsi kategori...">{{ old('description') }}</textarea>

                </div>


                <div>

                    <label class="inline-flex items-center gap-3">

                        <input type="checkbox" name="is_active" value="1" checked
                            class="h-4 w-4 rounded border-slate-300">

                        <span class="text-sm font-medium text-slate-700">
                            Kategori aktif
                        </span>

                    </label>

                </div>


                <div class="flex flex-wrap justify-between gap-3">
                    <a href="{{ route('trainings.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">← Kembali ke Pelatihan</a>
                    <div class="flex gap-3">

                    <a href="{{ route('training-categories.index') }}"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                        Simpan Kategori
                    </button>
                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
