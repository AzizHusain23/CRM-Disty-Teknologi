@csrf

<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
            Nama Perusahaan / Institusi *
        </label>

        <input id="name" type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
            Email
        </label>

        <input id="email" type="email" name="email" value="{{ old('email', $company->email ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">
            Telepon
        </label>

        <input id="phone" type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="city" class="mb-2 block text-sm font-medium text-slate-700">
            Kota
        </label>

        <input id="city" type="text" name="city" value="{{ old('city', $company->city ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('city')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="province" class="mb-2 block text-sm font-medium text-slate-700">
            Provinsi
        </label>

        <input id="province" type="text" name="province" value="{{ old('province', $company->province ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('province')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="industry" class="mb-2 block text-sm font-medium text-slate-700">
            Industri
        </label>

        <input id="industry" type="text" name="industry" value="{{ old('industry', $company->industry ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('industry')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="address" class="mb-2 block text-sm font-medium text-slate-700">
            Alamat
        </label>

        <textarea id="address" name="address" rows="3"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">{{ old('address', $company->address ?? '') }}</textarea>

        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">
            Catatan
        </label>

        <textarea id="notes" name="notes" rows="4"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">{{ old('notes', $company->notes ?? '') }}</textarea>

        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('companies.index') }}"
        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Batal
    </a>

    <button type="submit"
        class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
        {{ $submitLabel }}
    </button>
</div>
