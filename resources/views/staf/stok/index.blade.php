<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Stok Masuk (Pembelian)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @elseif (session('error'))
                 <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Form Input Stok Masuk --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Input Bahan Baku Masuk</h2>
                        <p class="mt-1 text-sm text-gray-600">Catat bahan baku yang baru dibeli atau diterima. Stok akan otomatis bertambah.</p>
                    </header>
                    
                    <form method="POST" action="{{ route('staf.stok-masuk.store') }}" class="mt-6 space-y-4">
                        @csrf
                        
                        <div>
                            <x-input-label for="tanggal_masuk" value="Tanggal Masuk" />
                            <x-text-input id="tanggal_masuk" class="block mt-1 w-full md:w-1/2" type="date" name="tanggal_masuk" :value="old('tanggal_masuk', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="bahan_baku_id" value="Nama Bahan Baku" />
                            <select name="bahan_baku_id" id="bahan_baku_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option disabled selected value="">-- Pilih Bahan --</option>
                                @foreach($bahanBakus as $bahan)
                                    <option value="{{ $bahan->id }}" @selected(old('bahan_baku_id') == $bahan->id)>
                                        {{ $bahan->nama_bahan }} (Satuan: {{ $bahan->satuan }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('bahan_baku_id')" class="mt-2" />
                        </div>

                         <div>
                            <x-input-label for="jumlah_masuk" value="Jumlah Masuk" />
                            <x-text-input id="jumlah_masuk" class="block mt-1 w-full" type="number" step="0.01" name="jumlah_masuk" :value="old('jumlah_masuk')" required />
                            <x-input-error :messages="$errors->get('jumlah_masuk')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>{{ __('Simpan Stok Masuk') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Rekap Stok Masuk --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Rekap Stok Masuk Tercatat</h3>
                    <form method="GET" action="{{ route('staf.stok-masuk.index') }}" class="mb-4 flex items-center space-x-2">
                        <label for="tanggal_filter" class="text-sm font-medium">Tampilkan Tanggal:</label>
                        <x-text-input id="tanggal_filter" type="date" name="tanggal" :value="$selectedDate" class="py-1" />
                        <x-primary-button type="submit" class="py-1">Filter</x-primary-button>
                    </form>
                    @if($stokMasukHarian->isEmpty())
                        <p class="text-center text-gray-500 mt-4">Belum ada data stok masuk untuk tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dicatat Oleh</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Masuk</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($stokMasukHarian as $stok)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $stok->user->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $stok->bahanBaku->nama_bahan }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ number_format($stok->jumlah_masuk, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $stok->bahanBaku->satuan }}</td>
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