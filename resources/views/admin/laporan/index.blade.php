<x-app-layout>
    <x-slot name="pageTitle">Laporan Lengkap</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Lengkap') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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
                            <select name="jenis_laporan" id="jenis_laporan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-1 mr-4" @change="$el.form.submit()">
                                <option value="penjualan" @selected($jenisLaporan == 'penjualan')>Laporan Penjualan</option>
                                <option value="stok_masuk" @selected($jenisLaporan == 'stok_masuk')>Laporan Stok Masuk</option>
                                <option value="pemakaian" @selected($jenisLaporan == 'pemakaian')>Laporan Pemakaian Bahan</option>
                            </select>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-4 mr-4">
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
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const form = document.getElementById('filter-form');
                            ['tanggal_mulai','tanggal_akhir','jenis_laporan'].forEach(id => {
                                const el = document.getElementById(id);
                                el && el.addEventListener('change', () => form.submit());
                            });
                        });
                    </script>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-4">
                        <h3 class="text-lg font-medium">
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
                        <form method="GET" action="{{ route('admin.laporan.index') }}" class="mt-4 md:mt-0 flex items-end gap-2">
                            <input type="hidden" name="tanggal_mulai" value="{{ $tanggalMulai }}">
                            <input type="hidden" name="tanggal_akhir" value="{{ $tanggalAkhir }}">
                            <input type="hidden" name="jenis_laporan" value="{{ $jenisLaporan }}">
                            <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'total' }}">
                            <input type="hidden" name="sort_dir" value="{{ $sortDir ?? 'desc' }}">
                            <div class="ml-4">
                                <x-input-label for="search_inline" value="Cari" />
                                <x-text-input id="search_inline" type="text" name="search" value="{{ $search ?? '' }}" class="py-1" placeholder="Nama menu/bahan" />
                            </div>
                        </form>
                    </div>

                    <!-- Desktop/Tablet table -->
                    <div class="hidden md:block">
                    <x-responsive-table>
                    <table class="min-w-full divide-y divide-gray-200 border text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            @php
                                $baseQuery = request()->except(['sort_by','sort_dir']);
                                $sort_by = request()->input('sort_by', 'total');
                                $sort_dir = request()->input('sort_dir', 'desc');
                                $sortIcons = function($col) use ($sort_by, $sort_dir) {
                                    $isActive = $sort_by === $col;
                                    return [
                                        'asc' => $isActive && $sort_dir === 'asc',
                                        'desc' => $isActive && $sort_dir === 'desc',
                                    ];
                                };
                                $sortLink = function($col) use ($baseQuery, $sort_by, $sort_dir, $tanggalMulai, $tanggalAkhir, $jenisLaporan) {
                                    $isActive = $sort_by === $col;
                                    $nextDir = ($isActive && $sort_dir === 'asc') ? 'desc' : 'asc';
                                    return route('admin.laporan.index', array_merge($baseQuery, [
                                        'tanggal_mulai' => $tanggalMulai,
                                        'tanggal_akhir' => $tanggalAkhir,
                                        'jenis_laporan' => $jenisLaporan,
                                        'sort_by' => $col,
                                        'sort_dir' => $nextDir,
                                    ]));
                                };
                            @endphp
                            <tr>
                                @php $icons = $sortIcons('nama'); @endphp
                                <th class="px-3 sm:px-6 py-2 sm:py-3">
                                    <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 cursor-pointer py-1 px-2 rounded hover:bg-indigo-100">
                                        <span class="font-semibold">{{ $jenisLaporan === 'penjualan' ? 'Nama Menu' : 'Nama Bahan Baku' }}</span>
                                        <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                        </span>
                                    </a>
                                </th>
                                @php $icons = $sortIcons('total'); @endphp
                                <th class="px-3 sm:px-6 py-2 sm:py-3">
                                    <a href="{{ $sortLink('total') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 cursor-pointer py-1 px-2 rounded hover:bg-indigo-100">
                                        <span class="font-semibold">{{ $jenisLaporan === 'penjualan' ? 'Total Porsi Terjual' : ($jenisLaporan === 'stok_masuk' ? 'Total Masuk' : 'Total Pemakaian') }}</span>
                                        <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                        </span>
                                    </a>
                                </th>
                                @if($jenisLaporan !== 'penjualan')
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($dataLaporan as $data)
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    @if($jenisLaporan == 'penjualan')
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->menu->nama_menu }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ number_format($data->total, 0, ',', '.') }}</td>
                                    @elseif($jenisLaporan == 'stok_masuk')
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->bahanBaku->nama_bahan }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ number_format($data->total, 0, ',', '.') }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->bahanBaku->satuan }}</td>
                                    @else
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $data->bahanBaku->nama_bahan }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4">{{ number_format($data->total, 2, ',', '.') }}</td>
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

                    <!-- Mobile stacked cards -->
                    <div class="md:hidden space-y-3">
                        <div class="flex items-end gap-2 mb-2">
                            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex items-end gap-2">
                                <input type="hidden" name="tanggal_mulai" value="{{ $tanggalMulai }}" />
                                <input type="hidden" name="tanggal_akhir" value="{{ $tanggalAkhir }}" />
                                <input type="hidden" name="jenis_laporan" value="{{ $jenisLaporan }}" />
                                <input type="hidden" name="search" value="{{ $search ?? '' }}" />
                                <div>
                                    <x-input-label for="sort_by_m" value="Urut" />
                                    <select id="sort_by_m" name="sort_by" class="border-gray-300 rounded-md shadow-sm py-1">
                                        <option value="total" @selected(($sortBy ?? 'total')==='total')>Total</option>
                                        <option value="nama" @selected(($sortBy ?? 'total')==='nama')>Nama</option>
                                    </select>
                                </div>
                                <input type="hidden" name="sort_dir" value="{{ $sortDir ?? 'desc' }}" />
                                <x-primary-button class="ml-1">Terapkan</x-primary-button>
                            </form>
                            <a href="{{ route('admin.laporan.index', ['tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir, 'jenis_laporan' => $jenisLaporan, 'search' => $search ?? null, 'sort_by' => $sortBy ?? 'total', 'sort_dir' => ($sortDir ?? 'desc')==='asc' ? 'desc' : 'asc']) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-xs">
                                Arah: {{ ($sortDir ?? 'desc')==='asc' ? 'Naik' : 'Turun' }}
                            </a>
                        </div>
                        @forelse ($dataLaporan as $data)
                            <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                @if($jenisLaporan == 'penjualan')
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="font-medium text-gray-800">{{ $data->menu->nama_menu }}</p>
                                        <p class="text-sm font-semibold">{{ number_format($data->total, 0, ',', '.') }} porsi</p>
                                    </div>
                                @elseif($jenisLaporan == 'stok_masuk')
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $data->bahanBaku->nama_bahan }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Satuan: {{ $data->bahanBaku->satuan }}</p>
                                        </div>
                                        <p class="text-sm font-semibold">{{ number_format($data->total, 0, ',', '.') }}</p>
                                    </div>
                                @else
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $data->bahanBaku->nama_bahan }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Satuan: {{ $data->bahanBaku->satuan }}</p>
                                        </div>
                                        <p class="text-sm font-semibold">{{ number_format($data->total, 2, ',', '.') }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center">Tidak ada data untuk rentang tanggal dan jenis laporan ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>