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
    public function index(Request $request)
    {
        $history_n = 7;
        $allowed_days = [1,3,7];
        $replenish_days = (int) $request->input('replenish_days', 1);
        if (!in_array($replenish_days, $allowed_days, true)) {
            $replenish_days = 1;
        }

        $search = trim((string) $request->input('search', ''));
        $allowedSort = ['nama_bahan', 'stok_terkini', 'stok_maksimum', 'total_pemakaian_history', 'prediksi_pembelian'];
        $sort_by = in_array($request->input('sort_by'), $allowedSort, true) ? $request->input('sort_by') : 'nama_bahan';
        $sort_dir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($request->has('use_max_stock')) {
            $use_max = filter_var($request->input('use_max_stock', false), FILTER_VALIDATE_BOOLEAN);
        } else {
            $use_max = true;
        }

        $tanggalAkhir = Carbon::today()->subDay();
        $tanggalMulai = $tanggalAkhir->copy()->subDays($history_n - 1);

        $pemakaianAgg = PemakaianHarian::selectRaw('bahan_baku_id, SUM(jumlah_terpakai) as total')
            ->whereBetween('tanggal', [$tanggalMulai->toDateString(), $tanggalAkhir->toDateString()])
            ->groupBy('bahan_baku_id')
            ->pluck('total', 'bahan_baku_id');

        $rekomendasi = [];
        $bahanBakus = BahanBaku::when($search !== '', function ($q) use ($search) {
                $q->where('nama_bahan', 'like', "%{$search}%");
            })
            ->orderBy('nama_bahan')
            ->get();

        foreach ($bahanBakus as $bahan) {
            $totalPemakaian = (float) ($pemakaianAgg[$bahan->id] ?? 0.0);

            //perhitungan rata-rata pemakaian harian
            $dailyAvg = $history_n > 0 ? ($totalPemakaian / $history_n) : 0;

            $requiredForPeriod = (int) ceil($dailyAvg * max(0, $replenish_days));

            $stokNow = (float) $bahan->stok_terkini;
            $buyQty = max(0, $requiredForPeriod - $stokNow);

            $stokMaks = $bahan->stok_maksimum ?? null;
            if ($use_max && $stokMaks !== null) {
                $maxCanBuy = max(0, $stokMaks - $stokNow);
                $buyQty = min($buyQty, $maxCanBuy);
            }

            $rekomendasi[] = [
                'nama_bahan' => $bahan->nama_bahan,
                'stok_terkini' => $stokNow,
                'satuan' => $bahan->satuan,
                'history_days' => $history_n,
                'total_pemakaian_history' => $totalPemakaian,
                'daily_average' => round($dailyAvg, 3),
                'replenish_days' => $replenish_days,
                'required_for_period' => $requiredForPeriod,
                'stok_maksimum' => $stokMaks,
                'prediksi_pembelian' => (int) ceil($buyQty),
            ];
        }

        usort($rekomendasi, function ($a, $b) use ($sort_by, $sort_dir) {
            $va = $a[$sort_by] ?? null;
            $vb = $b[$sort_by] ?? null;

            if (is_string($va) || is_string($vb)) {
                $cmp = strcasecmp((string) $va, (string) $vb);
            } else {
                $cmp = ($va <=> $vb);
            }

            return $sort_dir === 'asc' ? $cmp : -$cmp;
        });

        $tanggalPrediksi = Carbon::today()->translatedFormat('l, d F Y');

        return view('prediksi.index', compact(
            'rekomendasi',
            'history_n',
            'tanggalMulai',
            'tanggalAkhir',
            'tanggalPrediksi',
            'replenish_days',
            'use_max',
            'search',
            'sort_by',
            'sort_dir'
        ));
    }

    public function analisisAkurasi()
    {
        $bahanBaku = \App\Models\BahanBaku::all();
        
        $start_date = \Carbon\Carbon::parse('2025-12-08');
        $end_date   = \Carbon\Carbon::parse('2025-12-14');
        
        $laporanMingguan = [];

        foreach ($bahanBaku as $bahan) {
            $tempMapeBahan = 0;
            $detailHarian = [];
            $totalAktualMingguan = 0;
            
            $current = $start_date->copy();
            
            // Loop 7 Hari (Senin - Minggu)
            while ($current <= $end_date) {
                
                // 1. Data Aktual
                $dataAktual = \App\Models\PemakaianHarian::where('bahan_baku_id', $bahan->id)
                                ->whereDate('tanggal', $current)
                                ->first();
                $aktual = $dataAktual ? $dataAktual->jumlah_terpakai : 0;
                $totalAktualMingguan += $aktual;

                // 2. Forecast (Rata-rata 7 hari sebelumnya)
                $tgl_akhir_latih = $current->copy()->subDays(1);
                $tgl_mulai_latih = $current->copy()->subDays(7);
                
                $riwayat = \App\Models\PemakaianHarian::where('bahan_baku_id', $bahan->id)
                            ->whereBetween('tanggal', [$tgl_mulai_latih, $tgl_akhir_latih])
                            ->get();
                
                // Rumus SMA
                $forecast = ($riwayat->sum('jumlah_terpakai') > 0) ? ($riwayat->sum('jumlah_terpakai') / 7) : 0;

                // 3. Hitung Error Harian
                $error = abs($aktual - $forecast);
                $mapeHarian = ($aktual > 0) ? ($error / $aktual) * 100 : 0;

                $detailHarian[] = [
                    'hari'    => $current->translatedFormat('l'), // Nama Hari
                    'tgl'     => $current->format('d/m'),
                    'mape'    => $mapeHarian
                ];
                
                $tempMapeBahan += $mapeHarian;
                $current->addDay();
            }

            // Hitung Rata-rata MAPE Mingguan
            $avgMapeBahan = $tempMapeBahan / 7;

            if($totalAktualMingguan > 0 || $avgMapeBahan > 0) {
                $laporanMingguan[] = [
                    'nama_bahan'   => $bahan->nama_bahan,
                    'satuan'       => $bahan->satuan,
                    'detail_harian'=> $detailHarian,
                    'rata_mape'    => $avgMapeBahan
                ];
            }
        }

        return view('prediksi.analisis_mape', compact('laporanMingguan', 'start_date', 'end_date'));
    }
}