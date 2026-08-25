<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <!-- Tombol Back Button (Kiri Navbar) -->
            <a href="{{ route('dashboard') }}" class="mr-4 p-2 text-gray-500 bg-gray-100 rounded-full hover:text-disty-600 hover:bg-disty-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-disty-900 leading-tight">
                {{ __('Tambah Prospek Manual') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-disty-500 p-8">
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('leads.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap & Gelar</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-disty-500 focus:border-disty-500 text-sm" placeholder="Dr. John Doe, M.T.">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Institusi / Perguruan Tinggi</label>
                        <input type="text" name="institusi" value="{{ old('institusi') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-disty-500 focus:border-disty-500 text-sm" placeholder="Universitas Indonesia">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Utama</label>
                        <input type="email" name="email_primary" value="{{ old('email_primary') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-disty-500 focus:border-disty-500 text-sm" placeholder="dosen@ui.ac.id">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomer Dokumen (Opsional)</label>
                            <input type="text" name="nomer_dok" value="{{ old('nomer_dok') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-disty-500 focus:border-disty-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP / WhatsApp (Opsional)</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-disty-500 focus:border-disty-500 text-sm" placeholder="08123456789">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-4 py-2 bg-disty-500 text-white rounded-md text-xs uppercase font-semibold hover:bg-disty-600 transition">
                            Simpan Prospek
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>