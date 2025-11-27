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
                    {{-- TAMPILAN KHUSUS ADMIN --}}
                    @if ($role == 'Admin')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 p-6 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-green-600 font-medium">Pendapatan Hari Ini</p>
                                        <p class="text-3xl font-bold text-green-700 mt-2">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                                    </div>
                                    <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 p-6 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-blue-600 font-medium">Pendapatan Bulan Ini</p>
                                        <p class="text-3xl font-bold text-blue-700 mt-2">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</p>
                                    </div>
                                    <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 p-6 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-purple-600 font-medium">Total Transaksi Bulan Ini</p>
                                        <p class="text-3xl font-bold text-purple-700 mt-2">{{ (int)$jumlahTransaksiBulanIni }}</p>
                                    </div>
                                    <svg class="w-12 h-12 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="bg-white border border-gray-200 p-6 rounded-lg">
                                <h4 class="font-bold text-lg mb-4 text-gray-800">Tren Pendapatan (7 Hari)</h4>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="trenHarianChart"></canvas>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-200 p-6 rounded-lg">
                                <h4 class="font-bold text-lg mb-4 text-gray-800">Tren Pendapatan (12 Bulan)</h4>
                                <div style="position: relative; height: 300px;">
                                    <canvas id="trenBulananChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 p-6 rounded-lg">
                            <h4 class="font-bold text-lg mb-4 text-gray-800">Menu Paling Laris (Bulan Ini)</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full border-collapse">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 px-4 py-2 text-left">Menu</th>
                                            <th class="border border-gray-300 px-4 py-2 text-right">Total Porsi</th>
                                            <th class="border border-gray-300 px-4 py-2 text-right">Harga/Porsi</th>
                                            <th class="border border-gray-300 px-4 py-2 text-right">Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($menuLaris as $menu)
                                            <tr class="hover:bg-gray-50">
                                                <td class="border border-gray-300 px-4 py-2">{{ $menu->menu->nama_menu }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-right font-medium">{{ (int)$menu->total_porsi }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($menu->menu->harga, 0, ',', '.') }}</td>
                                                <td class="border border-gray-300 px-4 py-2 text-right font-bold text-green-600">
                                                    Rp {{ number_format($menu->total_porsi * $menu->menu->harga, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Belum ada data penjualan bulan ini</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    {{-- TAMPILAN KHUSUS STAF DAPUR --}}
                    @elseif ($role == 'Staf Dapur')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-bold text-orange-700">Stok Kritis (Di Bawah 10)</h4>
                                <ul class="mt-2 list-disc list-inside">
                                    @forelse ($stokKritis as $bahan)
                                        <li>
                                            {{ $bahan->nama_bahan }}: 
                                            <span class="font-medium">{{ number_format($bahan->stok_terkini, 0, ',', '.') }} {{ $bahan->satuan }}</span>
                                        </li>
                                    @empty
                                        <p class="text-sm text-gray-500">Semua stok dalam batas aman.</p>
                                    @endforelse
                                </ul>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold">Pemakaian Bahan Terbanyak (7 Hari Terakhir)</h4>
                                <div>
                                    <canvas id="pemakaianChart" class="mt-4"></canvas>
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
                                pointRadius: 5
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
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
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
                                pointRadius: 4
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
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
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
                                    'rgba(54, 162, 235, 0.2)', // Biru
                                    'rgba(255, 99, 132, 0.2)', // Merah
                                    'rgba(75, 192, 192, 0.2)', // Hijau
                                    'rgba(255, 206, 86, 0.2)', // Kuning
                                    'rgba(153, 102, 255, 0.2)' // Ungu
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
                            scales: {
                                y: {
                                    beginAtZero: true
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