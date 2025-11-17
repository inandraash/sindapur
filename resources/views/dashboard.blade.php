<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Judul Halaman Dinamis --}}
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
                        {{-- ... (Kode dashboard Admin Anda) ... --}}

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


    @if (Auth::user()->role->nama_role == 'Staf Dapur')
    <script>
        // Pastikan script dieksekusi setelah DOM siap
        document.addEventListener('DOMContentLoaded', function () {
            
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
        });
    </script>
    @endif
</x-app-layout>