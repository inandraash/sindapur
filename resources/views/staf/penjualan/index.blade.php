<x-app-layout>
    <x-slot name="pageTitle">Catat Penjualan Harian</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Penjualan Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4" role="alert">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-green-800 font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @elseif (session('error'))
                @php
                    $raw = session('error');
                    $parts = preg_split('/\\\\n/', $raw);
                    $title = trim($parts[0] ?? 'Terjadi kesalahan');
                    $items = collect($parts)->skip(1)->map(function($l){ return trim(ltrim($l, '- ')); })->filter()->values();
                @endphp
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4" role="alert">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-red-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div class="flex-1">
                            <p class="text-red-800 font-semibold">{{ $title }}</p>
                            @if ($items->count())
                                <ul class="mt-2 list-disc pl-5 text-red-700 text-sm space-y-1">
                                    @foreach ($items as $line)
                                        <li>{{ $line }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-red-700 text-sm mt-2">{!! nl2br(e(str_replace('\\n', "\n", $raw))) !!}</p>
                            @endif
                        </div>
                        <button type="button" class="ml-3 text-red-400 hover:text-red-600" @click="$el.closest('.mb-4').remove()" aria-label="Tutup">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
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
                        <x-responsive-table>
                        <table class="min-w-full divide-y divide-gray-200 border text-sm sm:text-base">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Menu</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Porsi</th>
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Terakhir Dicatat</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($penjualanHarian as $penjualan)
                                    <tr>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm font-medium">{{ $penjualan->menu->nama_menu }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ number_format($penjualan->total_porsi, 0, ',', '.') }} porsi</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($penjualan->last_recorded)->format('H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </x-responsive-table>
                        
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-gray-700">
                                <strong>Total penjualan hari ini:</strong> 
                                {{ number_format($penjualanHarian->sum('total_porsi'), 0, ',', '.') }} porsi dari {{ $penjualanHarian->count() }} jenis menu
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>