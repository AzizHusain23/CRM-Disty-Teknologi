@extends('layouts.app')
@section('title','Dashboard')
@section('page-heading','Dashboard')
@section('page-description') Ringkasan operasional CRM dan aktivitas customer Disty Akademi. @endsection
@section('content')
<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Customer</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($customerTotal) }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ number_format($newCustomersThisMonth) }} customer baru bulan ini</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Prospect</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">{{ number_format($prospectTotal) }}</p>
            <p class="mt-2 text-xs text-amber-700">Menunggu kontak / konfirmasi</p>
        </div>
        <div class="rounded-2xl border border-green-200 bg-green-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-green-700">Active</p>
            <p class="mt-2 text-3xl font-bold text-green-900">{{ number_format($activeTotal) }}</p>
            <p class="mt-2 text-xs text-green-700">Customer aktif saat ini</p>
        </div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-blue-700">Repeat Customer</p>
            <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format($repeatTotal) }}</p>
            <p class="mt-2 text-xs text-blue-700">Pernah kembali menggunakan layanan</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-4">
        <a href="{{ route('institutions.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow">
            <p class="text-sm text-slate-500">Instansi</p><p class="mt-2 text-2xl font-bold">{{ number_format($institutionTotal) }}</p>
        </a>
        <a href="{{ route('trainings.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow">
            <p class="text-sm text-slate-500">Pelatihan</p><p class="mt-2 text-2xl font-bold">{{ number_format($trainingTotal) }}</p>
        </a>
        <a href="{{ route('follow-ups.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow">
            <p class="text-sm text-slate-500">Follow Up Hari Ini</p><p class="mt-2 text-2xl font-bold">{{ number_format($todayFollowUpTotal) }}</p><p class="mt-1 text-xs text-slate-500">{{ number_format($overdueFollowUpTotal) }} terlambat</p>
        </a>
        <a href="{{ route('activities.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow">
            <p class="text-sm text-slate-500">Aktivitas Hari Ini</p><p class="mt-2 text-2xl font-bold">{{ number_format($todayActivityTotal) }}</p>
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between"><div><h2 class="text-base font-semibold">Aktivitas 6 Bulan Terakhir</h2><p class="mt-1 text-sm text-slate-500">Jumlah histori kontak yang dicatat.</p></div><a href="{{ route('reports.index') }}" class="text-sm font-medium text-slate-700 hover:underline">Lihat laporan</a></div>
            <div class="mt-6 space-y-4">
                @foreach($sixMonthActivity as $month)
                    <div class="grid grid-cols-12 items-center gap-3">
                        <div class="col-span-3 text-sm text-slate-500">{{ $month['label'] }}</div>
                        <div class="col-span-8"><div class="h-3 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-slate-700" style="width: {{ ($month['total'] / $maxMonthlyActivity) * 100 }}%"></div></div></div>
                        <div class="col-span-1 text-right text-sm font-semibold">{{ number_format($month['total']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold">Ringkasan Periode Ini</h2>
            <div class="mt-5 space-y-4">
                <div><p class="text-sm text-slate-500">Customer baru</p><p class="mt-1 text-2xl font-bold">{{ number_format($newCustomersThisMonth) }}</p></div>
                <div><p class="text-sm text-slate-500">Registrasi bulan ini</p><p class="mt-1 text-2xl font-bold">{{ number_format($registrationsThisMonth) }}</p></div>
                <div><p class="text-sm text-slate-500">Total registrasi</p><p class="mt-1 text-2xl font-bold">{{ number_format($registrationTotal) }}</p></div>
                <div><p class="text-sm text-slate-500">Rasio active + repeat</p><p class="mt-1 text-2xl font-bold">{{ number_format($conversionRate, 1) }}%</p></div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between"><div><h2 class="text-base font-semibold">Follow Up Berikutnya</h2><p class="mt-1 text-sm text-slate-500">Prioritas pekerjaan yang sudah dijadwalkan.</p></div><a href="{{ route('follow-ups.index') }}" class="text-sm font-medium text-slate-700 hover:underline">Kelola</a></div>
            <div class="mt-5 divide-y divide-slate-100">
                @forelse($upcomingFollowUps as $followUp)
                    <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                        <div class="min-w-0"><a href="{{ route('customers.show', $followUp->customer) }}" class="font-medium text-slate-900 hover:underline">{{ $followUp->customer->name }}</a><p class="truncate text-sm text-slate-500">{{ $followUp->title }}</p></div>
                        <div class="shrink-0 text-right"><p class="text-sm font-medium">{{ $followUp->follow_up_at?->format('d/m H:i') }}</p><p class="text-xs text-slate-500">{{ $followUp->priority }}</p></div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada follow up mendatang.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between"><div><h2 class="text-base font-semibold">Aktivitas Terbaru</h2><p class="mt-1 text-sm text-slate-500">Histori record keeping terakhir.</p></div><a href="{{ route('activities.index') }}" class="text-sm font-medium text-slate-700 hover:underline">Lihat semua</a></div>
            <div class="mt-5 divide-y divide-slate-100">
                @forelse($recentActivities as $activity)
                    <div class="py-3 first:pt-0 last:pb-0"><div class="flex items-center justify-between gap-4"><a href="{{ route('customers.show', $activity->customer) }}" class="font-medium text-slate-900 hover:underline">{{ $activity->customer->name }}</a><span class="text-xs text-slate-500">{{ $activity->activity_at?->format('d/m/Y H:i') }}</span></div><p class="mt-1 text-sm text-slate-600">{{ $activity->subject ?: ucfirst($activity->type) }}</p></div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada aktivitas tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold">Jenis Aktivitas Terbanyak</h2>
            <div class="mt-5 space-y-3">
                @forelse($activityTypeCounts as $type => $total)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"><span class="text-sm font-medium text-slate-700">{{ ucfirst($type) }}</span><span class="text-sm font-bold">{{ number_format($total) }}</span></div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data aktivitas.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold">Status Customer</h2>
            <div class="mt-5 grid grid-cols-2 gap-3">
                @foreach([['label'=>'Prospect','value'=>$prospectTotal,'class'=>'bg-amber-50 text-amber-900'],['label'=>'Active','value'=>$activeTotal,'class'=>'bg-green-50 text-green-900'],['label'=>'Inactive','value'=>$inactiveTotal,'class'=>'bg-slate-100 text-slate-800'],['label'=>'Repeat','value'=>$repeatTotal,'class'=>'bg-blue-50 text-blue-900']] as $status)
                    <div class="rounded-xl p-4 {{ $status['class'] }}"><p class="text-sm font-medium">{{ $status['label'] }}</p><p class="mt-1 text-2xl font-bold">{{ number_format($status['value']) }}</p></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
