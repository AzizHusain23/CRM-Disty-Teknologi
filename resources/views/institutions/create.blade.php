@extends('layouts.app')

@section('title', 'Tambah Instansi')

@section('page-heading', 'Tambah Instansi')

@section('page-description')
    Tambahkan instansi baru.
@endsection

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('institutions.store') }}">
                @include('institutions._form', [
                    'submitLabel' => 'Simpan Instansi',
                    'institution' => null,
                ])
            </form>

        </div>

    </div>

@endsection
