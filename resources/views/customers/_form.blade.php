@csrf

<div class="grid gap-6 md:grid-cols-2">

    <div class="md:col-span-2">

        <label
            for="name"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Nama Customer *
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $customer->name ?? '') }}"
            required
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label
            for="email"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Email
        </label>

        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $customer->email ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >

        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label
            for="phone"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Nomor Telepon
        </label>

        <input
            id="phone"
            type="text"
            name="phone"
            value="{{ old('phone', $customer->phone ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >

    </div>

    <div>

        <label
            for="document_number"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Nomor Dokumen
        </label>

        <input
            id="document_number"
            type="text"
            name="document_number"
            value="{{ old('document_number', $customer->document_number ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >

    </div>

    <div>

        <label
            for="institution_id"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Instansi
        </label>

        <select
            id="institution_id"
            name="institution_id"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >

            <option value="">
                -- Tidak ada instansi --
            </option>

            @foreach ($institutions as $institution)

                <option
                    value="{{ $institution->id }}"
                    @selected(
                        (string) old(
                            'institution_id',
                            $customer->institution_id ?? ''
                        ) === (string) $institution->id
                    )
                >
                    {{ $institution->name }}
                </option>

            @endforeach

        </select>

        @error('institution_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

    </div>

    <div>

        <label
            for="city"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Kota
        </label>

        <input
            id="city"
            type="text"
            name="city"
            value="{{ old('city', $customer->city ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >

    </div>

    <div>

        <label
            for="province"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Provinsi
        </label>

        <input
            id="province"
            type="text"
            name="province"
            value="{{ old('province', $customer->province ?? '') }}"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >

    </div>

    @if ($customer)
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Status Lifecycle</label>
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                {{ [
                    'active' => 'Active',
                    'prospect' => 'Prospect',
                    'inactive' => 'Inactive',
                    'repeat' => 'Repeat Customer',
                ][$customer->status] ?? ucfirst($customer->status) }}
            </div>
            <p class="mt-2 text-xs leading-5 text-slate-500">
                Status tidak diubah dari form data. Gunakan aksi lifecycle pada halaman detail agar histori customer tetap terjaga.
            </p>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Sumber Data</label>
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                {{ [
                    'manual' => 'Manual',
                    'excel' => 'Excel',
                    'academy' => 'Academy',
                    'website' => 'Website',
                    'campaign' => 'Campaign (legacy)',
                    'import' => 'Import',
                ][$customer->source] ?? ucfirst($customer->source) }}
            </div>
            <p class="mt-2 text-xs leading-5 text-slate-500">
                Sumber data bersifat historis dan tidak diubah saat memperbarui profil.
            </p>
        </div>
    @else
        <div class="md:col-span-2 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-semibold text-amber-900">Lifecycle awal customer</p>
            <p class="mt-1 text-sm leading-6 text-amber-800">
                Customer baru otomatis disimpan sebagai <strong>Prospect</strong> dan sumber data
                <strong>Manual</strong>. Setelah customer dihubungi dan dikonfirmasi, status dapat diubah menjadi Active.
            </p>
        </div>
    @endif

    <div class="md:col-span-2">

        <label
            for="notes"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Catatan
        </label>

        <textarea
            id="notes"
            name="notes"
            rows="5"
            class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10"
        >{{ old('notes', $customer->notes ?? '') }}</textarea>

    </div>

</div>

<div class="mt-8 flex justify-end gap-3">

    <a
        href="{{ route('customers.index') }}"
        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
    >
        Batal
    </a>

    <button
        type="submit"
        class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
    >
        {{ $submitLabel }}
    </button>

</div>