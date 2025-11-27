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
                <h2 class="text-lg font-medium text-gray-900 mb-4">Daftar Bahan Saat Ini</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                            <th class="relative px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($reseps as $resep)
                            <tr>
                                <td class="px-6 py-4">{{ $resep->bahanBaku->nama_bahan }}</td>
                                <td class="px-6 py-4">{{ $resep->jumlah_dibutuhkan }}</td>
                                <td class="px-6 py-4">{{ $resep->bahanBaku->satuan }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="editingResep = {{ $resep->load('bahanBaku')->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        Edit
                                    </button>
                                    <button @click="deletingResep = {{ $resep->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada bahan yang ditambahkan ke resep ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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