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

                    @php
                        $baseQuery = request()->except(['sort_by', 'sort_dir']);
                        $sortIcons = function($col) use ($sort_by, $sort_dir) {
                            $isActive = ($sort_by ?? 'nama_bahan') === $col;
                            $ascActive = $isActive && ($sort_dir ?? 'asc') === 'asc';
                            $descActive = $isActive && ($sort_dir ?? 'asc') === 'desc';
                            return [
                                'asc' => $ascActive,
                                'desc' => $descActive,
                            ];
                        };
                        $sortLink = function($col) use ($baseQuery, $sort_by, $sort_dir) {
                            $isActive = ($sort_by ?? 'nama_bahan') === $col;
                            $nextDir = ($isActive && ($sort_dir ?? 'asc') === 'asc') ? 'desc' : 'asc';
                            return route('prediksi.index', array_merge($baseQuery, [
                                'sort_by' => $col,
                                'sort_dir' => $nextDir,
                            ]));
                        };
                    @endphp

                    <form method="GET" action="{{ route('prediksi.index') }}" id="form-prediksi" class="mb-4 grid grid-cols-1 lg:grid-cols-4 gap-3 items-end">
                        <div>
                            <label class="text-sm font-medium">Periode Belanja (hari):</label>
                            <select name="replenish_days" id="period_days" class="border text-start rounded px-2 w-full py-2 mt-1">
                                <option value="1" {{ ((isset($replenish_days) && $replenish_days==1) || !isset($replenish_days)) ? 'selected' : '' }}>1</option>
                                <option value="3" {{ (isset($replenish_days) && $replenish_days==3) ? 'selected' : '' }}>3</option>
                                <option value="7" {{ (isset($replenish_days) && $replenish_days==7) ? 'selected' : '' }}>7</option>
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label class="text-sm font-medium" for="search">Cari Bahan Baku:</label>
                            <input type="text" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Nama bahan baku..." class="border rounded px-3 py-2 w-full mt-1" />
                        </div>

                        <div class="flex flex-wrap gap-2 justify-end lg:col-span-1">
                            <input type="hidden" name="use_max_stock" value="1" />
                            <a href="{{ route('prediksi.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 border border-transparent rounded-md text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">Reset</a>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const form = document.getElementById('form-prediksi');
                            const period = document.getElementById('period_days');
                            const search = document.getElementById('search');

                            if (period) {
                                period.addEventListener('change', () => form.submit());
                            }

                            if (search) {
                                let t;
                                search.addEventListener('input', () => {
                                    clearTimeout(t);
                                    t = setTimeout(() => form.submit(), 400);
                                });
                            }
                        });
                    </script>

                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <x-responsive-table>
                        <table class="min-w-full divide-y divide-gray-200 border text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            <tr>
                                @php
                                    $cols = [
                                        ['key' => 'nama_bahan', 'label' => 'Nama Bahan'],
                                        ['key' => 'stok_terkini', 'label' => 'Stok Terkini'],
                                        ['key' => 'stok_maksimum', 'label' => 'Stok Maksimum'],
                                        ['key' => 'total_pemakaian_history', 'label' => 'Total Pemakaian ('. $history_n .' Hari)'],
                                        ['key' => 'prediksi_pembelian', 'label' => 'Rekomendasi Pembelian (untuk '. ($replenish_days ?? 1) .' hari)'],
                                    ];
                                @endphp
                                @foreach ($cols as $col)
                                    @php $icons = $sortIcons($col['key']); @endphp
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <a href="{{ $sortLink($col['key']) }}" class="inline-flex items-center gap-1 group">
                                            <span>{{ $col['label'] }}</span>
                                            <span class="flex flex-col leading-none text-gray-300 group-hover:text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ $icons['asc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 -mt-1 {{ $icons['desc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                            </span>
                                        </a>
                                    </th>
                                @endforeach
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
