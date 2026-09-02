@csrf

<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">

        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
            Nama Instansi *
        </label>

        <input id="name" type="text" name="name" value="{{ old('name', $institution->name ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="type" class="mb-2 block text-sm font-medium text-slate-700">
            Jenis Instansi *
        </label>

        <select id="type" name="type" required
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

            @foreach ([
        'government' => 'Pemerintah',
        'school' => 'Sekolah',
        'university' => 'Perguruan Tinggi',
        'company' => 'Perusahaan',
        'foundation' => 'Yayasan',
        'institution' => 'Lembaga',
        'other' => 'Lainnya',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $institution->type ?? '') === $value)>
                    {{ $label }}
                </option>
            @endforeach

        </select>

        @error('type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
            Email
        </label>

        <input id="email" type="email" name="email" value="{{ old('email', $institution->email ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">
            Telepon
        </label>

        <input id="phone" type="text" name="phone" value="{{ old('phone', $institution->phone ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

    </div>

    <div>

        <label for="city" class="mb-2 block text-sm font-medium text-slate-700">
            Kota
        </label>

        <input id="city" type="text" name="city" value="{{ old('city', $institution->city ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

    </div>

    <div>

        <label for="province" class="mb-2 block text-sm font-medium text-slate-700">
            Provinsi
        </label>

        <input id="province" type="text" name="province" value="{{ old('province', $institution->province ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

    </div>

    <div>

        <label for="industry" class="mb-2 block text-sm font-medium text-slate-700">
            Bidang / Industri
        </label>

        <input id="industry" type="text" name="industry"
            value="{{ old('industry', $institution->industry ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

    </div>

    <div class="md:col-span-2">

        <label for="address" class="mb-2 block text-sm font-medium text-slate-700">
            Alamat
        </label>

        <textarea id="address" name="address" rows="3"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">{{ old('address', $institution->address ?? '') }}</textarea>

    </div>

    <div class="md:col-span-2">

        <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">
            Catatan
        </label>

        <textarea id="notes" name="notes" rows="4"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">{{ old('notes', $institution->notes ?? '') }}</textarea>

    </div>

</div>

<div class="mt-8 flex justify-end gap-3">

    <a href="{{ route('institutions.index') }}"
        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Batal
    </a>

    <button type="submit"
        class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
        {{ $submitLabel }}
    </button>

</div>
