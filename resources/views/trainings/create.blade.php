@extends('layouts.app')

@section('title', 'Tambah Pelatihan')

@section('page-heading', 'Tambah Pelatihan')

@section('page-description')
    Buat program pelatihan baru.
@endsection

@section('content')

<div class="mx-auto max-w-4xl">

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


        <form
            method="POST"
            action="{{ route('trainings.store') }}"
            class="space-y-6"
        >

            @csrf


            <div>

                <label
                    for="training_category_id"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Kategori Pelatihan
                </label>

                <select
                    id="training_category_id"
                    name="training_category_id"
                    class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                >

                    <option value="">
                        Tanpa kategori
                    </option>

                    @foreach ($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            @selected(old('training_category_id') == $category->id)
                        >
                            {{ $category->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div>

                <label
                    for="name"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Nama Pelatihan
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="mt-2 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    placeholder="Contoh: Microsoft Excel Fundamental"
                >

            </div>


            <div>

                <label
                    for="description"
                    class="block text-sm font-semibold text-slate-700"
                >
                    Deskripsi
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="mt-2 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                    placeholder="Deskripsi pelatihan..."
                >{{ old('description') }}</textarea>

            </div>


            <div class="grid gap-6 md:grid-cols-2">

                <div>

                    <label
                        for="price"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Harga
                    </label>

                    <div class="mt-2 flex">

                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-4 text-sm text-slate-500">
                            Rp
                        </span>

                        <input
                            id="price"
                            name="price"
                            type="number"
                            min="0"
                            step="0.01"
                            value="{{ old('price') }}"
                            class="block w-full rounded-r-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                            placeholder="0"
                        >

                    </div>

                </div>


                <div>

                    <label
                        for="duration_hours"
                        class="block text-sm font-semibold text-slate-700"
                    >
                        Durasi
                    </label>

                    <div class="mt-2 flex">

                        <input
                            id="duration_hours"
                            name="duration_hours"
                            type="number"
                            min="1"
                            value="{{ old('duration_hours') }}"
                            class="block w-full rounded-l-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                            placeholder="8"
                        >

                        <span class="inline-flex items-center rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 px-4 text-sm text-slate-500">
                            jam
                        </span>

                    </div>

                </div>

            </div>


            <div>

                <label class="inline-flex items-center gap-3">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        checked
                        class="h-4 w-4 rounded border-slate-300"
                    >

                    <span class="text-sm font-medium text-slate-700">
                        Pelatihan aktif
                    </span>

                </label>

            </div>


            <div class="flex justify-end gap-3">

                <a
                    href="{{ route('trainings.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
                >
                    Simpan Pelatihan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
