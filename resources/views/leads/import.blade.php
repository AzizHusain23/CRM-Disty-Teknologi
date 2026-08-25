<x-app-layout>
    <!-- Navigasi Kustom dengan Tombol Back di Kiri -->
    <x-slot name="header">
        <div class="flex items-center">
            <!-- Tombol Back Button -->
            <a href="{{ route('dashboard') }}" class="mr-4 p-2 text-gray-500 bg-gray-100 rounded-full hover:text-disty-600 hover:bg-disty-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-disty-900 leading-tight">
                {{ __('Import Data Leads (Excel)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-disty-500">
                <div class="p-8 bg-white border-b border-gray-200">
                    
                    <!-- Pesan Sukses/Error -->
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-medium text-disty-900 mb-4">Upload Dataset Calon Pelanggan</h3>
                    <p class="text-sm text-gray-500 mb-6">Sistem otomatis akan mencari email yang valid menggunakan Regex dan mengabaikan data kosong.</p>

                    <form action="{{ route('leads.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx / .csv)</label>
                            <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required
                                class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-disty-50 file:text-disty-600
                                hover:file:bg-disty-100 transition">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-disty-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-disty-600 active:bg-disty-900 focus:outline-none focus:border-disty-900 focus:ring ring-disty-100 transition ease-in-out duration-150">
                                Mulai Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>