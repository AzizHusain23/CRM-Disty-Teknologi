@extends('layouts.app')

@section('title', 'Aktivitas')
@section('page-heading', 'Aktivitas')
@section('page-description') Histori seluruh komunikasi dan aktivitas customer. @endsection

@section('content')
@php
    $typeLabels = [
        'phone_call' => 'Phone Call',
        'whatsapp' => 'WhatsApp',
        'meeting' => 'Meeting',
        'visit' => 'Kunjungan',
        'note' => 'Catatan',
    ];
@endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Histori Aktivitas</h2>
            <p class="mt-1 text-sm text-slate-500">{{ number_format($activities->total()) }} aktivitas ditemukan.</p>
        </div>
    </div>

    <form method="GET" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700" for="search">Cari</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Customer, kode customer, subjek, deskripsi..." class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="type">Jenis</label>
                <select id="type" name="type" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($typeLabels as $value => $label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="user_id">Dicatat Oleh</label>
                <select id="user_id" name="user_id" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm">
                    <option value="">Semua User</option>
                    @foreach($users as $user)<option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <a href="{{ route('activities.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Terapkan Filter</button>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
            <p class="text-sm text-slate-500">Klik judul kolom untuk mengurutkan.</p>
            <form method="GET" class="flex items-center gap-2 text-sm">
                @foreach(request()->except('per_page','page') as $key=>$value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach
                <label for="per_page" class="text-slate-500">Tampilkan</label>
                <select id="per_page" name="per_page" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach($perPageOptions as $option)<option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>@endforeach
                </select><span class="text-slate-500">data</span>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50"><tr>
                    @foreach([
                        ['Tanggal','activity_at'],['Jenis','type'],['Customer','customer'],['Subjek','subject'],['Dicatat Oleh','user']
                    ] as [$label,$column])
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">@include('components.table-sort',['label'=>$label,'column'=>$column])</th>
                    @endforeach
                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-200">
                @forelse($activities as $activity)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $activity->activity_at?->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $typeLabels[$activity->type] ?? ucfirst($activity->type) }}</td>
                        <td class="px-6 py-4"><a href="{{ route('customers.show',$activity->customer) }}" class="font-semibold text-slate-900 hover:underline">{{ $activity->customer?->name }}</a><div class="text-xs text-slate-400">{{ $activity->customer?->customer_code }}</div></td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $activity->subject ?: '-' }}<div class="mt-1 max-w-md truncate text-xs text-slate-400">{{ $activity->description }}</div></td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $activity->user?->name ?: 'System' }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('customers.show',$activity->customer) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada aktivitas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-6 py-4">{{ $activities->links() }}</div>
    </div>
</div>
@endsection
