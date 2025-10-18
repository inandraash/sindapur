<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Penjualan Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @elseif (session('error'))
                 <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Input Jumlah Porsi Terjual
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Masukkan jumlah porsi yang terjual untuk setiap menu pada hari ini. Biarkan kosong jika tidak ada penjualan.
                        </p>
                    </header>
                    
                    <form method="POST" action="{{ route('staf.penjualan.store') }}" class="mt-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="tanggal_penjualan" value="Tanggal Penjualan" />
                            <x-text-input id="tanggal_penjualan" class="block mt-1 w-full md:w-1/2" type="date" name="tanggal_penjualan" :value="date('Y-m-d')" required />
                            <x-input-error :messages="$errors->get('tanggal_penjualan')" class="mt-2" />
                        </div>
                        
                        <div class="mt-6 space-y-4">
                            @foreach ($menus as $menu)
                                <div class="flex items-center justify-between">
                                    <label for="menu_{{ $menu->id }}" class="text-md font-medium text-gray-700">{{ $menu->nama_menu }}</label>
                                    <x-text-input id="menu_{{ $menu->id }}" class="block w-24 text-center" type="number" name="penjualan[{{ $menu->id }}]" min="0" />
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>