<x-app-layout>
    <x-slot name="pageTitle">Laporan Lengkap</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Lengkap') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Form Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="filter-form" method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                        
                        <div>
                            <x-input-label for="tanggal_mulai" value="Tanggal Mulai" />
                            <x-text-input id="tanggal_mulai" type="date" name="tanggal_mulai" :value="$tanggalMulai" class="py-1" @change="$el.form.submit()" />
                        </div>
                        
                        <div>
                            <x-input-label for="tanggal_akhir" value="Tanggal Akhir" />
                            <x-text-input id="tanggal_akhir" type="date" name="tanggal_akhir" :value="$tanggalAkhir" class="py-1" @change="$el.form.submit()" />
                        </div>

                        <div>
                            <x-input-label for="jenis_laporan" value="Jenis Laporan" />
                            <select name="jenis_laporan" id="jenis_laporan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1" @change="$el.form.submit()">
                                <option value="penjualan" @selected($jenisLaporan == 'penjualan')>Laporan Penjualan</option>
                                <option value="stok_masuk" @selected($jenisLaporan == 'stok_masuk')>Laporan Stok Masuk</option>
                                <option value="pemakaian" @selected($jenisLaporan == 'pemakaian')>Laporan Pemakaian Bahan</option>
                            </select>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-4">
                            <a href="{{ route('admin.laporan.download', ['tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir, 'jenis_laporan' => $jenisLaporan]) }}" 
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download PDF
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Hasil Laporan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    
                    {{-- Judul Laporan Dinamis --}}
                    <h3 class="text-lg font-medium mb-4">
                        @if($jenisLaporan == 'penjualan')
                            Laporan Ringkasan Penjualan per Menu
                        @elseif($jenisLaporan == 'stok_masuk')
                            Laporan Ringkasan Stok Masuk per Bahan
                        @else
                            Laporan Ringkasan Pemakaian per Bahan
                        @endif
                        <span class="text-sm text-gray-500 font-normal">
                            ({{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d M Y') }})
                        </span>
                    </h3>

                    {{-- Tabel Hasil (Versi Ringkasan) --}}
                    <x-responsive-table>
                    <table class="min-w-full divide-y divide-gray-200 border text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach($kolomTabel as $kolom)
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $kolom }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($dataLaporan as $data)
                                <tr>
                                    @if($jenisLaporan == 'penjualan')
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->menu->nama_menu }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ number_format($data->total_porsi, 0, ',', '.') }}</td>
                                    @elseif($jenisLaporan == 'stok_masuk')
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->bahanBaku->nama_bahan }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ number_format($data->total_masuk, 0, ',', '.') }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->bahanBaku->satuan }}</td>
                                    @else
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->bahanBaku->nama_bahan }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ number_format($data->total_terpakai, 0, ',', '.') }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->bahanBaku->satuan }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($kolomTabel) }}" class="px-3 sm:px-6 py-2 sm:py-4 text-center text-gray-500">
                                        Tidak ada data untuk rentang tanggal dan jenis laporan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </x-responsive-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>