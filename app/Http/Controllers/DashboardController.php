<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BahanBaku;
use App\Models\PemakaianHarian;
use App\Models\TransaksiPenjualan;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard yang relevan berdasarkan role pengguna.
     */
    public function index(Request $request)
    {
        $role = Auth::user()->role->nama_role;
        $viewData = [];

        // ----------------- DATA UNTUK ADMIN -----------------
        if ($role == 'Admin') {
            // Ringkasan Finansial - Hari Ini
            $today = Carbon::today()->toDateString();
            $pendapatanHariIni = TransaksiPenjualan::with('menu')
                ->whereDate('tanggal_penjualan', $today)
                ->get()
                ->sum(function($transaksi) {
                    return $transaksi->jumlah_porsi * $transaksi->menu->harga;
                });

            // Ringkasan Finansial - Bulan Ini
            $bulanIni = Carbon::now()->startOfMonth();
            $bulanAkhir = Carbon::now()->endOfMonth();
            $pendapatanBulanIni = TransaksiPenjualan::with('menu')
                ->whereBetween('tanggal_penjualan', [$bulanIni, $bulanAkhir])
                ->get()
                ->sum(function($transaksi) {
                    return $transaksi->jumlah_porsi * $transaksi->menu->harga;
                });

            // Total Jumlah Transaksi (Bulan Ini)
            $jumlahTransaksiBulanIni = TransaksiPenjualan::whereBetween('tanggal_penjualan', [$bulanIni, $bulanAkhir])
                ->count();

            // Menu Paling Laris (Bulan Ini)
            $menuLaris = TransaksiPenjualan::with('menu')
                ->whereBetween('tanggal_penjualan', [$bulanIni, $bulanAkhir])
                ->select('menu_id', DB::raw('SUM(jumlah_porsi) as total_porsi'))
                ->groupBy('menu_id')
                ->orderBy('total_porsi', 'desc')
                ->limit(5)
                ->get();

            // Tren Pendapatan - 7 Hari Terakhir
            $hariIni = Carbon::today();
            $tujuhHariYangLalu = $hariIni->copy()->subDays(6);
            
            $trenPendapatan = [];
            $trenLabels = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = $hariIni->copy()->subDays($i)->toDateString();
                $pendapatan = TransaksiPenjualan::with('menu')
                    ->whereDate('tanggal_penjualan', $tanggal)
                    ->get()
                    ->sum(function($transaksi) {
                        return $transaksi->jumlah_porsi * $transaksi->menu->harga;
                    });
                
                $trenPendapatan[] = $pendapatan;
                $trenLabels[] = Carbon::parse($tanggal)->format('d M');
            }

            // Tren Pendapatan - 12 Bulan Terakhir
            $trenBulanan = [];
            $trenBulanLabels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $tanggalMulai = Carbon::now()->subMonths($i)->startOfMonth();
                $tanggalAkhir = Carbon::now()->subMonths($i)->endOfMonth();
                
                $pendapatan = TransaksiPenjualan::with('menu')
                    ->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir])
                    ->get()
                    ->sum(function($transaksi) {
                        return $transaksi->jumlah_porsi * $transaksi->menu->harga;
                    });
                
                $trenBulanan[] = $pendapatan;
                $trenBulanLabels[] = $tanggalMulai->format('M Y');
            }

            $viewData['pendapatanHariIni'] = $pendapatanHariIni;
            $viewData['pendapatanBulanIni'] = $pendapatanBulanIni;
            $viewData['jumlahTransaksiBulanIni'] = $jumlahTransaksiBulanIni;
            $viewData['menuLaris'] = $menuLaris;
            $viewData['trenPendapatanLabels'] = $trenLabels;
            $viewData['trenPendapatanData'] = $trenPendapatan;
            $viewData['trenBulanLabels'] = $trenBulanLabels;
            $viewData['trenBulanData'] = $trenBulanan;
        
        // ----------------- DATA UNTUK STAF DAPUR -----------------
        } elseif ($role == 'Staf Dapur') {
            $ambangBatasKritis = 10; 
            $viewData['stokKritis'] = BahanBaku::where('stok_terkini', '<=', $ambangBatasKritis)
                                        ->orderBy('stok_terkini', 'asc')
                                        ->limit(5)
                                        ->get();

            $tanggalMulai = Carbon::today()->subDays(6);
            $tanggalSelesai = Carbon::today();
            
            $dataGrafikQuery = PemakaianHarian::with('bahanBaku')
                                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
                                    ->select('bahan_baku_id', DB::raw('SUM(jumlah_terpakai) as total_pemakaian'))
                                    ->groupBy('bahan_baku_id')
                                    ->orderBy('total_pemakaian', 'desc')
                                    ->limit(5)
                                    ->get();
            
            $viewData['chartLabels'] = $dataGrafikQuery->map(fn($data) => $data->bahanBaku->nama_bahan);
            $viewData['chartData'] = $dataGrafikQuery->map(fn($data) => $data->total_pemakaian);
        }

        return view('dashboard', $viewData);
    }
}