<x-app-layout>
    <x-slot name="pageTitle">Manajemen Menu</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Menu</h2>
    </x-slot>

    <div 
        x-data="{ deleteModalOpen: false, deletingMenu: null, createModalOpen: false }"
        x-init="$nextTick(() => { if ($refs.createFormHasErrors && $refs.createFormHasErrors.value === '1') createModalOpen = true })"
        class="py-12"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <button @click="createModalOpen = true" type="button" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Tambah Menu Baru
                    </button>

                    <x-responsive-table>
                    <table class="min-w-full divide-y divide-gray-200 mt-6 text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 ...">Nama Menu</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 ...">Harga</th>
                                <th class="relative px-3 sm:px-6 py-2 sm:py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($menus as $menu)
                                <tr>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4">{{ $menu->nama_menu }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 text-right">
                                        <a href="{{ route('admin.resep.index', $menu) }}" class="font-medium text-green-600 hover:text-green-900 mr-4">Atur Resep</a>
                                        <a href="{{ route('admin.menu.edit', $menu) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                        <button @click="deletingMenu = {{ $menu->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </x-responsive-table>
                    
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