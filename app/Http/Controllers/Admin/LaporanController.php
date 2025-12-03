<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PemakaianHarian;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiStok;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function download(Request $request)
    {
        // 1. Ambil filter (sama seperti method index)
        $tanggalAkhir = $request->input('tanggal_akhir', Carbon::today()->toDateString());
        $tanggalMulai = $request->input('tanggal_mulai', Carbon::today()->toDateString()); // Default hari ini
        $jenisLaporan = $request->input('jenis_laporan', 'penjualan');

        $dataLaporan = [];
        // Variabel judul untuk PDF
        $judulLaporan = ''; 

        // 2. Ambil data (sama seperti method index)
        if ($jenisLaporan == 'penjualan') {
            $judulLaporan = 'Laporan Transaksi Penjualan';
            $dataLaporan = TransaksiPenjualan::with(['menu', 'user'])
                ->whereBetween('tanggal_penjualan', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('tanggal_penjualan', 'asc') // Urutkan dari yang terlama
                ->get();

        } elseif ($jenisLaporan == 'stok_masuk') {
            $judulLaporan = 'Laporan Transaksi Stok Masuk';
            $dataLaporan = TransaksiStok::with(['bahanBaku', 'user'])
                ->whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('tanggal_masuk', 'asc')
                ->get();

        } elseif ($jenisLaporan == 'pemakaian') {
            $judulLaporan = 'Laporan Transaksi Pemakaian Bahan';
            $dataLaporan = PemakaianHarian::with('bahanBaku')
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        // 3. Render PDF
        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'dataLaporan', 
            'jenisLaporan', 
            'tanggalMulai', 
            'tanggalAkhir',
            'judulLaporan'
        ));

        // Set ukuran kertas dan orientasi (opsional)
        $pdf->setPaper('A4', 'portrait');

        // 4. Download file
        // Nama file: laporan-penjualan-2025-11-24.pdf
        $fileName = 'laporan-' . $jenisLaporan . '-' . Carbon::now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($fileName);
    }
}