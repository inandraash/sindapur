<x-app-layout>
    <x-slot name="pageTitle">Tambah Menu Baru</x-slot>
    
    <x-slot name="header">
        <a href="{{ route('admin.menu.index') }}" class="p-2 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
        </a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Menu Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.menu.store') }}">
                        @csrf
                        <div>
                            <x-input-label for="nama_menu" value="Nama Menu" />
                            <x-text-input id="nama_menu" class="block mt-1 w-full" type="text" name="nama_menu" :value="old('nama_menu')" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="harga" value="Harga" />
                            <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga')" required />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>