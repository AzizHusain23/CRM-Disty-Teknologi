@extends('layouts.app')

@section('title', 'Tambah Customer')

@section('page-heading', 'Tambah Customer')

@section('page-description')
    Tambahkan customer baru ke CRM.
@endsection

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('customers.store') }}">

                @include('customers._form', [
                    'submitLabel' => 'Simpan Customer',
                    'customer' => null,
                ])

            </form>

        </div>

    </div>

@endsection
