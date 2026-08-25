@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-heading', 'Dashboard')

@section('page-description')
    Ringkasan aktivitas CRM Disty Akademi
@endsection

@section('content')

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Total Customer
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                0
            </p>

            <p class="mt-2 text-xs text-slate-500">
                Data akan tersedia setelah import
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Perusahaan
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                0
            </p>

            <p class="mt-2 text-xs text-slate-500">
                Master perusahaan
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Follow Up
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                0
            </p>

            <p class="mt-2 text-xs text-slate-500">
                Follow up hari ini
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Campaign
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-900">
                0
            </p>

            <p class="mt-2 text-xs text-slate-500">
                Campaign aktif
            </p>
        </div>

    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-6">
                <h2 class="text-base font-semibold text-slate-900">
                    Selamat Datang
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    CRM Disty Akademi sedang dalam tahap pembangunan.
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 p-5">
                <p class="text-sm leading-6 text-slate-600">
                    Sistem ini nantinya akan digunakan untuk mengelola
                    customer, perusahaan, histori pelatihan, aktivitas,
                    follow up, segmentasi, dan email marketing.
                </p>
            </div>

        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-6">
                <h2 class="text-base font-semibold text-slate-900">
                    Modul CRM
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Modul yang akan dikembangkan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold">
                        Customer
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold">
                        Perusahaan
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold">
                        Pelatihan
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold">
                        Follow Up
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold">
                        Segmentasi
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-semibold">
                        Email Marketing
                    </p>
                </div>

            </div>

        </div>

    </div>

@endsection