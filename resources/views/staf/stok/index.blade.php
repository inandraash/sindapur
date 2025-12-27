<x-app-layout>
    <x-slot name="pageTitle">Catat Stok Masuk (Pembelian)</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Stok Masuk (Pembelian)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6 animate-slideUp">

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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300">
                <div class="p-6 md:p-8 text-gray-900">
                    <header class="animate-fadeIn">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300" style="animation-delay: 0.1s">
                <div class="p-6 md:p-8 text-gray-900 animate-slideUp">
                    <h3 class="text-lg font-medium mb-4">Rekap Stok Masuk Tercatat</h3>
                    <form method="GET" action="{{ route('staf.stok-masuk.index') }}" id="stok-filter-form" class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label for="tanggal_filter" class="text-sm font-medium">Tampilkan Tanggal:</label>
                            <x-text-input id="tanggal_filter" type="date" name="tanggal" :value="$selectedDate" class="py-1 w-full" />
                        </div>
                        <div>
                            <label for="search" class="text-sm font-medium">Cari Bahan:</label>
                            <x-text-input id="search" type="text" name="search" :value="$search ?? ''" placeholder="Nama bahan..." class="py-1 w-full" />
                        </div>
                        <div class="hidden"></div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const form = document.getElementById('stok-filter-form');
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
                    @if($stokMasukHarian->isEmpty())
                        <p class="text-center text-gray-500 mt-4">Belum ada data stok masuk untuk tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.</p>
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
                                return route('staf.stok-masuk.index', array_merge($baseQuery, [
                                    'sort_by' => $col,
                                    'sort_dir' => $nextDir,
                                ]));
                            };
                        @endphp
                        <table class="min-w-full divide-y divide-gray-200 border text-sm sm:text-base">
                            <thead class="bg-gray-50">
                                <tr>
                                    @php $icons = $sortIcons('nama_bahan'); @endphp
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <a href="{{ $sortLink('nama_bahan') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 py-1 px-2 rounded hover:bg-indigo-100">
                                            <span class="font-semibold">Nama Bahan</span>
                                            <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                            </span>
                                        </a>
                                    </th>
                                    @php $icons = $sortIcons('total_masuk'); @endphp
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <a href="{{ $sortLink('total_masuk') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 py-1 px-2 rounded hover:bg-indigo-100">
                                            <span class="font-semibold">Total Masuk</span>
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
                                    <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($stokMasukHarian as $stok)
                                    <tr class="hover:bg-blue-50 transition-colors duration-200">
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ $stok->nama_bahan }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ number_format($stok->total_masuk, 0, ',', '.') }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($stok->last_recorded)->format('H:i') }}</td>
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ $stok->satuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table
                        </x-responsive-table>
                        </div>

                        <!-- Mobile sort controls -->
                        <div class="md:hidden mb-3">
                            <form action="{{ route('staf.stok-masuk.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                                <input type="hidden" name="tanggal" value="{{ $selectedDate }}" />
                                <input type="hidden" name="search" value="{{ $search }}" />
                                <input type="hidden" name="sort_dir" value="{{ $sort_dir }}" />

                                <label class="text-xs text-gray-600" for="mobile_sort">Urutkan:</label>
                                <select id="mobile_sort" name="sort_by" class="border rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                                    <option value="nama_bahan" @selected(($sort_by ?? 'last_recorded') === 'nama_bahan')>Nama Bahan</option>
                                    <option value="total_masuk" @selected(($sort_by ?? 'last_recorded') === 'total_masuk')>Total Masuk</option>
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
                            @foreach ($stokMasukHarian as $stok)
                                <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $stok->nama_bahan }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Terakhir dicatat: {{ \Carbon\Carbon::parse($stok->last_recorded)->format('H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">Total Masuk</p>
                                            <p class="text-sm font-semibold">{{ number_format($stok->total_masuk, 0, ',', '.') }} {{ $stok->satuan }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>