<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PemakaianHarian;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiStok;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tentukan rentang tanggal
        // Default: 30 hari terakhir (misalnya)
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::today()->toDateString());
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::today()->subDays(29)->toDateString());
        
        // 2. Tentukan jenis laporan
        $jenisLaporan = $request->input('jenis_laporan', 'penjualan');

        $dataLaporan = [];
        $kolomTabel = []; // Untuk header tabel dinamis di view

        // 3. Ambil data berdasarkan jenis laporan
        if ($jenisLaporan == 'penjualan') {
            $kolomTabel = ['Nama Menu', 'Total Porsi Terjual'];
            // Ringkasan penjualan per menu
            $dataLaporan = TransaksiPenjualan::with('menu')
                ->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir])
                ->select('menu_id', DB::raw('SUM(jumlah_porsi) as total_porsi'))
                ->groupBy('menu_id')
                ->orderBy('total_porsi', 'desc')
                ->get();

        } elseif ($jenisLaporan == 'stok_masuk') {
            $kolomTabel = ['Nama Bahan Baku', 'Total Masuk', 'Satuan'];
            // Ringkasan stok masuk per bahan baku
            $dataLaporan = TransaksiStok::with('bahanBaku')
                ->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalAkhir])
                ->select('bahan_baku_id', DB::raw('SUM(jumlah_masuk) as total_masuk'))
                ->groupBy('bahan_baku_id')
                ->orderBy('total_masuk', 'desc')
                ->get();

        } elseif ($jenisLaporan == 'pemakaian') {
            $kolomTabel = ['Nama Bahan Baku', 'Total Pemakaian', 'Satuan'];
            // Ringkasan pemakaian per bahan baku (dari tabel summary)
            $dataLaporan = PemakaianHarian::with('bahanBaku')
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
                ->select('bahan_baku_id', DB::raw('SUM(jumlah_terpakai) as total_terpakai'))
                ->groupBy('bahan_baku_id')
                ->orderBy('total_terpakai', 'desc')
                ->get();
        }

        return view('admin.laporan.index', compact(
            'dataLaporan', 
            'jenisLaporan', 
            'tanggalMulai', 
            'tanggalAkhir',
            'kolomTabel'
        ));
    }
}