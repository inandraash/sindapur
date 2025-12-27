<x-app-layout>
    <x-slot name="pageTitle">Atur Resep - {{ $menu->nama_menu }}</x-slot>
    
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.menu.index') }}" class="p-2 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Atur Resep untuk: <span class="font-bold">{{ $menu->nama_menu }}</span>
            </h2>
        </div>
    </x-slot>

    <div x-data="{ editModalOpen: false, deleteModalOpen: false, editingResep: null, deletingResep: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900">Tambah Bahan ke Resep</h2>
                    <form method="POST" action="{{ route('admin.resep.store', $menu) }}" class="mt-6 space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="bahan_baku_id" value="Nama Bahan Baku" />
                            <select name="bahan_baku_id" id="bahan_baku_id" class="block mt-1 w-full ...">
                                <option disabled selected>-- Pilih Bahan --</option>
                                @foreach($bahanBakus as $bahan)
                                    <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="jumlah_dibutuhkan" value="Jumlah Dibutuhkan per Porsi" />
                            <x-text-input id="jumlah_dibutuhkan" name="jumlah_dibutuhkan" type="number" step="0.01" class="mt-1 block w-full" required />
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>Tambahkan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between">
                    <h2 class="text-lg font-medium text-gray-900 mb-4 mr-4">Daftar Bahan Saat Ini</h2>
                    <form id="filter-form" method="GET" action="{{ route('admin.resep.index', $menu) }}" class="flex items-end gap-3 mt-4 md:mt-0">
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
                <table class="min-w-full divide-y divide-gray-200 text-sm sm:text-base">
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
                                $sortLink = function($col) use ($baseQuery, $sort_by, $sort_dir, $menu) {
                                    $isActive = $sort_by === $col;
                                    $nextDir = ($isActive && $sort_dir === 'asc') ? 'desc' : 'asc';
                                    return route('admin.resep.index', array_merge(['menu' => $menu], $baseQuery, [
                                        'sort_by' => $col,
                                        'sort_dir' => $nextDir,
                                    ]));
                                };
                            @endphp
                            <tr>
                            @php $icons = $sortIcons('nama_bahan'); @endphp
                            <th class="px-3 sm:px-6 py-2 sm:py-3">
                                <a href="{{ $sortLink('nama_bahan') }}" class="inline-flex items-center gap-1 group hover:text-indigo-500 transition duration-150 ease-in-out cursor-pointer py-1 px-2 rounded hover:bg-indigo-50">
                                    <span class="font-semibold">Nama Bahan</span>
                                    <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-500 transition duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                    </span>
                                </a>
                            </th>
                            @php $icons = $sortIcons('jumlah_dibutuhkan'); @endphp
                            <th class="px-3 sm:px-6 py-2 sm:py-3">
                                <a href="{{ $sortLink('jumlah_dibutuhkan') }}" class="inline-flex items-center gap-1 group hover:text-indigo-500 transition duration-150 ease-in-out cursor-pointer py-1 px-2 rounded hover:bg-indigo-50">
                                    <span class="font-semibold">Jumlah</span>
                                    <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-500 transition duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                    </span>
                                </a>
                            </th>
                            @php $icons = $sortIcons('satuan'); @endphp
                            <th class="px-3 sm:px-6 py-2 sm:py-3">
                                <a href="{{ $sortLink('satuan') }}" class="inline-flex items-center gap-1 group hover:text-indigo-500 transition duration-150 ease-in-out cursor-pointer py-1 px-2 rounded hover:bg-indigo-50">
                                    <span class="font-semibold">Satuan</span>
                                    <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-500 transition duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                    </span>
                                </a>
                            </th>
                            <th class="relative px-3 sm:px-6 py-2 sm:py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($reseps as $resep)
                                <tr class="hover:bg-orange-50 transition-colors duration-200">
                                <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $resep->bahanBaku->nama_bahan }}</td>
                                <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $resep->jumlah_dibutuhkan }}</td>
                                <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $resep->bahanBaku->satuan }}</td>
                                <td class="px-3 sm:px-6 py-2 sm:py-4 text-right">
                                    <button @click="editingResep = {{ $resep->load('bahanBaku')->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-900 hover:underline mr-4 transition duration-200">
                                        Edit
                                    </button>
                                    <button @click="deletingResep = {{ $resep->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900 hover:underline transition duration-200">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 sm:px-6 py-2 sm:py-4 text-center text-gray-500">
                                    Belum ada bahan yang ditambahkan ke resep ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </x-responsive-table>
                    </div>

                    <!-- Mobile stacked cards -->
                    <div class="md:hidden space-y-3">
                        <div class="flex items-end gap-2">
                            <form id="m-sort-form-resep" method="GET" action="{{ route('admin.resep.index', $menu) }}" class="flex items-end gap-2">
                                <input type="hidden" name="search" value="{{ $search ?? '' }}" />
                                <div>
                                    <x-input-label for="sort_by_m" value="Urut" />
                                    <select id="sort_by_m" name="sort_by" class="border-gray-300 rounded-md shadow-sm py-1">
                                        <option value="nama_bahan" @selected(($sortBy ?? '')==='nama_bahan')>Nama</option>
                                        <option value="jumlah_dibutuhkan" @selected(($sortBy ?? '')==='jumlah_dibutuhkan')>Jumlah</option>
                                        <option value="satuan" @selected(($sortBy ?? '')==='satuan')>Satuan</option>
                                    </select>
                                </div>
                                <input type="hidden" name="sort_dir" value="{{ $sortDir ?? 'asc' }}" />
                                <x-primary-button class="ml-1">Terapkan</x-primary-button>
                            </form>
                            <a href="{{ route('admin.resep.index', ['menu' => $menu, 'search' => $search ?? null, 'sort_by' => $sortBy ?? 'nama_bahan', 'sort_dir' => ($sortDir ?? 'asc')==='asc' ? 'desc' : 'asc']) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-xs">
                                Arah: {{ ($sortDir ?? 'asc')==='asc' ? 'Naik' : 'Turun' }}
                            </a>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const form = document.getElementById('m-sort-form-resep');
                                const select = document.getElementById('sort_by_m');
                                select && select.addEventListener('change', () => form.submit());
                            });
                        </script>
                        @forelse ($reseps as $resep)
                            <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $resep->bahanBaku->nama_bahan }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Satuan: {{ $resep->bahanBaku->satuan }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Jumlah</p>
                                        <p class="text-sm font-semibold">{{ $resep->jumlah_dibutuhkan }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-end gap-3">
                                    <button @click="editingResep = {{ $resep->load('bahanBaku')->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-800 hover:underline text-sm transition duration-200">Edit</button>
                                    <button @click="deletingResep = {{ $resep->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-800 hover:underline text-sm transition duration-200">Hapus</button>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center">Belum ada bahan yang ditambahkan ke resep ini.</p>
                        @endforelse
                    </div>
            </div>
        </div>

        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="editModalOpen" x-transition @click="editModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <div x-show="editModalOpen" x-transition class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Edit Jumlah Bahan
                    </h3>
                    <div class="mt-2" x-if="editingResep">
                        <p class="text-sm text-gray-500">
                            Ubah kuantitas <strong x-text="editingResep.bahan_baku.nama_bahan"></strong> yang dibutuhkan per porsi.
                        </p>
                    </div>

                    <form method="POST" :action="'{{ route('admin.resep.update', ['resep' => 'REPLACE_ME']) }}'.replace('REPLACE_ME', editingResep ? editingResep.id : '')" class="mt-4" x-if="editingResep">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="jumlah_dibutuhkan_edit" value="Jumlah Dibutuhkan Baru" />
                            <x-text-input id="jumlah_dibutuhkan_edit" name="jumlah_dibutuhkan" type="number" step="0.01" class="mt-1 block w-full" x-model="editingResep.jumlah_dibutuhkan" required />
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button @click="editModalOpen = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border ...">Batal</button>
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="deleteModalOpen" x-transition @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <div x-show="deleteModalOpen" x-transition class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        Konfirmasi Penghapusan
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Anda yakin ingin menghapus bahan <strong x-text="deletingResep ? deletingResep.bahan_baku.nama_bahan : ''"></strong> dari resep ini?
                        </p>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button @click="deleteModalOpen = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-transparent rounded-md hover:bg-gray-200 focus:outline-none">
                            Batal
                        </button>

                        <form method="POST" 
                            :action="'{{ route('admin.resep.destroy', ['resep' => 'REPLACE_ME']) }}'.replace('REPLACE_ME', deletingResep ? deletingResep.id : '')" 
                            x-if="deletingResep">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>