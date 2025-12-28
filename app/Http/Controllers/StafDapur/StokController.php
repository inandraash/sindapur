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

        $search = trim((string) $request->input('search', ''));
        $allowedSort = ['nama_bahan', 'total_masuk', 'last_recorded'];
        $sort_by = in_array($request->input('sort_by'), $allowedSort, true) ? $request->input('sort_by') : 'last_recorded';
        $sort_dir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $orderColumn = $sort_by === 'nama_bahan' ? 'bahan_bakus.nama_bahan' : $sort_by;

        $stokMasukHarian = TransaksiStok::selectRaw(
                'transaksi_stoks.bahan_baku_id, '
                . 'SUM(transaksi_stoks.jumlah_masuk) as total_masuk, '
                . 'MAX(transaksi_stoks.created_at) as last_recorded, '
                . 'bahan_bakus.nama_bahan as nama_bahan, '
                . 'bahan_bakus.satuan as satuan'
            )
            ->join('bahan_bakus', 'transaksi_stoks.bahan_baku_id', '=', 'bahan_bakus.id')
            ->whereDate('tanggal_masuk', $selectedDate)
            ->when($search !== '', function ($q) use ($search) {
                $q->where('bahan_bakus.nama_bahan', 'like', "%{$search}%");
            })
            ->groupBy('transaksi_stoks.bahan_baku_id', 'bahan_bakus.nama_bahan', 'bahan_bakus.satuan')
            ->orderBy($orderColumn, $sort_dir)
            ->get();

        return view('staf.stok.index', compact('bahanBakus', 'selectedDate', 'stokMasukHarian', 'search', 'sort_by', 'sort_dir'));
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

    public function bulkStore(Request $request)
    {
        $rawItems = collect($request->input('items', []))
            ->filter(function ($item) {
                return filled($item['bahan_baku_id'] ?? null) || filled($item['jumlah_masuk'] ?? null);
            })
            ->values()
            ->all();

        $request->merge(['items' => $rawItems]);

        $data = $request->validateWithBag(
            'bulkStock',
            [
                'tanggal_masuk' => ['required', 'date'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.bahan_baku_id' => ['required', 'exists:bahan_bakus,id'],
                'items.*.jumlah_masuk' => ['required', 'numeric', 'min:0.01'],
            ],
            [
                'items.required' => 'Isi minimal satu baris stok masuk.',
                'items.*.bahan_baku_id.required' => 'Pilih bahan baku.',
                'items.*.jumlah_masuk.required' => 'Jumlah masuk wajib diisi.',
                'items.*.jumlah_masuk.min' => 'Jumlah masuk harus lebih dari 0.',
            ]
        );

        try {
            DB::transaction(function () use ($data) {
                foreach ($data['items'] as $item) {
                    $bahanBaku = BahanBaku::find($item['bahan_baku_id']);
                    $stokMaks = $bahanBaku->stok_maksimum;
                    $stokSekarang = $bahanBaku->stok_terkini;
                    $totalSesudah = $stokSekarang + $item['jumlah_masuk'];

                    if (!is_null($stokMaks) && $totalSesudah > $stokMaks) {
                        $tersisa = max(0, $stokMaks - $stokSekarang);
                        $pesan = "Stok untuk {$bahanBaku->nama_bahan} melebihi batas maksimum. Maks: "
                            . number_format($stokMaks, 2, ',', '.') . " {$bahanBaku->satuan}, tersisa ruang: "
                            . number_format($tersisa, 2, ',', '.') . " {$bahanBaku->satuan}.";
                        throw new \Exception($pesan);
                    }

                    TransaksiStok::create([
                        'bahan_baku_id' => $item['bahan_baku_id'],
                        'jumlah_masuk' => $item['jumlah_masuk'],
                        'tanggal_masuk' => $data['tanggal_masuk'],
                        'user_id' => Auth::id(),
                    ]);

                    $bahanBaku->increment('stok_terkini', $item['jumlah_masuk']);
                }
            });
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }

        return back()->with('success', 'Berhasil mencatat stok masuk untuk ' . count($data['items']) . ' bahan baku.');
    }
}
