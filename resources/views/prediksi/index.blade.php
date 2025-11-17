<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekomendasi Belanja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="font-bold text-blue-800">
                            Rekomendasi Belanja untuk Hari Ini ({{ $tanggalPrediksi }})
                        </h3>
                        <p class="text-sm text-blue-700">
                            Prediksi dihitung menggunakan metode <strong>Single Moving Average (SMA)</strong> 
                            berdasarkan data pemakaian <strong>{{ $periode_n }} hari terakhir</strong> (dari: {{ $tanggalMulai->translatedFormat('d F Y') }} s/d {{ $tanggalAkhir->translatedFormat('d F Y') }}).
                        </p>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Terkini</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Pemakaian (7 Hari)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rekomendasi Pembelian</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($rekomendasi as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item['nama_bahan'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ number_format($item['stok_terkini'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        {{ number_format($item['total_pemakaian_7_hari'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-lg text-blue-600">
                                        {{ number_format($item['prediksi_pembelian'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item['satuan'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Data pemakaian historis belum cukup untuk membuat prediksi.
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