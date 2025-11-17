<x-app-layout>
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
                        
                        {{-- Tombol submit dihapus karena auto-submit --}}
                        
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
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach($kolomTabel as $kolom)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $kolom }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($dataLaporan as $data)
                                <tr>
                                    @if($jenisLaporan == 'penjualan')
                                        <td class="px-6 py-4">{{ $data->menu->nama_menu }}</td>
                                        <td class="px-6 py-4">{{ number_format($data->total_porsi, 0, ',', '.') }}</td>
                                    @elseif($jenisLaporan == 'stok_masuk')
                                        <td class="px-6 py-4">{{ $data->bahanBaku->nama_bahan }}</td>
                                        <td class="px-6 py-4">{{ number_format($data->total_masuk, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">{{ $data->bahanBaku->satuan }}</td>
                                    @else
                                        <td class="px-6 py-4">{{ $data->bahanBaku->nama_bahan }}</td>
                                        <td class="px-6 py-4">{{ number_format($data->total_terpakai, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">{{ $data->bahanBaku->satuan }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($kolomTabel) }}" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data untuk rentang tanggal dan jenis laporan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>