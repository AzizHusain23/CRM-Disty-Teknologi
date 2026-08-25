@csrf

<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">

        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
            Nama Customer *
        </label>

        <input id="name" type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
            Email
        </label>

        <input id="email" type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">
            Nomor Telepon
        </label>

        <input id="phone" type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="document_number" class="mb-2 block text-sm font-medium text-slate-700">
            Nomor Dokumen
        </label>

        <input id="document_number" type="text" name="document_number"
            value="{{ old('document_number', $customer->document_number ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('document_number')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="company_id" class="mb-2 block text-sm font-medium text-slate-700">
            Perusahaan / Institusi
        </label>

        <select id="company_id" name="company_id"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

            <option value="">
                -- Tidak ada perusahaan --
            </option>

            @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected((string) old('company_id', $customer->company_id ?? '') === (string) $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach

        </select>

        @error('company_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="city" class="mb-2 block text-sm font-medium text-slate-700">
            Kota
        </label>

        <input id="city" type="text" name="city" value="{{ old('city', $customer->city ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('city')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="province" class="mb-2 block text-sm font-medium text-slate-700">
            Provinsi
        </label>

        <input id="province" type="text" name="province" value="{{ old('province', $customer->province ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

        @error('province')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">
            Status *
        </label>

        <select id="status" name="status" required
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

            @foreach ([
        'active' => 'Active',
        'prospect' => 'Prospect',
        'inactive' => 'Inactive',
        'repeat' => 'Repeat Customer',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $customer->status ?? 'active') === $value)>
                    {{ $label }}
                </option>
            @endforeach

        </select>

        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label for="source" class="mb-2 block text-sm font-medium text-slate-700">
            Sumber Data *
        </label>

        <select id="source" name="source" required
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">

            @foreach ([
        'manual' => 'Manual',
        'excel' => 'Excel',
        'academy' => 'Academy',
        'website' => 'Website',
        'campaign' => 'Campaign',
        'import' => 'Import',
    ] as $value => $label)
                <option value="{{ $value }}" @selected(old('source', $customer->source ?? 'manual') === $value)>
                    {{ $label }}
                </option>
            @endforeach

        </select>

        @error('source')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div class="md:col-span-2">

        <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">
            Catatan
        </label>

        <textarea id="notes" name="notes" rows="5"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10">{{ old('notes', $customer->notes ?? '') }}</textarea>

        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

</div>

<div class="mt-8 flex justify-end gap-3">

    <a href="{{ route('customers.index') }}"
        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Batal
    </a>

    <button type="submit"
        class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
        {{ $submitLabel }}
    </button>

</div>
