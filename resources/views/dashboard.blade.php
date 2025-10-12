<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold">Ringkasan Finansial (7 Hari Terakhir)</h4>
                                <p>Total Pembelian: Rp ...</p>
                                <p>Estimasi Pendapatan: Rp ...</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold">Bahan Paling Sering Terbuang</h4>
                                <p>1. Bawang Merah</p>
                                <p>2. Cabai Rawit</p>
                            </div>

                            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                                 <h4 class="font-bold">Grafik Tren Pendapatan Harian</h4>
                                 </div>
                        </div>

                    @elseif ($role == 'Staf Dapur')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg">
                                <h4 class="font-bold text-orange-700">Stok Kritis</h4>
                                <p>Daging Sapi: 5 kg</p>
                                <p>Ayam Paha: 3 kg</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold">Grafik Pemakaian Bahan Kunci (7 Hari Terakhir)</h4>
                                </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>