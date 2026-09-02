@extends('layouts.app')
@section('title','Buat Follow Up')
@section('page-heading','Buat Follow Up')
@section('page-description') Buat jadwal tindak lanjut customer. @endsection
@section('content')
<div class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
<form method="POST" action="{{ route('follow-ups.store') }}" class="space-y-6">@csrf
@include('follow-ups._form', ['followUp'=>null])
<div class="flex justify-end gap-2"><a href="{{ route('follow-ups.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700">Batal</a><button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Simpan Follow Up</button></div>
</form></div>
@endsection
