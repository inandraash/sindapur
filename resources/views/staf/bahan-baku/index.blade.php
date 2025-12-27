<x-app-layout>
    <x-slot name="pageTitle">Manajemen Bahan Baku</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Bahan Baku
        </h2>
    </x-slot>

    <div x-data="{ addModalOpen: @json($errors->any()), editModalOpen: false, deleteModalOpen: false, editingBahanBaku: null, deletingBahanBaku: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-slideUp">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
                        <button @click="addModalOpen = true" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 hover:scale-105 transition-all duration-200">
                            Tambah Bahan Baku Baru
                        </button>
                        <form id="filter-form" method="GET" action="{{ route('staf.bahan-baku.index') }}" class="flex items-end gap-3">
                            <div>
                                <x-input-label for="search" value="Cari" />
                                <x-text-input id="search" type="text" name="search" value="{{ $search ?? '' }}" class="py-1" placeholder="Nama/Satuan" />
                            </div>
                            <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'nama_bahan' }}" />
                            <input type="hidden" name="sort_dir" value="{{ $sortDir ?? 'asc' }}" />
                        </form>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const form = document.getElementById('filter-form');
                            const input = document.getElementById('search');
                            let t;
                            input.addEventListener('input', () => {
                                clearTimeout(t);
                                t = setTimeout(() => form.submit(), 300);
                            });
                        });
                    </script>

                    <!-- Desktop/Tablet table -->
                    <div class="hidden md:block">
                    <x-responsive-table>
                    <table class="min-w-full divide-y divide-gray-200 mt-6 text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            @php
                                $baseQuery = request()->except(['sort_by','sort_dir']);
                                $sort_by = request()->input('sort_by', 'nama_bahan');
                                $sort_dir = request()->input('sort_dir', 'asc');
                                $sortIcons = function($col) use ($sort_by, $sort_dir) {
                                    $isActive = $sort_by === $col;
                                    return [
                                        'asc' => $isActive && $sort_dir === 'asc',
                                        'desc' => $isActive && $sort_dir === 'desc',
                                    ];
                                };
                                $sortLink = function($col) use ($baseQuery, $sort_by, $sort_dir) {
                                    $isActive = $sort_by === $col;
                                    $nextDir = ($isActive && $sort_dir === 'asc') ? 'desc' : 'asc';
                                    return route('staf.bahan-baku.index', array_merge($baseQuery, [
                                        'sort_by' => $col,
                                        'sort_dir' => $nextDir,
                                    ]));
                                };
                            @endphp
                            <tr>
                                @php $icons = $sortIcons('nama_bahan'); @endphp
                                <th class="px-3 sm:px-6 py-2 sm:py-3">
                                    <a href="{{ $sortLink('nama_bahan') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 cursor-pointer py-1 px-2 rounded hover:bg-indigo-100">
                                        <span class="font-semibold">Nama Bahan</span>
                                        <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                        </span>
                                    </a>
                                </th>
                                @php $icons = $sortIcons('stok_terkini'); @endphp
                                <th class="px-3 sm:px-6 py-2 sm:py-3">
                                    <a href="{{ $sortLink('stok_terkini') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 cursor-pointer py-1 px-2 rounded hover:bg-indigo-100">
                                        <span class="font-semibold">Stok Terkini</span>
                                        <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                        </span>
                                    </a>
                                </th>
                                @php $icons = $sortIcons('satuan'); @endphp
                                <th class="px-3 sm:px-6 py-2 sm:py-3">
                                    <a href="{{ $sortLink('satuan') }}" class="inline-flex items-center gap-1 group hover:text-indigo-600 transition duration-200 cursor-pointer py-1 px-2 rounded hover:bg-indigo-100">
                                        <span class="font-semibold">Satuan</span>
                                        <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-600 transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-600 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                        </span>
                                    </a>
                                </th>
                                <th class="relative px-3 sm:px-6 py-2 sm:py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($bahanBakus as $bahan)
                                <tr class="hover:bg-green-50 transition-colors duration-200">
                                    <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $bahan->nama_bahan }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $bahan->stok_terkini }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $bahan->satuan }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 text-right">
                                        <button @click="editingBahanBaku = {{ $bahan->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-900 hover:underline transition duration-200">Edit</button>
                                        <button @click="deletingBahanBaku = {{ $bahan->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900 hover:underline ml-4 transition duration-200">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </x-responsive-table>
                    </div>

                    <!-- Mobile stacked cards -->
                    <div class="md:hidden mt-6 space-y-3">
                        <div class="flex items-end gap-2">
                            <form id="m-sort-form-bahan" method="GET" action="{{ route('staf.bahan-baku.index') }}" class="flex items-end gap-2">
                                <input type="hidden" name="search" value="{{ $search ?? '' }}" />
                                <div>
                                    <x-input-label for="sort_by_m" value="Urut" />
                                    <select id="sort_by_m" name="sort_by" class="border-gray-300 rounded-md shadow-sm py-1">
                                        <option value="nama_bahan" @selected(($sortBy ?? '')==='nama_bahan')>Nama</option>
                                        <option value="stok_terkini" @selected(($sortBy ?? '')==='stok_terkini')>Stok</option>
                                        <option value="satuan" @selected(($sortBy ?? '')==='satuan')>Satuan</option>
                                    </select>
                                </div>
                                <input type="hidden" name="sort_dir" value="{{ $sortDir ?? 'asc' }}" />
                                <x-primary-button class="ml-1">Terapkan</x-primary-button>
                            </form>
                            <a href="{{ route('staf.bahan-baku.index', ['search' => $search ?? null, 'sort_by' => $sortBy ?? 'nama_bahan', 'sort_dir' => ($sortDir ?? 'asc')==='asc' ? 'desc' : 'asc']) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-xs">
                                Arah: {{ ($sortDir ?? 'asc')==='asc' ? 'Naik' : 'Turun' }}
                            </a>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const form = document.getElementById('m-sort-form-bahan');
                                const select = document.getElementById('sort_by_m');
                                select && select.addEventListener('change', () => form.submit());
                            });
                        </script>
                        @foreach ($bahanBakus as $bahan)
                            <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $bahan->nama_bahan }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Satuan: {{ $bahan->satuan }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Stok</p>
                                        <p class="text-sm font-semibold">{{ $bahan->stok_terkini }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-end gap-3">
                                    <button @click="editingBahanBaku = {{ $bahan->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-800 hover:underline text-sm transition duration-200">Edit</button>
                                    <button @click="deletingBahanBaku = {{ $bahan->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-800 hover:underline text-sm transition duration-200">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="addModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl 2xl:max-w-2xl">
                    <div class="flex items-center justify-between space-x-4"><h1 class="text-xl font-medium text-gray-800">Tambah Bahan Baku Baru</h1><button @click="addModalOpen = false" class="text-gray-600 hover:text-gray-700 hover:scale-110 focus:outline-none transition duration-200"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button></div>
                    <form method="POST" action="{{ route('staf.bahan-baku.store') }}" class="mt-6">
                        @csrf
                        <div><x-input-label for="nama_bahan" value="Nama Bahan" /><x-text-input id="nama_bahan" class="block mt-1 w-full" type="text" name="nama_bahan" :value="old('nama_bahan')" required autofocus /><x-input-error :messages="$errors->get('nama_bahan')" class="mt-2" /></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div><x-input-label for="stok_terkini" value="Stok Awal" /><x-text-input id="stok_terkini" class="block mt-1 w-full" type="number" step="0.01" name="stok_terkini" :value="old('stok_terkini')" required /><x-input-error :messages="$errors->get('stok_terkini')" class="mt-2" /></div>
                            <div><x-input-label for="satuan" value="Satuan (kg, liter, pcs)" /><x-text-input id="satuan" class="block mt-1 w-full" type="text" name="satuan" :value="old('satuan')" required /><x-input-error :messages="$errors->get('satuan')" class="mt-2" /></div>
                        </div>
                        <div class="mt-4"><x-input-label for="stok_maksimum" value="Stok Maksimum" /><x-text-input id="stok_maksimum" class="block mt-1 w-full" type="number" step="0.01" name="stok_maksimum" :value="old('stok_maksimum')" /><x-input-error :messages="$errors->get('stok_maksimum')" class="mt-2" /></div>
                        <div class="flex items-center justify-end mt-6"><button type="button" @click="addModalOpen = false" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</button><x-primary-button>Simpan</x-primary-button></div>
                    </form>
                </div>
            </div>
        </div>
        

        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="editModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
                <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl 2xl:max-w-2xl">
                    <div class="flex items-center justify-between space-x-4"><h1 class="text-xl font-medium text-gray-800">Edit Bahan Baku</h1><button @click="editModalOpen = false" class="text-gray-600 hover:text-gray-700 hover:scale-110 focus:outline-none transition duration-200"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button></div>
                    <form method="POST" :action="editingBahanBaku ? `/staf/bahan-baku/${editingBahanBaku.id}` : '#'" class="mt-6" x-if="editingBahanBaku">
                        @csrf
                        @method('PUT')
                        <div><x-input-label for="nama_bahan_edit" value="Nama Bahan" /><x-text-input id="nama_bahan_edit" class="block mt-1 w-full" type="text" name="nama_bahan" x-model="editingBahanBaku.nama_bahan" required /><x-input-error :messages="$errors->get('nama_bahan')" class="mt-2" /></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div><x-input-label for="stok_terkini_edit" value="Stok Terkini" /><x-text-input id="stok_terkini_edit" class="block mt-1 w-full" type="number" step="0.01" name="stok_terkini" x-model="editingBahanBaku.stok_terkini" required /><x-input-error :messages="$errors->get('stok_terkini')" class="mt-2" /></div>
                            <div><x-input-label for="satuan_edit" value="Satuan" /><x-text-input id="satuan_edit" class="block mt-1 w-full" type="text" name="satuan" x-model="editingBahanBaku.satuan" required /><x-input-error :messages="$errors->get('satuan')" class="mt-2" /></div>
                        </div>
                        <div class="mt-4"><x-input-label for="stok_maksimum_edit" value="Stok Maksimum" /><x-text-input id="stok_maksimum_edit" class="block mt-1 w-full" type="number" step="0.01" name="stok_maksimum" x-model="editingBahanBaku.stok_maksimum" /><x-input-error :messages="$errors->get('stok_maksimum')" class="mt-2" /></div>
                        <div class="flex items-center justify-end mt-6"><button type="button" @click="editModalOpen = false" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</button><x-primary-button>Perbarui</x-primary-button></div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Konfirmasi Penghapusan</h3>
                    <div class="mt-2"><p class="text-sm text-gray-500">Anda yakin ingin menghapus bahan baku <strong x-text="deletingBahanBaku ? deletingBahanBaku.nama_bahan : ''"></strong>?</p></div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button @click="deleteModalOpen = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-transparent rounded-md hover:bg-gray-200">Batal</button>
                        <form method="POST" :action="deletingBahanBaku ? `/staf/bahan-baku/${deletingBahanBaku.id}` : '#'" x-if="deletingBahanBaku">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>