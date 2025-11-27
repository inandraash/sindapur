<x-app-layout>
    <x-slot name="pageTitle">Manajemen Bahan Baku</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Bahan Baku
        </h2>
    </x-slot>

    <div x-data="{ addModalOpen: @json($errors->any()), editModalOpen: false, deleteModalOpen: false, editingBahanBaku: null, deletingBahanBaku: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <button @click="addModalOpen = true" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Tambah Bahan Baku Baru
                    </button>

                    <table class="min-w-full divide-y divide-gray-200 mt-6">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 ...">Nama Bahan</th>
                                <th class="px-6 py-3 ...">Stok Terkini</th>
                                <th class="px-6 py-3 ...">Satuan</th>
                                <th class="relative px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($bahanBakus as $bahan)
                                <tr>
                                    <td class="px-6 py-4">{{ $bahan->nama_bahan }}</td>
                                    <td class="px-6 py-4">{{ $bahan->stok_terkini }}</td>
                                    <td class="px-6 py-4">{{ $bahan->satuan }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="editingBahanBaku = {{ $bahan->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button @click="deletingBahanBaku = {{ $bahan->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="addModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl 2xl:max-w-2xl">
                    <div class="flex items-center justify-between space-x-4"><h1 class="text-xl font-medium text-gray-800">Tambah Bahan Baku Baru</h1><button @click="addModalOpen = false" class="text-gray-600 focus:outline-none hover:text-gray-700"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button></div>
                    <form method="POST" action="{{ route('staf.bahan-baku.store') }}" class="mt-6">
                        @csrf
                        <div><x-input-label for="nama_bahan" value="Nama Bahan" /><x-text-input id="nama_bahan" class="block mt-1 w-full" type="text" name="nama_bahan" :value="old('nama_bahan')" required autofocus /><x-input-error :messages="$errors->get('nama_bahan')" class="mt-2" /></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div><x-input-label for="stok_terkini" value="Stok Awal" /><x-text-input id="stok_terkini" class="block mt-1 w-full" type="number" step="0.01" name="stok_terkini" :value="old('stok_terkini')" required /><x-input-error :messages="$errors->get('stok_terkini')" class="mt-2" /></div>
                            <div><x-input-label for="satuan" value="Satuan (kg, liter, pcs)" /><x-text-input id="satuan" class="block mt-1 w-full" type="text" name="satuan" :value="old('satuan')" required /><x-input-error :messages="$errors->get('satuan')" class="mt-2" /></div>
                        </div>
                        <div class="flex items-center justify-end mt-6"><button type="button" @click="addModalOpen = false" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</button><x-primary-button>Simpan</x-primary-button></div>
                    </form>
                </div>
            </div>
        </div>
        

        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition @click="editModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
                <div x-show="editModalOpen" x-transition class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl 2xl:max-w-2xl">
                    <div class="flex items-center justify-between space-x-4"><h1 class="text-xl font-medium text-gray-800">Edit Bahan Baku</h1><button @click="editModalOpen = false" class="text-gray-600 focus:outline-none hover:text-gray-700"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button></div>
                    <form method="POST" :action="editingBahanBaku ? `/staf/bahan-baku/${editingBahanBaku.id}` : '#'" class="mt-6" x-if="editingBahanBaku">
                        @csrf
                        @method('PUT')
                        <div><x-input-label for="nama_bahan_edit" value="Nama Bahan" /><x-text-input id="nama_bahan_edit" class="block mt-1 w-full" type="text" name="nama_bahan" x-model="editingBahanBaku.nama_bahan" required /><x-input-error :messages="$errors->get('nama_bahan')" class="mt-2" /></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div><x-input-label for="stok_terkini_edit" value="Stok Terkini" /><x-text-input id="stok_terkini_edit" class="block mt-1 w-full" type="number" step="0.01" name="stok_terkini" x-model="editingBahanBaku.stok_terkini" required /><x-input-error :messages="$errors->get('stok_terkini')" class="mt-2" /></div>
                            <div><x-input-label for="satuan_edit" value="Satuan" /><x-text-input id="satuan_edit" class="block mt-1 w-full" type="text" name="satuan" x-model="editingBahanBaku.satuan" required /><x-input-error :messages="$errors->get('satuan')" class="mt-2" /></div>
                        </div>
                        <div class="flex items-center justify-end mt-6"><button type="button" @click="editModalOpen = false" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</button><x-primary-button>Perbarui</x-primary-button></div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                <div x-show="deleteModalOpen" x-transition @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <div x-show="deleteModalOpen" x-transition class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
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