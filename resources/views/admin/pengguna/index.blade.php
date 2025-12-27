<x-app-layout>
    <x-slot name="pageTitle">Manajemen Pengguna</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div x-data="{ addModalOpen: @json($errors->any()), editModalOpen: false, editingUser: null, deleteModalOpen: false, editingUser: null, deletingUser: null }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <button @click="addModalOpen = true" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Tambah Pengguna Baru
                    </button>

                    <!-- Desktop/Tablet table -->
                    <div class="hidden md:block">
                    <x-responsive-table>
                    <table class="min-w-full divide-y divide-gray-200 mt-6 text-sm sm:text-base">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                                <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="relative px-3 sm:px-6 py-2 sm:py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr class="hover:bg-cyan-50 transition-colors duration-200">
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">{{ $user->username }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">{{ $user->role->nama_role }}</td>
                                    <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="editingUser = {{ $user->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-900 hover:underline transition duration-200">
                                            Edit
                                        </button>

                                        <button @click="deletingUser = {{ $user->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900 hover:underline ml-4 transition duration-200">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </x-responsive-table>
                    </div>

                    <!-- Mobile stacked cards -->
                    <div class="md:hidden mt-6 space-y-3">
                        @foreach ($users as $user)
                            <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Username: {{ $user->username }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Role</p>
                                        <p class="text-sm font-semibold">{{ $user->role->nama_role }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-end gap-3">
                                    <button @click="editingUser = {{ $user->toJson() }}; editModalOpen = true" class="text-indigo-600 hover:text-indigo-800 hover:underline text-sm transition duration-200">Edit</button>
                                    <button @click="deletingUser = {{ $user->toJson() }}; deleteModalOpen = true" class="text-red-600 hover:text-red-800 hover:underline text-sm transition duration-200">Hapus</button>
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
                    
                    <div class="flex items-center justify-between space-x-4">
                        <h1 class="text-xl font-medium text-gray-800">Tambah Pengguna Baru</h1>
                        <button @click="addModalOpen = false" class="text-gray-600 focus:outline-none hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>

                    <p class="mt-2 text-sm text-gray-500">
                        Buat akun baru untuk pengguna yang dapat mengakses sistem.
                    </p>

                    <form method="POST" action="{{ route('admin.pengguna.store') }}" class="mt-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="name" value="Nama Lengkap" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="username" value="Username" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="password" value="Password" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="role_id" value="Role" />
                            <select name="role_id" id="role_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option disabled selected>-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                                @endforeach
                            </select>
                             <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="button" @click="addModalOpen = false" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </button>

                            <x-primary-button>
                                {{ __('Simpan Pengguna') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
                
                <div x-show="editModalOpen" x-transition ... @click="editModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <div x-show="editModalOpen" x-transition ... class="inline-block w-full max-w-xl p-8 my-20 overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl 2xl:max-w-2xl">
                    
                    <div class="flex items-center justify-between space-x-4">
                        <h1 class="text-xl font-medium text-gray-800">Edit Pengguna</h1>
                        <button @click="editModalOpen = false" class="text-gray-600 focus:outline-none hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>

                    <p class="mt-2 text-sm text-gray-500">
                        Perbarui informasi akun pengguna. Kosongkan password jika tidak ingin diubah.
                    </p>
                    <form method="POST" :action="editingUser ? `/admin/pengguna/${editingUser.id}` : '#'" class="mt-6" x-if="editingUser">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <x-input-label for="name" value="Nama Lengkap" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" x-model="editingUser.name" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="username" value="Username" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" x-model="editingUser.username" required />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="password" value="Password Baru (Opsional)" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="role_id" value="Peran (Role)" />
                            <select name="role_id" id="role_id" class="block mt-1 w-full ..." x-model="editingUser.role_id">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="button" @click="editModalOpen = false" class="text-sm text-gray-600 ... mr-4">
                                Batal
                            </button>
                            <x-primary-button>
                                Perbarui Pengguna
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                
                <div x-show="deleteModalOpen" x-transition ... @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

                <div x-show="deleteModalOpen" x-transition ... class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                    
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        Konfirmasi Penghapusan
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Anda yakin ingin menghapus pengguna <strong x-text="deletingUser ? deletingUser.nama : ''"></strong>? Tindakan ini tidak dapat dibatalkan secara langsung.
                        </p>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button @click="deleteModalOpen = false" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-transparent rounded-md hover:bg-gray-200 focus:outline-none">
                            Batal
                        </button>

                        <form method="POST" :action="deletingUser ? `/admin/pengguna/${deletingUser.id}` : '#'" x-if="deletingUser">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none">
                                Ya, Hapus Pengguna
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>