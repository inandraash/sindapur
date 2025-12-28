<x-app-layout>
    <x-slot name="pageTitle">Catat Penjualan Harian</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Penjualan Harian') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6 animate-slideUp">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300">
                <div class="p-6 md:p-8 text-gray-900">
                    <header class="animate-fadeIn">
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
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <label for="menu_{{ $menu->id }}" class="text-md font-medium text-gray-700">{{ $menu->nama_menu }}</label>
                                    <div class="flex flex-col items-end">
                                        <x-text-input id="menu_{{ $menu->id }}" class="block w-24 text-center" type="number" name="penjualan[{{ $menu->id }}]" placeholder="0" />
                                        @error("penjualan.{$menu->id}")
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300" style="animation-delay: 0.1s">
                <div class="p-6 md:p-8 text-gray-900 animate-slideUp">
                    <h3 class="text-lg font-medium mb-4">Rekap Penjualan Tercatat</h3>

                    <form method="GET" action="{{ route('staf.penjualan.index') }}" id="penjualan-filter-form" class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label for="tanggal_filter" class="text-sm font-medium">Tampilkan Tanggal:</label>
                            <x-text-input id="tanggal_filter" type="date" name="tanggal" :value="$selectedDate" class="py-1 w-full" />
                        </div>
                        <div>
                            <label for="search" class="text-sm font-medium">Cari Menu:</label>
                            <x-text-input id="search" type="text" name="search" :value="$search ?? ''" placeholder="Nama menu..." class="py-1 w-full" />
                        </div>
                        <div class="hidden"></div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const form = document.getElementById('penjualan-filter-form');
                            const dateInput = document.getElementById('tanggal_filter');
                            const searchInput = document.getElementById('search');

                            if (dateInput) {
                                dateInput.addEventListener('change', () => form.submit());
                            }

                            if (searchInput) {
                                let t;
                                searchInput.addEventListener('input', () => {
                                    clearTimeout(t);
                                    t = setTimeout(() => form.submit(), 400);
                                });
                            }
                        });
                    </script>

                    @if($penjualanHarian->isEmpty())
                        <p class="text-center text-gray-500 mt-4">Belum ada data penjualan yang tercatat untuk tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.</p>
                    @else
                        <div class="hidden md:block">
                        <x-responsive-table>
                        @php
                            $baseQuery = request()->except(['sort_by','sort_dir']);
                            $sortIcons = function($col) use ($sort_by, $sort_dir) {
                                $isActive = ($sort_by ?? 'last_recorded') === $col;
                                $ascActive = $isActive && ($sort_dir ?? 'desc') === 'asc';
                                $descActive = $isActive && ($sort_dir ?? 'desc') === 'desc';
                                return ['asc' => $ascActive, 'desc' => $descActive];
                            };
                            $sortLink = function($col) use ($baseQuery, $sort_by, $sort_dir) {
                                $isActive = ($sort_by ?? 'last_recorded') === $col;
                                $nextDir = ($isActive && ($sort_dir ?? 'desc') === 'asc') ? 'desc' : 'asc';
                                return route('staf.penjualan.index', array_merge($baseQuery, [
                                    'sort_by' => $col,
                                    'sort_dir' => $nextDir,
                                ]));
                            };
                        @endphp
                        <table class="min-w-full divide-y divide-gray-200 border text-sm sm:text-base">
                            <thead class="bg-gray-50">
                                <tr>
                                    @php $icons = $sortIcons('menu_name'); @endphp
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <a href="{{ $sortLink('menu_name') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 py-1 px-2 rounded hover:bg-indigo-100">
                                            <span class="font-semibold">Nama Menu</span>
                                            <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                            </span>
                                        </a>
                                    </th>
                                    @php $icons = $sortIcons('total_porsi'); @endphp
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <a href="{{ $sortLink('total_porsi') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 py-1 px-2 rounded hover:bg-indigo-100">
                                            <span class="font-semibold">Total Porsi</span>
                                            <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                            </span>
                                        </a>
                                    </th>
                                    @php $icons = $sortIcons('last_recorded'); @endphp
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <a href="{{ $sortLink('last_recorded') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 py-1 px-2 rounded hover:bg-indigo-100">
                                            <span class="font-semibold">Terakhir Dicatat</span>
                                            <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                            </span>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($penjualanHarian as $penjualan)
                                    <tr class="hover:bg-indigo-50 transition-colors duration-200">
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm font-medium">{{ $penjualan->menu_name }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ number_format($penjualan->total_porsi, 0, ',', '.') }} porsi</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($penjualan->last_recorded)->format('H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </x-responsive-table>
                        </div>

                        <!-- Mobile sort controls -->
                        <div class="md:hidden mb-3">
                            <form action="{{ route('staf.penjualan.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                                <input type="hidden" name="tanggal" value="{{ $selectedDate }}" />
                                <input type="hidden" name="search" value="{{ $search }}" />
                                <input type="hidden" name="sort_dir" value="{{ $sort_dir }}" />

                                <label class="text-xs text-gray-600" for="mobile_sort">Urutkan:</label>
                                <select id="mobile_sort" name="sort_by" class="border rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                                    <option value="menu_name" @selected(($sort_by ?? 'last_recorded') === 'menu_name')>Nama Menu</option>
                                    <option value="total_porsi" @selected(($sort_by ?? 'last_recorded') === 'total_porsi')>Total Porsi</option>
                                    <option value="last_recorded" @selected(($sort_by ?? 'last_recorded') === 'last_recorded')>Terakhir Dicatat</option>
                                </select>

                                <button type="submit" name="sort_dir" value="{{ ($sort_dir ?? 'desc') === 'asc' ? 'desc' : 'asc' }}" class="inline-flex items-center px-2 py-1 text-xs border rounded hover:bg-gray-100 transition">
                                    <span class="mr-1">Arah</span>
                                    @if (($sort_dir ?? 'desc') === 'asc')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <div class="md:hidden space-y-3">
                            @foreach ($penjualanHarian as $penjualan)
                                <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $penjualan->menu_name }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Terakhir dicatat: {{ \Carbon\Carbon::parse($penjualan->last_recorded)->format('H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Porsi</p>
                                            <p class="text-sm font-semibold">{{ number_format($penjualan->total_porsi, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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