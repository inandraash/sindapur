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
        $bahanBakus = BahanBaku::orderBy('nama_bahan')->get();

        foreach ($bahanBakus as $bahan) {
            $totalPemakaian = (float) ($pemakaianAgg[$bahan->id] ?? 0.0);

            // rata-rata diasumsikan termasuk hari tanpa data (0)
            $dailyAvg = $history_n > 0 ? ($totalPemakaian / $history_n) : 0;

            $requiredForPeriod = (int) ceil($dailyAvg * max(0, $replenish_days));

            $stokNow = (float) $bahan->stok_terkini;
            $buyQty = max(0, $requiredForPeriod - $stokNow);

            $stokMaks = $bahan->stok_maksimum ?? null;
            if ($use_max && $stokMaks !== null) {
                $maxCanBuy = max(0, $stokMaks - $stokNow);
                $buyQty = min($buyQty, $maxCanBuy);
            }

            // contoh kebijakan opsional: minimal order 1 jika ada pemakaian historis tapi purchase = 0
            // if ($totalPemakaian > 0 && $buyQty <= 0 && $stokNow < ($stokMaks ?? INF)) {
            //     $buyQty = 1;
            // }

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

        $tanggalPrediksi = Carbon::today()->translatedFormat('l, d F Y');

        return view('prediksi.index', compact(
            'rekomendasi',
            'history_n',
            'tanggalMulai',
            'tanggalAkhir',
            'tanggalPrediksi',
            'replenish_days',
            'use_max'
        ));
    }
}