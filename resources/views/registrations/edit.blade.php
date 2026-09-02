@extends('layouts.app')

@section('title', 'Edit Pendaftaran Training')
@section('page-heading', 'Edit Pendaftaran Training')
@section('page-description')
    Perbarui data customer, pelatihan, tanggal, status, dan catatan pendaftaran.
@endsection

@section('content')
    @include('registrations._form', [
        'action' => route('registrations.update', $registration),
        'method' => 'PUT',
        'buttonLabel' => 'Simpan Perubahan',
        'registration' => $registration,
    ])
@endsection
