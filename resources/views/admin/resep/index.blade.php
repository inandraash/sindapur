<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Atur Resep untuk: <span class="font-bold">{{ $menu->nama_menu }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
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

            {{-- Tabel Bahan yang Sudah Ada --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Daftar Bahan Saat Ini</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 ...">Nama Bahan</th>
                            <th class="px-6 py-3 ...">Jumlah</th>
                            <th class="px-6 py-3 ...">Satuan</th>
                            <th class="relative px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($reseps as $resep)
                            <tr>
                                <td class="px-6 py-4">{{ $resep->bahanBaku->nama_bahan }}</td>
                                <td class="px-6 py-4">{{ $resep->jumlah_dibutuhkan }}</td>
                                <td class="px-6 py-4">{{ $resep->bahanBaku->satuan }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="text-red-600 hover:text-red-900">Hapus</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>