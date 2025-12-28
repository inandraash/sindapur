<x-app-layout>
    <x-slot name="pageTitle">Catat Stok Masuk (Pembelian)</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Stok Masuk (Pembelian)') }}
        </h2>
    </x-slot>

    @php
        $oldBulkStockItems = old('items', [ ['bahan_baku_id' => '', 'jumlah_masuk' => ''] ]);
        if (!is_array($oldBulkStockItems) || count($oldBulkStockItems) === 0) {
            $oldBulkStockItems = [ ['bahan_baku_id' => '', 'jumlah_masuk' => ''] ];
        }
    @endphp

    <div class="py-2" x-data='{
            bulkModalOpen: @json($errors->getBag("bulkStock")->any()),
            bulkItems: @json($oldBulkStockItems),
            addRow() { this.bulkItems.push({ bahan_baku_id: "", jumlah_masuk: "" }); },
            removeRow(i) { this.bulkItems.splice(i,1); if (this.bulkItems.length === 0) { this.bulkItems.push({ bahan_baku_id: "", jumlah_masuk: "" }); } }
        }'>
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
                    
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mt-4">
                        <div class="text-sm text-gray-600">Gunakan formulir cepat untuk 1 item atau klik tombol di kanan untuk banyak item sekaligus.</div>
                        <div class="flex gap-2">
                            <button type="button" @click="bulkModalOpen = true" class="px-4 py-2 bg-slate-700 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1 transition">Input Banyak Sekaligus</button>
                        </div>
                    </div>

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

            {{-- Modal Input Banyak Sekaligus --}}
            <div x-show="bulkModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
                <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
                    <div x-show="bulkModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="bulkModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-60 transition-opacity" aria-hidden="true"></div>
                    <div x-show="bulkModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-5xl p-6 my-10 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Input Banyak Stok Masuk</h3>
                                <p class="text-sm text-slate-600">Satu tanggal untuk semua baris, baris kosong akan diabaikan.</p>
                            </div>
                            <button @click="bulkModalOpen = false" class="text-slate-500 hover:text-slate-700">Tutup</button>
                        </div>

                        <form method="POST" action="{{ route('staf.stok-masuk.bulk-store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="bulk_tanggal_masuk" value="Tanggal Masuk" />
                                <x-text-input id="bulk_tanggal_masuk" class="block mt-1 w-full md:w-1/2" type="date" name="tanggal_masuk" :value="old('tanggal_masuk', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->getBag('bulkStock')->get('tanggal_masuk')" class="mt-2" />
                            </div>

                            @if($errors->getBag('bulkStock')->any())
                                <div class="p-3 bg-red-100 text-red-700 rounded border border-red-300">
                                    <ul class="list-disc list-inside text-sm space-y-1">
                                        @foreach($errors->getBag('bulkStock')->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-slate-700">Bahan Baku</th>
                                            <th class="px-3 py-2 text-left text-slate-700">Jumlah Masuk</th>
                                            <th class="px-3 py-2 text-center w-24 text-slate-700">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-for="(row, idx) in bulkItems" :key="idx">
                                            <tr>
                                                <td class="px-3 py-2 min-w-[220px]">
                                                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" x-bind:name="'items[' + idx + '][bahan_baku_id]'" x-model="row.bahan_baku_id">
                                                        <option value="">-- Pilih Bahan --</option>
                                                        @foreach($bahanBakus as $bahan)
                                                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <x-text-input class="w-full" type="number" step="0.01" x-bind:name="'items[' + idx + '][jumlah_masuk]'" x-model="row.jumlah_masuk" />
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <button type="button" @click="removeRow(idx)" class="text-red-600 hover:text-red-700">Hapus</button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex items-center justify-between">
                                <button type="button" @click="addRow()" class="px-3 py-2 bg-slate-100 text-slate-800 rounded-md hover:bg-slate-200 text-sm border border-slate-200">+ Tambah Baris</button>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="bulkModalOpen = false" class="text-sm text-slate-600 hover:text-slate-800">Batal</button>
                                    <x-primary-button class="bg-slate-700 hover:bg-slate-600 focus:ring-slate-400">Simpan Semua</x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm">{{ number_format($stok->total_masuk, 2, ',', '.') }}</td>
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
                                            <p class="text-sm font-semibold">{{ number_format($stok->total_masuk, 2, ',', '.') }} {{ $stok->satuan }}</p>
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