@extends('layouts.app')

@section('title', 'Edit Instansi')

@section('page-heading', 'Edit Instansi')

@section('page-description')
    Perbarui informasi instansi.
@endsection

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('institutions.update', $institution) }}">

                @method('PUT')

                @include('institutions._form', [
                    'submitLabel' => 'Simpan Perubahan',
                ])

            </form>

        </div>

    </div>

@endsection
