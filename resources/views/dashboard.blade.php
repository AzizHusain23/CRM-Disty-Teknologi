<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <!-- Tombol Back Button -->
                <a href="{{ url()->previous() }}" class="mr-4 p-2 text-gray-500 bg-gray-100 rounded-full hover:text-disty-600 hover:bg-disty-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-disty-900 leading-tight">
                    {{ __('Dashboard CRM Disty Academy') }}
                </h2>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('leads.create') }}" class="px-4 py-2 bg-gray-800 text-white text-xs rounded-md uppercase font-semibold hover:bg-gray-700 transition">+ Tambah Manual</a>
                <a href="{{ route('leads.import') }}" class="px-4 py-2 bg-gray-800 text-white text-xs rounded-md uppercase font-semibold hover:bg-gray-700 transition">Import Excel</a>
                <a href="{{ route('campaigns.create') }}" class="px-4 py-2 bg-gray-800 text-white text-xs rounded-md uppercase font-semibold hover:bg-gray-700 transition">Kirim Email Blast</a>
            </div>
            
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Ringkasan Metrik Real-time -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-disty-500">
                    <p class="text-xs uppercase font-semibold text-gray-500">Total Prospek</p>
                    <h3 class="text-3xl font-bold text-disty-900 mt-2">{{ Str::limit($metrics['total'], 4, '') }}</h3>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
                    <p class="text-xs uppercase font-semibold text-gray-500">Dalam Antrean</p>
                    <h3 class="text-3xl font-bold text-yellow-600 mt-2">{{ Str::limit($metrics['queuing'], 4, '') }}</h3>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <p class="text-xs uppercase font-semibold text-gray-500">Email Terkirim</p>
                    <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ Str::limit($metrics['delivered'], 4, '') }}</h3>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <p class="text-xs uppercase font-semibold text-gray-500">Merespon / Membalas</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">{{ Str::limit($metrics['replied'], 4, '') }}</h3>
                </div>
            </div>

            <!-- Form Searchbar & Sorting -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between gap-4">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col md:flex-row gap-3 w-full">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, Institusi, atau Email..." class="w-full text-sm border-gray-300 rounded-md focus:ring-disty-500 focus:border-disty-500">
                    </div>
                    <div>
                        <select name="sort" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md focus:ring-disty-500 focus:border-disty-500 w-full md:w-auto">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                            <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Urutkan: Status Pipeline</option>
                            <option value="institusi" {{ request('sort') == 'institusi' ? 'selected' : '' }}>Urutkan: Abjad Institusi</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-disty-500 text-white text-sm rounded-md hover:bg-disty-600 transition">Terapkan Filter</button>
                    @if(request('search') || request('sort'))
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition text-center">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Tabel Customer Pipeline -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-disty-50 text-disty-900 font-semibold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Nama Prospek</th>
                            <th class="px-6 py-3">Institusi</th>
                            <th class="px-6 py-3">Email Primary</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">No. WhatsApp / HP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($leads as $lead)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $lead->nama }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $lead->institusi }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $lead->email_primary }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($lead->status == 'Uncontacted') bg-gray-100 text-gray-800
                                        @elseif($lead->status == 'Queuing') bg-yellow-100 text-yellow-800
                                        @elseif($lead->status == 'Delivered') bg-blue-100 text-blue-800
                                        @elseif($lead->status == 'Replied') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $lead->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- Fitur input nomor HP hanya/siap terbuka jika merespon -->
                                    @if($lead->phone)
                                        <span class="text-green-700 font-semibold">{{ $lead->phone }}</span>
                                    @else
                                        <form action="{{ route('leads.update-phone', $lead->id) }}" method="POST" class="flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="phone" placeholder="08xxx" maxlength="4" class="text-xs border-gray-300 rounded px-2 py-1 w-24 focus:ring-disty-500" required>
                                            <button type="submit" class="px-2 py-1 bg-disty-500 text-white text-xs rounded hover:bg-disty-600">Simpan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data leads ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-200">
                    {{ $leads->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>