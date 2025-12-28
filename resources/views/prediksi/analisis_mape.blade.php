<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight print:hidden">
            {{ __('Analisis Akurasi Peramalan (MAPE)') }}
        </h2>
    </x-slot>

    <style>
        @media print {
            .no-print, header, nav, footer { display: none !important; }
            body { background-color: white !important; }
            .print-header { display: block !important; margin-bottom: 20px; text-align: center; }
            .page-break { page-break-inside: avoid; }
        }
        .print-header { display: none; }
    </style>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="print-header">
                <h1 class="text-2xl font-bold uppercase">Laporan Hasil Pengujian Akurasi Sistem</h1>
                <p>Metode: Single Moving Average (SMA-7) | Periode Uji: {{ $start_date->format('d M Y') }} - {{ $end_date->format('d M Y') }}</p>
                <hr class="border-2 border-black my-4">
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 shadow-sm no-print flex justify-between items-center">
                <div>
                    <p class="font-bold text-blue-700">Metode Pengujian Mingguan</p>
                    <p class="text-sm text-blue-600">Nilai MAPE dirata-rata selama 7 hari ({{ $start_date->format('d M') }} - {{ $end_date->format('d M') }}) untuk mengakomodasi fluktuasi penjualan <i>Weekdays</i> vs <i>Weekend</i>.</p>
                </div>
                <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center ml-4 shadow-lg transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download PDF / Cetak
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase border border-gray-300">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase border border-gray-300">Nama Bahan</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase border border-gray-300 w-1/3">Detail Error Harian (Senin-Minggu)</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Rata-rata MAPE</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border border-gray-300">Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($laporanMingguan as $data)
                                <tr class="page-break hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-500 border border-gray-300 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900 border border-gray-300">{{ $data['nama_bahan'] }} <span class="text-xs font-normal text-gray-500">({{ $data['satuan'] }})</span></td>
                                    
                                    <td class="px-4 py-3 text-xs text-gray-500 border border-gray-300">
                                        <div class="grid grid-cols-4 gap-1">
                                            @foreach($data['detail_harian'] as $harian)
                                                <div class="{{ $harian['mape'] > 50 ? 'text-red-500 font-bold' : 'text-green-600' }}">
                                                    {{ substr($harian['hari'], 0, 3) }}: {{ number_format($harian['mape'], 0) }}%
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-center border border-gray-300">
                                        <span class="text-sm font-bold text-blue-700">
                                            {{ number_format($data['rata_mape'], 2) }}%
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center border border-gray-300 text-sm">
                                        @if($data['rata_mape'] <= 10) <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full font-bold text-xs">Sangat Baik</span>
                                        @elseif($data['rata_mape'] <= 20) <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-bold text-xs">Baik</span>
                                        @elseif($data['rata_mape'] <= 50) <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-bold text-xs">Layak</span>
                                        @else <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full font-bold text-xs">Buruk</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right border border-gray-300 text-gray-700">Rata-rata Akurasi Sistem (All Item):</td>
                                    <td class="px-4 py-3 text-center border border-gray-300 text-blue-800">
                                        {{ number_format(collect($laporanMingguan)->avg('rata_mape'), 2) }}%
                                    </td>
                                    <td class="border border-gray-300"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-8 text-xs text-gray-500 print-header">
                        <p>Dicetak otomatis oleh Sistem SINDAPUR pada {{ date('d F Y H:i') }}</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>