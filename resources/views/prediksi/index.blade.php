<x-app-layout>
    <x-slot name="pageTitle">Rekomendasi Belanja</x-slot>
    
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
                            Prediksi dihitung menggunakan metode <strong>Single Moving Average (SMA)</strong> berdasarkan data pemakaian <strong>{{ $history_n }} hari terakhir</strong> (dari: {{ $tanggalMulai->translatedFormat('d F Y') }} s/d {{ $tanggalAkhir->translatedFormat('d F Y') }}).
                        </p>
                    </div>

                    <form method="GET" action="{{ route('prediksi.index') }}" id="form-prediksi" class="mb-4 flex items-center space-x-3">
                        <label class="text-sm font-medium">Periode Belanja (hari):</label>
                        <select name="replenish_days" id="period_days" class="border text-start rounded px-2 flex w-20 py-1">
                            <option value="1" {{ ((isset($replenish_days) && $replenish_days==1) || !isset($replenish_days)) ? 'selected' : '' }}>1</option>
                            <option value="3" {{ (isset($replenish_days) && $replenish_days==3) ? 'selected' : '' }}>3</option>
                            <option value="7" {{ (isset($replenish_days) && $replenish_days==7) ? 'selected' : '' }}>7</option>
                        </select>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const sel = document.getElementById('period_days');
                            sel && sel.addEventListener('change', function () {
                                // tambahkan parameter use_max_stock=1 agar controller tetap tahu batas diterapkan
                                const form = document.getElementById('form-prediksi');
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = 'use_max_stock';
                                hidden.value = '1';
                                form.appendChild(hidden);
                                form.submit();
                            });
                        });
                    </script>

                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <x-responsive-table>
                        <table class="min-w-full divide-y divide-gray-200 border text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Terkini</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Maksimum</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Pemakaian ({{ $history_n }} Hari)</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Rekomendasi Pembelian (untuk {{ $replenish_days ?? 1 }} hari)</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($rekomendasi as $item)
                                <tr>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">{{ $item['nama_bahan'] }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                        {{ number_format($item['stok_terkini'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                        {{ isset($item['stok_maksimum']) ? number_format($item['stok_maksimum'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-gray-500">
                                        {{ number_format($item['total_pemakaian_history'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap font-bold text-lg text-blue-600">
                                        {{ number_format($item['prediksi_pembelian'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">{{ $item['satuan'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 sm:px-6 py-2 sm:py-4 text-center text-gray-500">
                                        Data pemakaian historis belum cukup untuk membuat prediksi.
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
    </div>
</x-app-layout>
