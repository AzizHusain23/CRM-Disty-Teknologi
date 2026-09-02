@extends('layouts.app')
@section('title','Follow Up')
@section('page-heading','Follow Up')
@section('page-description') Kelola jadwal tindak lanjut customer. @endsection
@section('content')
@php
$statusLabels=['pending'=>'Pending','completed'=>'Completed','cancelled'=>'Cancelled'];
$priorityLabels=['low'=>'Low','normal'=>'Normal','high'=>'High'];
@endphp
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4"><div><h2 class="text-xl font-semibold text-slate-900">Daftar Follow Up</h2><p class="mt-1 text-sm text-slate-500">{{ number_format($followUps->total()) }} follow up ditemukan.</p></div><a href="{{ route('follow-ups.create') }}" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">+ Buat Follow Up</a></div>
    <form method="GET" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-5">
            <div class="lg:col-span-2"><label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Judul, customer, kode customer..." class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></div>
            <div><label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label><select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"><option value="">Semua</option>@foreach($statusLabels as $value=>$label)<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>@endforeach</select></div>
            <div><label for="priority" class="mb-2 block text-sm font-medium text-slate-700">Prioritas</label><select id="priority" name="priority" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"><option value="">Semua</option>@foreach($priorityLabels as $value=>$label)<option value="{{ $value }}" @selected(request('priority')===$value)>{{ $label }}</option>@endforeach</select></div>
            <div><label for="assigned_to" class="mb-2 block text-sm font-medium text-slate-700">PIC</label><select id="assigned_to" name="assigned_to" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"><option value="">Semua PIC</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)request('assigned_to')===(string)$user->id)>{{ $user->name }}</option>@endforeach</select></div>
        </div>
        <div class="mt-4 flex justify-end gap-2"><a href="{{ route('follow-ups.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a><button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Terapkan Filter</button></div>
    </form>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4"><p class="text-sm text-slate-500">Klik judul kolom untuk mengurutkan.</p><form method="GET" class="flex items-center gap-2 text-sm">@foreach(request()->except('per_page','page') as $key=>$value)<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endforeach<label for="per_page" class="text-slate-500">Tampilkan</label><select id="per_page" name="per_page" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">@foreach($perPageOptions as $option)<option value="{{ $option }}" @selected($perPage===$option)>{{ $option }}</option>@endforeach</select><span class="text-slate-500">data</span></form></div>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50"><tr>
            @foreach([['Jadwal','follow_up_at'],['Judul','title'],['Customer','customer'],['Prioritas','priority'],['Status','status'],['PIC','assigned_to']] as [$label,$column])<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">@include('components.table-sort',['label'=>$label,'column'=>$column])</th>@endforeach
            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
        </tr></thead><tbody class="divide-y divide-slate-200">
        @forelse($followUps as $followUp)
            @php $isOverdue=$followUp->status==='pending' && $followUp->follow_up_at?->isPast(); @endphp
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4"><div class="text-sm font-semibold {{ $isOverdue ? 'text-red-600':'text-slate-800' }}">{{ $followUp->follow_up_at?->format('d M Y H:i') }}</div>@if($isOverdue)<div class="mt-1 text-xs font-medium text-red-500">Terlambat</div>@endif</td>
                <td class="px-6 py-4 text-sm text-slate-800"><div class="font-semibold">{{ $followUp->title }}</div>@if($followUp->description)<div class="mt-1 max-w-xs truncate text-xs text-slate-400">{{ $followUp->description }}</div>@endif</td>
                <td class="px-6 py-4"><a href="{{ route('customers.show',$followUp->customer) }}" class="font-semibold text-slate-900 hover:underline">{{ $followUp->customer?->name }}</a></td>
                <td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $followUp->priority==='high'?'bg-red-100 text-red-700':($followUp->priority==='low'?'bg-slate-100 text-slate-600':'bg-amber-100 text-amber-700') }}">{{ $priorityLabels[$followUp->priority] }}</span></td>
                <td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $followUp->status==='completed'?'bg-green-100 text-green-700':($followUp->status==='cancelled'?'bg-slate-100 text-slate-600':'bg-blue-100 text-blue-700') }}">{{ $statusLabels[$followUp->status] ?? ucfirst($followUp->status) }}</span></td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ $followUp->assignedUser?->name ?: '-' }}</td>
                <td class="px-6 py-4"><div class="flex justify-end gap-2">@if($followUp->status==='pending')<form method="POST" action="{{ route('follow-ups.complete',$followUp) }}">@csrf<button class="rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm font-medium text-green-700 hover:bg-green-100">Selesai</button></form><form method="POST" action="{{ route('follow-ups.cancel',$followUp) }}">@csrf<button class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100">Batal</button></form>@endif<a href="{{ route('follow-ups.edit',$followUp) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a><form method="POST" action="{{ route('follow-ups.destroy',$followUp) }}" onsubmit="return confirm('Hapus follow up ini?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Hapus</button></form></div></td>
            </tr>
        @empty<tr><td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada follow up.</td></tr>@endforelse
        </tbody></table></div><div class="border-t border-slate-200 px-6 py-4">{{ $followUps->links() }}</div>
    </div>
</div>
@endsection
