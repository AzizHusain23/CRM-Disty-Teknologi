@php
    $selectedCustomerId = old('customer_id', $selectedCustomerId ?? ($registration?->customer_id));
    $selectedTrainingId = old('training_id', $selectedTrainingId ?? ($registration?->training_id));
    $selectedStatus = old('status', $registration?->status ?? 'registered');
    $selectedAmount = old('amount', $registration?->amount);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
            <div class="font-semibold">Periksa kembali data berikut:</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="customer_id" class="mb-2 block text-sm font-medium text-slate-700">Customer *</label>
                <select id="customer_id" name="customer_id" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                    <option value="">Pilih customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected((int) $selectedCustomerId === (int) $customer->id)>
                            {{ $customer->name }} — {{ ucfirst($customer->status) }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-slate-500">Hanya Active dan Repeat Customer yang dapat dipilih.</p>
                @error('customer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="training_id" class="mb-2 block text-sm font-medium text-slate-700">Pelatihan *</label>
                <select id="training_id" name="training_id" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                    <option value="">Pilih pelatihan</option>
                    @foreach ($trainings as $training)
                        <option value="{{ $training->id }}" data-price="{{ $training->price !== null ? (float) $training->price : '' }}" @selected((int) $selectedTrainingId === (int) $training->id)>
                            {{ $training->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-slate-500">Pelatihan yang tampil adalah pelatihan aktif, kecuali pelatihan lama yang sedang diedit.</p>
                @error('training_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="training_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Training</label>
                <input id="training_date" type="date" name="training_date" value="{{ old('training_date', $registration?->training_date?->format('Y-m-d')) }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                @error('training_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status *</label>
                <select id="status" name="status" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="amount" class="mb-2 block text-sm font-medium text-slate-700">Amount</label>
                <input id="amount" type="number" name="amount" value="{{ $selectedAmount }}" step="0.01" min="0"
                    placeholder="Contoh: 750000" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                <p class="mt-2 text-xs text-slate-500">Saat memilih pelatihan, harga master akan ditawarkan sebagai nilai awal.</p>
                @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="registration_number" class="mb-2 block text-sm font-medium text-slate-700">No. Registrasi</label>
                <input id="registration_number" type="text" name="registration_number"
                    value="{{ old('registration_number', $registration?->registration_number) }}"
                    placeholder="Kosongkan untuk dibuat otomatis"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                @error('registration_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                <textarea id="notes" name="notes" rows="4" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"
                    placeholder="Catatan tambahan...">{{ old('notes', $registration?->notes) }}</textarea>
                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-2">
        <a href="{{ $registration ? route('registrations.index') : route('customers.index') }}"
            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Batal
        </a>
        <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
            {{ $buttonLabel }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const training = document.getElementById('training_id');
        const amount = document.getElementById('amount');

        if (!training || !amount) return;

        training.addEventListener('change', function () {
            const selected = training.options[training.selectedIndex];
            const price = selected?.dataset?.price;

            if (price !== undefined && price !== '') {
                amount.value = price;
            }
        });
    });
</script>
