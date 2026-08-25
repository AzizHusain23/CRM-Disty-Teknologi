@extends('layouts.app')

@section('title', $customer->name)

@section('page-heading', $customer->name)

@section('page-description')
    Detail dan histori customer.
@endsection

@section('content')

    <div class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">

            <div>

                <div class="flex items-center gap-3">

                    <h2 class="text-xl font-semibold text-slate-900">
                        {{ $customer->name }}
                    </h2>

                    @php
                        $statusClasses = match ($customer->status) {
                            'active' => 'bg-green-100 text-green-700',
                            'prospect' => 'bg-amber-100 text-amber-700',
                            'inactive' => 'bg-slate-100 text-slate-600',
                            'repeat' => 'bg-blue-100 text-blue-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp

                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                        {{ ucfirst($customer->status) }}
                    </span>

                </div>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $customer->customer_code }}
                </p>

            </div>

            <div class="flex gap-2">

                <a href="{{ route('customers.edit', $customer) }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Edit
                </a>

                <a href="{{ route('customers.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>

            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <h3 class="text-base font-semibold text-slate-900">
                    Informasi Customer
                </h3>

                <dl class="mt-6 space-y-4">

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Nama
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $customer->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Email
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $customer->email ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Telepon
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $customer->phone ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Nomor Dokumen
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $customer->document_number ?: '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Perusahaan
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            @if ($customer->company)
                                <a href="{{ route('companies.show', $customer->company) }}"
                                    class="font-medium text-slate-900 hover:underline">
                                    {{ $customer->company->name }}
                                </a>
                            @else
                                -
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Lokasi
                        </dt>

                        <dd class="mt-1 text-sm text-slate-800">
                            {{ $customer->city ?: '-' }}

                            @if ($customer->province)
                                , {{ $customer->province }}
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-400">
                            Sumber
                        </dt>

                        <dd class="mt-1 text-sm capitalize text-slate-800">
                            {{ $customer->source }}
                        </dd>
                    </div>

                </dl>

            </div>

            <div class="space-y-6 lg:col-span-2">

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="text-base font-semibold text-slate-900">
                            Histori Pelatihan
                        </h3>
                    </div>

                    <div class="divide-y divide-slate-200">

                        @forelse ($customer->registrations as $registration)
                            <div class="px-6 py-5">

                                <div class="flex items-start justify-between gap-4">

                                    <div>

                                        <div class="font-semibold text-slate-900">
                                            {{ $registration->training->name }}
                                        </div>

                                        <div class="mt-1 text-sm text-slate-500">
                                            {{ $registration->training_date?->format('d M Y') ?: 'Tanggal belum tersedia' }}
                                        </div>

                                    </div>

                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ ucfirst($registration->status) }}
                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada histori pelatihan.
                            </div>
                        @endforelse

                    </div>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="text-base font-semibold text-slate-900">
                            Aktivitas
                        </h3>
                    </div>

                    <div class="divide-y divide-slate-200">

                        @forelse ($customer->activities->sortByDesc('activity_at') as $activity)
                            <div class="px-6 py-5">

                                <div class="flex items-start justify-between gap-4">

                                    <div>

                                        <div class="font-semibold capitalize text-slate-900">
                                            {{ $activity->type }}
                                        </div>

                                        @if ($activity->subject)
                                            <div class="mt-1 text-sm font-medium text-slate-700">
                                                {{ $activity->subject }}
                                            </div>
                                        @endif

                                        @if ($activity->description)
                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                {{ $activity->description }}
                                            </p>
                                        @endif

                                    </div>

                                    <div class="text-right text-xs text-slate-400">
                                        {{ $activity->activity_at?->format('d M Y H:i') }}
                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada aktivitas.
                            </div>
                        @endforelse

                    </div>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="text-base font-semibold text-slate-900">
                            Follow Up
                        </h3>
                    </div>

                    <div class="divide-y divide-slate-200">

                        @forelse ($customer->followUps->sortBy('follow_up_at') as $followUp)
                            <div class="px-6 py-5">

                                <div class="flex items-start justify-between gap-4">

                                    <div>

                                        <div class="font-semibold text-slate-900">
                                            {{ $followUp->title }}
                                        </div>

                                        @if ($followUp->description)
                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                {{ $followUp->description }}
                                            </p>
                                        @endif

                                        <div class="mt-2 text-xs text-slate-400">
                                            PIC:
                                            {{ $followUp->assignedUser?->name ?: 'Belum ditentukan' }}
                                        </div>

                                    </div>

                                    <div class="text-right">

                                        <div class="text-sm font-medium text-slate-700">
                                            {{ $followUp->follow_up_at?->format('d M Y H:i') }}
                                        </div>

                                        <div class="mt-1 text-xs capitalize text-slate-400">
                                            {{ $followUp->status }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada follow up.
                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
