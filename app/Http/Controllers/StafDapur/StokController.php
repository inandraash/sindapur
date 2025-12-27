<?php

namespace App\Http\Controllers\StafDapur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\TransaksiStok;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $bahanBakus = BahanBaku::orderBy('nama_bahan')->get();
        $selectedDate = $request->input('tanggal', Carbon::today()->toDateString());
        $stokMasukHarian = TransaksiStok::with(['bahanBaku', 'user'])
            ->whereDate('tanggal_masuk', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staf.stok.index', compact('bahanBakus', 'selectedDate', 'stokMasukHarian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bahan_baku_id' => ['required', 'exists:bahan_bakus,id'],
            'jumlah_masuk' => ['required', 'numeric', 'min:0.01'],
            'tanggal_masuk' => ['required', 'date'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $bahanBaku = BahanBaku::find($request->bahan_baku_id);
                $stokMaks = $bahanBaku->stok_maksimum;
                $stokSekarang = $bahanBaku->stok_terkini;
                $totalSesudah = $stokSekarang + $request->jumlah_masuk;

                if (!is_null($stokMaks) && $totalSesudah > $stokMaks) {
                    $tersisa = max(0, $stokMaks - $stokSekarang);
                    $pesan = "Stok melebihi batas maksimum. Maks: " . number_format($stokMaks, 2, ',', '.') .
                             " {$bahanBaku->satuan}, tersisa ruang: " . number_format($tersisa, 2, ',', '.') . " {$bahanBaku->satuan}.";
                    throw new \Exception($pesan);
                }

                TransaksiStok::create([
                    'bahan_baku_id' => $request->bahan_baku_id,
                    'jumlah_masuk' => $request->jumlah_masuk,
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'user_id' => Auth::id(),
                ]);

                $bahanBaku->increment('stok_terkini', $request->jumlah_masuk);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return back()->with('success', 'Data stok masuk berhasil dicatat.');
    }
}
