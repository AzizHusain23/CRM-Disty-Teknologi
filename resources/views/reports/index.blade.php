@extends('layouts.app')
@section('title','Laporan')
@section('page-heading','Laporan')
@section('page-description') Ringkasan dan analisis data CRM berdasarkan periode yang dipilih. @endsection
@section('content')
<div class="space-y-6">
    <form method="GET" action="{{ route('reports.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3 md:items-end">
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Mulai</label><input type="date" name="start_date" value="{{ $start->toDateString() }}" class="w-full rounded-lg border-slate-300 text-sm"></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Akhir</label><input type="date" name="end_date" value="{{ $end->toDateString() }}" class="w-full rounded-lg border-slate-300 text-sm"></div>
            <div class="flex gap-2"><button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Terapkan</button><a href="{{ route('reports.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a></div>
        </div>
        <p class="mt-3 text-xs text-slate-500">Periode laporan: {{ $start->format('d/m/Y H:i') }} – {{ $end->format('d/m/Y H:i') }}</p>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Customer Baru</p><p class="mt-2 text-2xl font-bold">{{ number_format($newCustomers) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Aktivitas</p><p class="mt-2 text-2xl font-bold">{{ number_format($activities) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Follow Up</p><p class="mt-2 text-2xl font-bold">{{ number_format($followUps) }}</p><p class="mt-1 text-xs text-slate-500">{{ number_format($followUpCompletionRate, 1) }}% selesai</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Registrasi</p><p class="mt-2 text-2xl font-bold">{{ number_format($registrations) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Nilai Registrasi</p><p class="mt-2 text-2xl font-bold">Rp {{ number_format($registrationRevenue, 0, ',', '.') }}</p></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-base font-semibold">Status Customer Saat Ini</h2><div class="mt-5 grid grid-cols-2 gap-3">@foreach(['prospect'=>'Prospect','active'=>'Active','inactive'=>'Inactive','repeat'=>'Repeat'] as $key=>$label)<div class="rounded-xl bg-slate-50 p-4"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-1 text-2xl font-bold">{{ number_format((int)($customerStatusCounts[$key] ?? 0)) }}</p></div>@endforeach</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-base font-semibold">Status Follow Up pada Periode</h2><div class="mt-5 space-y-3">@forelse($followUpStatusCounts as $status=>$total)<div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"><span class="text-sm font-medium">{{ ucfirst($status) }}</span><span class="font-bold">{{ number_format($total) }}</span></div>@empty<p class="text-sm text-slate-500">Tidak ada follow up pada periode ini.</p>@endforelse</div></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-base font-semibold">Aktivitas berdasarkan Jenis</h2><div class="mt-5 space-y-3">@forelse($activityTypeCounts as $type=>$total)<div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"><span class="text-sm font-medium">{{ ucfirst($type) }}</span><span class="font-bold">{{ number_format($total) }}</span></div>@empty<p class="text-sm text-slate-500">Tidak ada aktivitas pada periode ini.</p>@endforelse</div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-base font-semibold">Registrasi berdasarkan Status</h2><div class="mt-5 space-y-3">@forelse($registrationStatusCounts as $status=>$total)<div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"><span class="text-sm font-medium">{{ ucfirst($status) }}</span><span class="font-bold">{{ number_format($total) }}</span></div>@empty<p class="text-sm text-slate-500">Tidak ada registrasi pada periode ini.</p>@endforelse</div></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-base font-semibold">Pelatihan Paling Banyak Diikuti</h2><div class="mt-5 overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="border-b text-left text-slate-500"><th class="px-3 py-3 font-medium">Pelatihan</th><th class="px-3 py-3 text-right font-medium">Registrasi</th></tr></thead><tbody>@forelse($topTrainings as $training)<tr class="border-b last:border-0"><td class="px-3 py-3 font-medium">{{ $training->name }}</td><td class="px-3 py-3 text-right font-bold">{{ number_format($training->registrations_count) }}</td></tr>@empty<tr><td colspan="2" class="px-3 py-6 text-center text-slate-500">Belum ada data.</td></tr>@endforelse</tbody></table></div></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h2 class="text-base font-semibold">Instansi dengan Customer Baru Terbanyak</h2><div class="mt-5 overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="border-b text-left text-slate-500"><th class="px-3 py-3 font-medium">Instansi</th><th class="px-3 py-3 text-right font-medium">Customer Baru</th></tr></thead><tbody>@forelse($topInstitutions as $institution)<tr class="border-b last:border-0"><td class="px-3 py-3 font-medium">{{ $institution->name }}</td><td class="px-3 py-3 text-right font-bold">{{ number_format($institution->customers_count) }}</td></tr>@empty<tr><td colspan="2" class="px-3 py-6 text-center text-slate-500">Belum ada data pada periode ini.</td></tr>@endforelse</tbody></table></div></div>
    </div>
</div>
@endsection
