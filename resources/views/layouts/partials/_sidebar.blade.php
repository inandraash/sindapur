<div 
    class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white p-4 transform transition-transform duration-300 ease-in-out shadow-xl"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
    x-cloak
>
    <div class="flex justify-center items-center mb-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo_2.png') }}" alt="SINDAPUR" class="h-24 object-contain" />
        </a>
    </div>

    @php
        $navItems = [
            [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />',
                'show' => true,
                'match' => ['dashboard']
            ],
            [
                'label' => 'Manajemen Bahan Baku',
                'route' => 'staf.bahan-baku.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2h-4l-2-2-2 2H6a2 2 0 00-2 2v7" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13h16v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6z" />',
                'show' => Auth::check() && Auth::user()->role->nama_role == 'Staf Dapur',
                'match' => ['staf.bahan-baku.*']
            ],
            [
                'label' => 'Catat Stok Masuk',
                'route' => 'staf.stok-masuk.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z" />
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m0 0l-3-3m3 3l3-3" />',
                'show' => Auth::check() && Auth::user()->role->nama_role == 'Staf Dapur',
                'match' => ['staf.stok-masuk.*']
            ],
            [
                'label' => 'Catat Penjualan Harian',
                'route' => 'staf.penjualan.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2M7 9h10M5 7h14a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />',
                'show' => Auth::check() && Auth::user()->role->nama_role == 'Staf Dapur',
                'match' => ['staf.penjualan.*']
            ],
            [
                'label' => 'Manajemen Pengguna',
                'route' => 'admin.pengguna.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a4 4 0 100-8 4 4 0 000 8z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20a6 6 0 1112 0H6z" />',
                'show' => Auth::check() && Auth::user()->role->nama_role == 'Admin',
                'match' => ['admin.pengguna.*']
            ],
            [
                'label' => 'Manajemen Menu',
                'route' => 'admin.menu.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />',
                'show' => Auth::check() && Auth::user()->role->nama_role == 'Admin',
                'match' => ['admin.menu.*']
            ],
            [
                'label' => 'Laporan Lengkap',
                'route' => 'admin.laporan.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m6 0V9a2 2 0 012-2h2a2 2 0 012 2v8m0 0V9a2 2 0 012-2h2a2 2 0 012 2v8" />',
                'show' => Auth::check() && Auth::user()->role->nama_role == 'Admin',
                'match' => ['admin.laporan.*']
            ],
            [
                'label' => 'Rekomendasi Belanja',
                'route' => 'prediksi.index',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />',
                'show' => true,
                'match' => ['prediksi.index']
            ],
        ];
    @endphp

    <nav>
       <ul class="space-y-1">
            @foreach ($navItems as $item)
                @if ($item['show'])
                    @php $active = request()->routeIs(...$item['match']); @endphp
                    <li>
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-md transition duration-150 ease-out
                           {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-200 hover:bg-gray-800 hover:text-white' }}">
                            <svg class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {!! $item['icon'] !!}
                            </svg>
                            <span class="text-sm">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</div>

<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black opacity-40 z-20 lg:hidden" x-cloak></div>