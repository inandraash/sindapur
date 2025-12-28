<x-app-layout>
    <x-slot name="pageTitle">Rekomendasi Belanja</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekomendasi Belanja') }}
        </h2>
    </x-slot>

    <div class="py-2">
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

                    <!-- Desktop/Tablet table -->
                    <div class="hidden md:block overflow-x-auto -mx-4 sm:mx-0">
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
                                        <a href="{{ $sortLink($col['key']) }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 cursor-pointer py-1 px-2 rounded hover:bg-indigo-100">
                                            <span class="font-semibold">{{ $col['label'] }}</span>
                                            <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                            </span>
                                        </a>
                                    </th>
                                @endforeach
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($rekomendasi as $item)
                                <tr class="hover:bg-sky-50 transition-colors duration-200">
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

                    <!-- Mobile sort controls -->
                    <div class="md:hidden mb-3">
                        <form action="{{ route('prediksi.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="replenish_days" value="{{ $replenish_days }}" />
                            <input type="hidden" name="search" value="{{ $search }}" />
                            <input type="hidden" name="use_max_stock" value="{{ $use_max ? 1 : 0 }}" />
                            <input type="hidden" name="sort_dir" value="{{ $sort_dir }}" />

                            <label class="text-xs text-gray-600" for="mobile_sort">Urutkan:</label>
                            <select id="mobile_sort" name="sort_by" class="border rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                                @foreach ($cols as $col)
                                    <option value="{{ $col['key'] }}" @selected(($sort_by ?? 'nama_bahan') === $col['key'])>{{ $col['label'] }}</option>
                                @endforeach
                            </select>

                            <button type="submit" name="sort_dir" value="{{ ($sort_dir ?? 'asc') === 'asc' ? 'desc' : 'asc' }}" class="inline-flex items-center px-2 py-1 text-xs border rounded hover:bg-gray-100 transition">
                                <span class="mr-1">Arah</span>
                                @if (($sort_dir ?? 'asc') === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @endif
                            </button>
                        </form>
                    </div>

                    <!-- Mobile stacked cards -->
                    <div class="md:hidden space-y-3">
                        @forelse ($rekomendasi as $item)
                            <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $item['nama_bahan'] }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Satuan: {{ $item['satuan'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Rekomendasi</p>
                                        <p class="text-sm font-semibold">{{ number_format($item['prediksi_pembelian'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-600">
                                    <div>Stok: <span class="font-medium">{{ number_format($item['stok_terkini'], 0, ',', '.') }}</span></div>
                                    <div>Maksimum: <span class="font-medium">{{ isset($item['stok_maksimum']) ? number_format($item['stok_maksimum'], 0, ',', '.') : '-' }}</span></div>
                                    <div class="col-span-2">Pemakaian {{ $history_n }} hari: <span class="font-medium">{{ number_format($item['total_pemakaian_history'], 0, ',', '.') }}</span></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center">Data pemakaian historis belum cukup untuk membuat prediksi.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
