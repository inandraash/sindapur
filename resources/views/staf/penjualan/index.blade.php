<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Penjualan Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Rekap Penjualan Tercatat</h3>

                    <form method="GET" action="{{ route('staf.penjualan.index') }}" class="mb-4 flex items-center space-x-2">
                        <label for="tanggal_filter" class="text-sm font-medium">Tampilkan Tanggal:</label>
                        <x-text-input id="tanggal_filter" type="date" name="tanggal" :value="$selectedDate" class="py-1" />
                        <x-primary-button type="submit" class="py-1">Filter</x-primary-button>
                    </form>

                    @if($penjualanHarian->isEmpty())
                        <p class="text-center text-gray-500 mt-4">Belum ada data penjualan yang tercatat untuk tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dicatat Oleh</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Menu</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Porsi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($penjualanHarian as $penjualan)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $penjualan->user->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $penjualan->menu->nama_menu }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ number_format($penjualan->jumlah_porsi, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>