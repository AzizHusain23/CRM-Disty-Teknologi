@extends('layouts.app')

@section('title', 'Tambah Perusahaan')

@section('page-heading', 'Tambah Perusahaan')

@section('page-description')
    Tambahkan perusahaan atau institusi baru.
@endsection

@section('content')

    <div class="mx-auto max-w-4xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('companies.store') }}">
                @include('companies._form', [
                    'submitLabel' => 'Simpan Perusahaan',
                    'company' => null,
                ])
            </form>

        </div>

    </div>

@endsection
