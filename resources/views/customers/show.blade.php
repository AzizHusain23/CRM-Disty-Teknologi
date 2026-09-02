@extends('layouts.app')

@section('title', $customer->name)

@section('page-heading', $customer->name)

@section('page-description')
    Detail, histori kontak, dan histori pelatihan customer.
@endsection

@section('content')
    @php
        $statusClasses = match ($customer->status) {
            'active' => 'bg-green-100 text-green-700',
            'prospect' => 'bg-amber-100 text-amber-700',
            'inactive' => 'bg-slate-100 text-slate-600',
            'repeat' => 'bg-blue-100 text-blue-700',
            default => 'bg-slate-100 text-slate-600',
        };

        $statusLabels = [
            'active' => 'Active',
            'prospect' => 'Prospect',
            'inactive' => 'Inactive',
            'repeat' => 'Repeat Customer',
        ];

        $activityTypes = [
            'phone_call' => 'Phone Call',
            'whatsapp' => 'WhatsApp',
            'meeting' => 'Meeting',
            'visit' => 'Kunjungan',
            'note' => 'Catatan',
        ];
    @endphp

    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold text-slate-900">{{ $customer->name }}</h2>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                        {{ $statusLabels[$customer->status] ?? ucfirst($customer->status) }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-slate-500">{{ $customer->customer_code }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($customer->status === 'prospect')
                    <form method="POST" action="{{ route('customers.activate', $customer) }}"
                        onsubmit="return confirm('Konversi customer ini menjadi Active? Pastikan customer sudah berhasil dikonfirmasi.')">
                        @csrf
                        <button type="submit"
                            class="rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700 hover:bg-green-100">
                            Jadikan Active
                        </button>
                    </form>
                @elseif ($customer->status === 'active')
                    <form method="POST" action="{{ route('customers.deactivate', $customer) }}"
                        onsubmit="return confirm('Ubah customer ini menjadi Inactive?')">
                        @csrf
                        <button type="submit"
                            class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 hover:bg-amber-100">
                            Jadikan Inactive
                        </button>
                    </form>
                @elseif ($customer->status === 'inactive')
                    <form method="POST" action="{{ route('customers.activate', $customer) }}"
                        onsubmit="return confirm('Aktifkan kembali customer ini menjadi Active?')">
                        @csrf
                        <button type="submit"
                            class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-100">
                            Aktifkan Kembali
                        </button>
                    </form>
                @endif

                <a href="{{ route('customers.edit', $customer) }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>

                <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                    onsubmit="return confirm('Yakin ingin menghapus customer {{ addslashes($customer->name) }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">Hapus</button>
                </form>

                <a href="{{ route('customers.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Kembali</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Informasi Customer</h3>

                    <dl class="mt-6 space-y-4">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Nama</dt>
                            <dd class="mt-1 text-sm text-slate-800">{{ $customer->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Email</dt>
                            <dd class="mt-1 text-sm text-slate-800">{{ $customer->email ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Telepon</dt>
                            <dd class="mt-1 text-sm text-slate-800">{{ $customer->phone ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Nomor Dokumen</dt>
                            <dd class="mt-1 text-sm text-slate-800">{{ $customer->document_number ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Instansi</dt>
                            <dd class="mt-1 text-sm text-slate-800">
                                @if ($customer->institution)
                                    <a href="{{ route('institutions.show', $customer->institution) }}"
                                        class="font-medium text-slate-900 hover:underline">
                                        {{ $customer->institution->name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Lokasi</dt>
                            <dd class="mt-1 text-sm text-slate-800">
                                {{ $customer->city ?: '-' }}{{ $customer->province ? ', '.$customer->province : '' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase text-slate-400">Sumber</dt>
                            <dd class="mt-1 text-sm capitalize text-slate-800">{{ $customer->source }}</dd>
                        </div>
                    </dl>
                </div>

                @if ($customer->status === 'prospect')
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                        <h3 class="text-base font-semibold text-amber-900">Lifecycle Prospect</h3>
                        <p class="mt-2 text-sm leading-6 text-amber-800">
                            Catat komunikasi dengan customer. Setelah customer dikonfirmasi/berhasil dikonversi,
                            gunakan opsi <strong>Jadikan Active</strong> saat menyimpan aktivitas.
                        </p>
                    </div>
                @endif
            </div>

            <div class="space-y-6 lg:col-span-2">

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900">Catat Aktivitas</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Semua komunikasi dengan customer disimpan sebagai histori record.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('customers.activities.store', $customer) }}" class="mt-6">
                        @csrf

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Jenis Aktivitas *</label>
                                <select id="type" name="type" required
                                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                                    @foreach ($activityTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="activity_at" class="mb-2 block text-sm font-medium text-slate-700">Tanggal & Waktu *</label>
                                <input id="activity_at" type="datetime-local" name="activity_at"
                                    value="{{ old('activity_at', now()->format('Y-m-d\TH:i')) }}" required
                                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                                @error('activity_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="subject" class="mb-2 block text-sm font-medium text-slate-700">Subjek</label>
                                <input id="subject" type="text" name="subject" value="{{ old('subject') }}"
                                    placeholder="Contoh: Follow up kebutuhan pelatihan"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                                @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                                <textarea id="description" name="description" rows="4"
                                    placeholder="Tuliskan hasil komunikasi..."
                                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">{{ old('description') }}</textarea>
                                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs text-slate-500">
                                @if ($customer->status === 'prospect')
                                    Centang opsi aktivasi hanya ketika customer benar-benar sudah dikonversi.
                                @else
                                    Aktivitas dicatat sebagai histori dan tidak mengubah status customer secara otomatis.
                                @endif
                            </p>

                            <div class="flex flex-wrap gap-2">
                                <button type="submit"
                                    class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                    Simpan Aktivitas
                                </button>

                                @if ($customer->status === 'prospect')
                                    <label class="inline-flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700">
                                        <input type="checkbox" name="activate_customer" value="1"
                                            class="rounded border-green-300 text-green-600 focus:ring-green-500">
                                        Simpan & Jadikan Active
                                    </label>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="text-base font-semibold text-slate-900">Histori Pelatihan</h3>
                        <div class="mt-1 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-sm text-slate-500">
                                @if (in_array($customer->status, ['active', 'repeat'], true))
                                    Customer dapat dicatat mengikuti pelatihan melalui tombol di sebelah kanan.
                                @else
                                    Prospect/Inactive belum dapat dicatat masuk ke pelatihan.
                                @endif
                            </p>

                            @if (in_array($customer->status, ['active', 'repeat'], true))
                                <a href="{{ route('registrations.create', ['customer_id' => $customer->id]) }}"
                                    class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                    + Daftarkan Training
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse ($customer->registrations as $registration)
                            <div class="px-6 py-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $registration->training->name }}</div>
                                        <div class="mt-1 text-sm text-slate-500">
                                            {{ $registration->training_date?->format('d M Y') ?: 'Tanggal belum tersedia' }}
                                            · {{ $registration->registration_number ?: 'Tanpa nomor registrasi' }}
                                        </div>
                                        @if ($registration->amount !== null)
                                            <div class="mt-1 text-xs text-slate-400">Rp {{ number_format((float) $registration->amount, 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $registrationStatusClasses = match ($registration->status) {
                                                'completed' => 'bg-green-100 text-green-700',
                                                'confirmed' => 'bg-blue-100 text-blue-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                                default => 'bg-amber-100 text-amber-700',
                                            };
                                            $registrationStatusLabels = [
                                                'registered' => 'Registered',
                                                'confirmed' => 'Confirmed',
                                                'completed' => 'Completed',
                                                'cancelled' => 'Cancelled',
                                            ];
                                        @endphp
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $registrationStatusClasses }}">
                                            {{ $registrationStatusLabels[$registration->status] ?? ucfirst($registration->status) }}
                                        </span>
                                        <a href="{{ route('registrations.edit', $registration) }}"
                                            class="mt-2 inline-block text-xs font-medium text-slate-500 hover:text-slate-900 hover:underline">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada histori pelatihan.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="text-base font-semibold text-slate-900">Aktivitas</h3>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse ($customer->activities->sortByDesc('activity_at') as $activity)
                            <div class="px-6 py-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-slate-900">
                                            {{ $activityTypes[$activity->type] ?? ucfirst(str_replace('_', ' ', $activity->type)) }}
                                        </div>
                                        @if ($activity->subject)
                                            <div class="mt-1 text-sm font-medium text-slate-700">{{ $activity->subject }}</div>
                                        @endif
                                        @if ($activity->description)
                                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $activity->description }}</p>
                                        @endif
                                        <div class="mt-2 text-xs text-slate-400">
                                            Dicatat oleh {{ $activity->user?->name ?: 'System' }}
                                        </div>
                                    </div>
                                    <div class="text-right text-xs text-slate-400">
                                        {{ $activity->activity_at?->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-sm text-slate-500">Belum ada aktivitas.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-5">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">Follow Up</h3>
                            <p class="mt-1 text-sm text-slate-500">Jadwalkan tindakan berikutnya agar histori customer tetap terdokumentasi.</p>
                        </div>
                        <a href="{{ route('follow-ups.create', ['customer_id' => $customer->id]) }}"
                            class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                            + Buat Follow Up
                        </a>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse ($customer->followUps->sortBy('follow_up_at') as $followUp)
                            <div class="px-6 py-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $followUp->title }}</div>
                                        @if ($followUp->description)
                                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $followUp->description }}</p>
                                        @endif
                                        <div class="mt-2 text-xs text-slate-400">
                                            PIC: {{ $followUp->assignedUser?->name ?: 'Belum ditentukan' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-slate-700">
                                            {{ $followUp->follow_up_at?->format('d M Y H:i') }}
                                        </div>
                                        <div class="mt-1 text-xs capitalize text-slate-400">{{ $followUp->status }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-sm text-slate-500">Belum ada follow up.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
