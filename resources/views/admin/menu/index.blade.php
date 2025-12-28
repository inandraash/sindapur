<x-app-layout>
    <x-slot name="pageTitle">Manajemen Menu</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Menu</h2>
    </x-slot>

    <div 
        x-data="{ deleteModalOpen: false, deletingMenu: null, createModalOpen: false }"
        x-init="$nextTick(() => { if ($refs.createFormHasErrors && $refs.createFormHasErrors.value === '1') createModalOpen = true })"
        class="py-2"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
                        <button @click="createModalOpen = true" type="button" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Tambah Menu Baru
                        </button>
                        <form id="filter-form" method="GET" action="{{ route('admin.menu.index') }}" class="flex items-end gap-3">
                            <div>
                                <x-input-label for="search" value="Cari Menu" />
                                <x-text-input id="search" type="text" name="search" value="{{ $search ?? '' }}" class="py-1" placeholder="Nama menu" />
                            </div>
                            <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'nama_menu' }}" />
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
                                $sort_by = request()->input('sort_by', 'nama_menu');
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
                                    return route('admin.menu.index', array_merge($baseQuery, [
                                        'sort_by' => $col,
                                        'sort_dir' => $nextDir,
                                    ]));
                                };
                            @endphp
                            <tr>
                                @php $icons = $sortIcons('nama_menu'); @endphp
                                <th class="px-3 sm:px-6 py-2 sm:py-3">
                                    <a href="{{ $sortLink('nama_menu') }}" class="inline-flex items-center gap-1 group hover:text-indigo-500 transition duration-150 ease-in-out cursor-pointer py-1 px-2 rounded hover:bg-indigo-50">
                                        <span class="font-semibold">Nama Menu</span>
                                        <span class="flex flex-col leading-none text-gray-400 group-hover:text-indigo-500 transition duration-150 ease-in-out">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $icons['asc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5l-4 5h8l-4-5z"/></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 -mt-1 {{ $icons['desc'] ? 'text-indigo-500 font-bold' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l4-5H6l4 5z"/></svg>
                                        </span>
                                    </a>
                                </th>
                                @php $icons = $sortIcons('harga'); @endphp
                                <th class="px-3 sm:px-6 py-2 sm:py-3">
                                    <a href="{{ $sortLink('harga') }}" class="inline-flex items-center gap-1 group hover:text-indigo-500 transition duration-150 ease-in-out cursor-pointer py-1 px-2 rounded hover:bg-indigo-50">
                                        <span class="font-semibold">Harga</span>
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
                            @foreach ($menus as $menu)
                                <tr class="hover:bg-purple-50 transition-colors duration-200">
                                    <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $menu->nama_menu }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 text-right">
                                        <a href="{{ route('admin.resep.index', $menu) }}" class="font-medium text-green-600 hover:text-green-900 hover:underline mr-4 transition duration-200">Atur Resep</a>
                                        <a href="{{ route('admin.menu.edit', $menu) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline mr-4 transition duration-200">Edit</a>
                                        <button @click="deletingMenu = {{ $menu->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900 hover:underline transition duration-200">Hapus</button>
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
                            <form id="m-sort-form-menu" method="GET" action="{{ route('admin.menu.index') }}" class="flex items-end gap-2">
                                <input type="hidden" name="search" value="{{ $search ?? '' }}" />
                                <div>
                                    <x-input-label for="sort_by_m" value="Urut" />
                                    <select id="sort_by_m" name="sort_by" class="border-gray-300 rounded-md shadow-sm py-1">
                                        <option value="nama_menu" @selected(($sortBy ?? '')==='nama_menu')>Nama</option>
                                        <option value="harga" @selected(($sortBy ?? '')==='harga')>Harga</option>
                                    </select>
                                </div>
                                <input type="hidden" name="sort_dir" value="{{ $sortDir ?? 'asc' }}" />
                                <x-primary-button class="ml-1">Terapkan</x-primary-button>
                            </form>
                            <a href="{{ route('admin.menu.index', ['search' => $search ?? null, 'sort_by' => $sortBy ?? 'nama_menu', 'sort_dir' => ($sortDir ?? 'asc')==='asc' ? 'desc' : 'asc']) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-xs">
                                Arah: {{ ($sortDir ?? 'asc')==='asc' ? 'Naik' : 'Turun' }}
                            </a>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const form = document.getElementById('m-sort-form-menu');
                                const select = document.getElementById('sort_by_m');
                                select && select.addEventListener('change', () => form.submit());
                            });
                        </script>
                        @foreach ($menus as $menu)
                            <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $menu->nama_menu }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Harga: Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <a href="{{ route('admin.resep.index', $menu) }}" class="text-green-600 hover:text-green-800 hover:underline text-sm transition duration-200">Atur Resep</a>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.menu.edit', $menu) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline text-sm transition duration-200">Edit</a>
                                    <button @click="deletingMenu = {{ $menu->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-800 hover:underline text-sm transition duration-200">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen px-4 text-center">
                            <div x-show="deleteModalOpen" x-transition @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                            <div x-show="deleteModalOpen" x-transition class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                    Konfirmasi Penghapusan
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Anda yakin ingin menghapus menu <strong x-text="deletingMenu ? deletingMenu.nama_menu : ''"></strong>? Tindakan ini akan menghapus resep terkait.
                                    </p>
                                </div>

                                <div class="mt-4 flex justify-end space-x-2">
                                    <button @click="deleteModalOpen = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-transparent rounded-md hover:bg-gray-200 focus:outline-none">
                                        Batal
                                    </button>

                                    <form method="POST" :action="deletingMenu ? `/admin/menu/${deletingMenu.id}` : '#'" x-if="deletingMenu">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none">
                                            Ya, Hapus Menu
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" x-ref="createFormHasErrors" value="{{ $errors->any() ? '1' : '0' }}" />

        <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="createModalOpen" x-transition @click="createModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <div x-show="createModalOpen" x-transition class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    <div class="flex items-start justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Tambah Menu Baru</h3>
                        <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="mt-4">
                        @if ($errors->any())
                            <div class="mb-3 rounded border border-red-200 bg-red-50 text-red-700 p-3 text-sm">
                                <ul class="list-disc ml-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.menu.store') }}">
                            @csrf
                            <div>
                                <x-input-label for="nama_menu" value="Nama Menu" />
                                <x-text-input id="nama_menu" class="block mt-1 w-full" type="text" name="nama_menu" value="{{ old('nama_menu') }}" required />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="harga" value="Harga" />
                                <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" value="{{ old('harga') }}" required />
                            </div>
                            <div class="flex items-center justify-end mt-6 space-x-2">
                                <button type="button" @click="createModalOpen = false" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Batal</button>
                                <x-primary-button>Simpan</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>