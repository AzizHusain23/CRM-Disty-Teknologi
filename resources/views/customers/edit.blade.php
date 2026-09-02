@extends('layouts.app')

@section('title', 'Edit Customer')

@section('page-heading', 'Edit Customer')

@section('page-description')
    Perbarui informasi customer.
@endsection

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('customers.update', $customer) }}">

                @method('PUT')

                @include('customers._form', [
                    'submitLabel' => 'Simpan Perubahan',
                ])

            </form>

        </div>

    </div>

@endsection
