@extends('layouts.app')

@section('title', 'Tambah Pendaftaran Training')
@section('page-heading', 'Tambah Pendaftaran Training')
@section('page-description')
    Catat customer Active atau Repeat Customer yang masuk ke pelatihan.
@endsection

@section('content')
    @include('registrations._form', [
        'action' => route('registrations.store'),
        'method' => 'POST',
        'buttonLabel' => 'Simpan Pendaftaran',
        'registration' => null,
    ])
@endsection
