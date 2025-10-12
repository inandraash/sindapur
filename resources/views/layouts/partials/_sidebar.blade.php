<div 
    class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-800 text-white p-4 transform transition-transform duration-300 ease-in-out"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
    x-cloak
>
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('dashboard') }}">
            <h1 class="text-2xl font-bold">SINDAPUR</h1>
        </a>
        <button x-on:click="sidebarOpen = false" class="lg:hidden">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav>
       <ul>
            <li class="mb-2">
                <a href="{{ route('dashboard') }}" class="block p-2 rounded hover:bg-gray-700 @if(request()->routeIs('dashboard')) bg-gray-700 @endif">Dashboard</a>
            </li>

            @if (Auth::check() && Auth::user()->role->nama_role == 'Staf Dapur')
            <li class="mb-2">
                <a href="#" class="block p-2 rounded hover:bg-gray-700">Manajemen Stok</a>
            </li>
            <li class="mb-2">
                <a href="#" class="block p-2 rounded hover:bg-gray-700">Catat Penjualan</a>
            </li>
            @endif
            
            @if (Auth::check() && Auth::user()->role->nama_role == 'Admin')
            <li class="mb-2 mt-2">
                <a href="{{ route('admin.pengguna.index') }}" class="block p-2 rounded hover:bg-gray-700">Manajemen Pengguna</a>
            </li>
            <li class="mb-2">
                <a href="#" class="block p-2 rounded hover:bg-gray-700">Manajemen Menu</a>
            </li>
            <li class="mb-2">
                <a href="#" class="block p-2 rounded hover:bg-gray-700">Manajemen Resep</a>
            </li>
            <li class="mb-2">
                <a href="#" class="block p-2 rounded hover:bg-gray-700">Laporan Lengkap</a>
            </li>
            @endif
            <li class="mb-2">
                <a href="#" class="block p-2 rounded hover:bg-gray-700">Rekomendasi Belanja</a>
            </li>
        </ul>
    </nav>
</div>

<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black opacity-50 z-20 lg:hidden" x-cloak></div>