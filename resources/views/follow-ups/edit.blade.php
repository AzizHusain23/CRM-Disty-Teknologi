@extends('layouts.app')
@section('title','Edit Follow Up')
@section('page-heading','Edit Follow Up')
@section('page-description') Perbarui jadwal tindak lanjut customer. @endsection
@section('content')
<div class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
<form method="POST" action="{{ route('follow-ups.update',$followUp) }}" class="space-y-6">@csrf @method('PUT')
@include('follow-ups._form', ['followUp'=>$followUp])
<div class="flex justify-end gap-2"><a href="{{ route('follow-ups.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700">Batal</a><button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Simpan Perubahan</button></div>
</form></div>
@endsection
