@extends('layouts.app')

@section('title', 'Edit Perusahaan')

@section('page-heading', 'Edit Perusahaan')

@section('page-description')
    Perbarui informasi perusahaan.
@endsection

@section('content')

    <div class="mx-auto max-w-4xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('companies.update', $company) }}">
                @method('PUT')

                @include('companies._form', [
                    'submitLabel' => 'Simpan Perubahan',
                ])
            </form>

        </div>

    </div>

@endsection
