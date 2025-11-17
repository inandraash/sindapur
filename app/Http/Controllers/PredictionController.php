<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\PemakaianHarian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PredictionController extends Controller
{
    /**
     * Menampilkan halaman prediksi (rekomendasi belanja).
     */
    public function index()
    {
        // Tentukan periode (n) untuk SMA
        $periode_n = 7; 
        
        // Data yang digunakan: 7 hari terakhir, berakhir KEMARIN.
        // Data hari ini belum selesai, jadi kita prediksi berdasarkan data kemarin.
        $tanggalAkhir = Carbon::today()->subDay();
        $tanggalMulai = $tanggalAkhir->copy()->subDays($periode_n - 1);

        $rekomendasi = [];
        $bahanBakus = BahanBaku::orderBy('nama_bahan')->get();

        foreach ($bahanBakus as $bahan) {
            // Ambil data pemakaian 7 hari terakhir untuk bahan baku ini
            $pemakaianHistoris = PemakaianHarian::where('bahan_baku_id', $bahan->id)
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
                ->get();

            // Hitung total pemakaian selama n hari
            $totalPemakaian = $pemakaianHistoris->sum('jumlah_terpakai');
            
            // Hitung rata-rata (SMA)
            $prediksi = $totalPemakaian / $periode_n;

            $rekomendasi[] = [
                'nama_bahan' => $bahan->nama_bahan,
                'stok_terkini' => $bahan->stok_terkini,
                'satuan' => $bahan->satuan,
                'total_pemakaian_7_hari' => $totalPemakaian, // <-- Data baru untuk transparansi
                'prediksi_pembelian' => ceil($prediksi) // Dibulatkan ke atas
            ];
        }
        
        $tanggalPrediksi = Carbon::today()->translatedFormat('l, d F Y');

        return view('prediksi.index', compact(
            'rekomendasi', 
            'periode_n', 
            'tanggalMulai', 
            'tanggalAkhir', 
            'tanggalPrediksi'
        ));
    }
}