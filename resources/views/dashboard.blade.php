<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if (Auth::user()->role->nama_role == 'Admin')
                Dashboard Manajerial
            @else
                Dashboard Operasional
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @php
                        $role = Auth::user()->role->nama_role;
                    @endphp

                    <h3 class="text-lg font-medium mb-6">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    @if ($role == 'Admin')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 p-6 rounded-lg animate-slideUp hover:shadow-lg hover:scale-105 transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-green-600 font-medium">Pendapatan Hari Ini</p>
                                        <p class="text-3xl font-bold text-green-700 mt-2">
                                            <span class="mr-1">Rp</span>
                                            <span class="js-countup" data-target="{{ $pendapatanHariIni }}" data-decimals="0"></span>
                                        </p>
                                    </div>
                                    <svg class="w-12 h-12 text-green-200 animate-pulse-custom" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 p-6 rounded-lg animate-slideUp hover:shadow-lg hover:scale-105 transition-all duration-300" style="animation-delay: 0.1s;">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-blue-600 font-medium">Pendapatan Bulan Ini</p>
                                        <p class="text-3xl font-bold text-blue-700 mt-2">
                                            <span class="mr-1">Rp</span>
                                            <span class="js-countup" data-target="{{ $pendapatanBulanIni }}" data-decimals="0" data-delay="120"></span>
                                        </p>
                                    </div>
                                    <svg class="w-12 h-12 text-blue-200 animate-pulse-custom" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 p-6 rounded-lg animate-slideUp hover:shadow-lg hover:scale-105 transition-all duration-300" style="animation-delay: 0.2s;">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-purple-600 font-medium">Total Transaksi Bulan Ini</p>
                                        <p class="text-3xl font-bold text-purple-700 mt-2">
                                            <span class="js-countup" data-target="{{ (int)$jumlahTransaksiBulanIni }}" data-decimals="0" data-delay="200"></span>
                                        </p>
                                    </div>
                                    <svg class="w-12 h-12 text-purple-200 animate-pulse-custom" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="bg-white border border-gray-200 p-4 sm:p-6 rounded-lg animate-slideUp hover:shadow-lg transition-all duration-300">
                                <h4 class="font-bold text-base sm:text-lg mb-4 text-gray-800">Tren Pendapatan (7 Hari)</h4>
                                <div class="relative w-full h-48 sm:h-64">
                                    <canvas id="trenHarianChart"></canvas>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-200 p-4 sm:p-6 rounded-lg animate-slideUp hover:shadow-lg transition-all duration-300" style="animation-delay: 0.1s;">
                                <h4 class="font-bold text-base sm:text-lg mb-4 text-gray-800">Tren Pendapatan (12 Bulan)</h4>
                                <div class="relative w-full h-48 sm:h-64">
                                    <canvas id="trenBulananChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 p-4 sm:p-6 rounded-lg animate-slideUp hover:shadow-lg transition-all duration-300">
                            <h4 class="font-bold text-base sm:text-lg mb-4 text-gray-800">Menu Paling Laris (Bulan Ini)</h4>
                            <!-- Desktop/Tablet table -->
                            <div class="overflow-x-auto -mx-4 sm:mx-0 hidden md:block">
                                <x-responsive-table>
                                <table class="min-w-full border-collapse text-sm sm:text-base">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-3 text-left whitespace-nowrap">Menu</th>
                                            <th class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-3 text-right whitespace-nowrap">Total Porsi</th>
                                            <th class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-3 text-right whitespace-nowrap hidden md:table-cell">Harga/Porsi</th>
                                            <th class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-3 text-right whitespace-nowrap">Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($menuLaris as $menu)
                                            <tr class="hover:bg-gray-50">
                                                <td class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">{{ $menu->menu->nama_menu }}</td>
                                                <td class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-4 text-right font-medium whitespace-nowrap">{{ (int)$menu->total_porsi }}</td>
                                                <td class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-4 text-right whitespace-nowrap hidden md:table-cell">Rp {{ number_format($menu->menu->harga, 0, ',', '.') }}</td>
                                                <td class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-4 text-right font-bold text-green-600 whitespace-nowrap">
                                                    Rp {{ number_format($menu->total_porsi * $menu->menu->harga, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="border border-gray-300 px-3 sm:px-6 py-2 sm:py-4 text-center text-gray-500">Belum ada data penjualan bulan ini</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                </x-responsive-table>
                            </div>

                            <!-- Mobile stacked cards -->
                            <div class="md:hidden space-y-3">
                                @forelse ($menuLaris as $menu)
                                    <div class="border border-gray-200 rounded-lg p-3 animate-slideUp hover:shadow-md hover:bg-gray-50 transition-all duration-300">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $menu->menu->nama_menu }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">Harga/Porsi: Rp {{ number_format($menu->menu->harga, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-700">Porsi: <span class="font-medium">{{ (int)$menu->total_porsi }}</span></p>
                                                <p class="text-sm font-bold text-green-600">Total: Rp {{ number_format($menu->total_porsi * $menu->menu->harga, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center">Belum ada data penjualan bulan ini</p>
                                @endforelse
                            </div>
                        </div>

                    @elseif ($role == 'Staf Dapur')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-slideUp">

                            <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 animate-slideUp">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <h4 class="font-bold text-gray-800">Perhatian Stok</h4>
                                    <span class="text-xs sm:text-sm text-gray-500">Maksimum 5 item</span>
                                </div>

                                <div class="mt-4 space-y-4">
                                    @forelse ($stokKritis as $bahan)
                                        @php
                                            $stokNow = (float) $bahan->stok_terkini;
                                            $threshold = $bahan->critical_threshold ?? 10;
                                            if (isset($bahan->critical_percent) && $bahan->critical_percent !== null) {
                                                $percent = (int) max(0, min(100, $bahan->critical_percent));
                                            } else {
                                                $percent = (int) max(0, min(100, round(($stokNow / max(1, $threshold)) * 100)));
                                            }
                                            $isCritical = $stokNow <= $threshold;
                                        @endphp

                                        <div class="p-3 bg-orange-50 border border-orange-100 rounded-lg animate-slideUp hover:shadow-sm transition-shadow duration-300">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h5 class="font-semibold text-gray-800">{{ $bahan->nama_bahan }}</h5>
                                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium "
                                                              style="background-color: {{ $isCritical ? '#fee2e2' : '#fef3c7' }}; color: {{ $isCritical ? '#b91c1c' : '#92400e' }};">
                                                            Ambang: {{ number_format($threshold, 0, ',', '.') }} {{ $bahan->satuan ?? '' }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1">Stok sekarang: <span class="font-medium">{{ number_format($bahan->stok_terkini, 0, ',', '.') }} {{ $bahan->satuan }}</span></p>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-sm font-semibold {{ $isCritical ? 'text-red-600' : 'text-yellow-700' }}">{{ $percent }}%</span>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                    <div class="h-2 rounded-full" style="width: {{ $percent }}%; background-color: {{ $isCritical ? '#ef4444' : '#f59e0b' }};"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500">Semua stok dalam batas aman.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 animate-slideUp" style="animation-delay: 0.1s;">
                                <h4 class="font-bold text-sm sm:text-base">Pemakaian Bahan Terbanyak (7 Hari)</h4>
                                <div class="relative w-full mt-4 h-44 sm:h-56">
                                    <canvas id="pemakaianChart"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fmt = (value, decimals = 0) => new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            }).format(value);

            const animateCounts = () => {
                document.querySelectorAll('.js-countup').forEach(el => {
                    const target = parseFloat(el.dataset.target || '0');
                    const decimals = parseInt(el.dataset.decimals || '0', 10);
                    const delay = parseInt(el.dataset.delay || '0', 10);
                    const duration = parseInt(el.dataset.duration || '800', 10);
                    const start = 0;
                    const startTime = performance.now() + delay;

                    const step = now => {
                        if (now < startTime) {
                            requestAnimationFrame(step);
                            return;
                        }
                        const progress = Math.min((now - startTime) / duration, 1);
                        const value = start + (target - start) * progress;
                        el.textContent = fmt(value, decimals);
                        if (progress < 1) requestAnimationFrame(step);
                    };

                    requestAnimationFrame(step);
                });
            };

            animateCounts();

            @if(Auth::user()->role->nama_role == 'Admin')
                @if(isset($trenPendapatanLabels) && isset($trenPendapatanData))
                    const ctxHarian = document.getElementById('trenHarianChart');
                    const labelsHarian = @json($trenPendapatanLabels);
                    const dataHarian = @json($trenPendapatanData);
        
                    new Chart(ctxHarian, {
                        type: 'line',
                        data: {
                            labels: labelsHarian,
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: dataHarian,
                                borderColor: 'rgba(34, 197, 94, 1)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        },
                                        font: { size: 11 }
                                    }
                                },
                                x: {
                                    ticks: { font: { size: 11 } }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: { font: { size: 12 } }
                                }
                            }
                        }
                    });
                @endif

                @if(isset($trenBulanLabels) && isset($trenBulanData))
                    const ctxBulanan = document.getElementById('trenBulananChart');
                    const labelsBulanan = @json($trenBulanLabels);
                    const dataBulanan = @json($trenBulanData);
        
                    new Chart(ctxBulanan, {
                        type: 'line',
                        data: {
                            labels: labelsBulanan,
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: dataBulanan,
                                borderColor: 'rgba(59, 130, 246, 1)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        },
                                        font: { size: 11 }
                                    }
                                },
                                x: {
                                    ticks: { font: { size: 10 } }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: { font: { size: 12 } }
                                }
                            }
                        }
                    });
                @endif
            @elseif(Auth::user()->role->nama_role == 'Staf Dapur')
                @if(isset($chartLabels) && isset($chartData))
                    const ctx = document.getElementById('pemakaianChart');
                    
                    const labels = @json($chartLabels);
                    const data = @json($chartData);
        
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Pemakaian',
                                data: data,
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(153, 102, 255, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(153, 102, 255, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { font: { size: 11 } }
                                },
                                x: {
                                    ticks: { font: { size: 10 } }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                @endif
            @endif
        });
    </script>
</x-app-layout>