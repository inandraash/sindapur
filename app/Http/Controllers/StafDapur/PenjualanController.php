<?php

namespace App\Http\Controllers\StafDapur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\PemakaianHarian;
use App\Models\TransaksiPenjualan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $menus = Menu::where('harga', '>', 0)->orderBy('nama_menu')->get();
        $selectedDate = $request->input('tanggal', Carbon::today()->toDateString());
        $penjualanHarian = TransaksiPenjualan::with(['menu', 'user'])
            ->whereDate('tanggal_penjualan', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('staf.penjualan.index', compact('menus', 'selectedDate', 'penjualanHarian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan' => ['required', 'array'],
            'penjualan.*' => ['nullable', 'numeric', 'min:0'],
            'tanggal_penjualan' => ['required', 'date'],
        ]);

        // Array sementara untuk mengagregasi total pemakaian per bahan baku
        $pemakaianAgregat = [];
        $nasiRamesDasar = Menu::where('nama_menu', 'Nasi Rames')->first();

        if (!$nasiRamesDasar) {
            return back()->with('error', 'Kesalahan Kritis: Resep dasar "Nasi Rames" tidak ditemukan. Hubungi Admin.');
        }

        try {
            DB::transaction(function () use ($request, &$pemakaianAgregat, $nasiRamesDasar) {
                
                foreach ($request->penjualan as $menuId => $jumlahPorsi) {
                    if ($jumlahPorsi > 0) {
                        $menu = Menu::find($menuId);
                        if (!$menu) continue; 

                        // 1. Catat transaksi penjualan (untuk rekap staf)
                        TransaksiPenjualan::create([
                            'menu_id' => $menuId,
                            'jumlah_porsi' => $jumlahPorsi,
                            'tanggal_penjualan' => $request->tanggal_penjualan,
                            'user_id' => Auth::id(),
                        ]);

                        // 2. Agregasi pemakaian bahan dari resep menu itu sendiri (misal: telur, daging)
                        foreach ($menu->reseps as $resep) {
                            $totalPemakaian = $resep->jumlah_dibutuhkan * $jumlahPorsi;
                            $pemakaianAgregat[$resep->bahan_baku_id] = ($pemakaianAgregat[$resep->bahan_baku_id] ?? 0) + $totalPemakaian;
                        }

                        // 3. LOGIKA KHUSUS: Agregasi pemakaian bahan dasar Nasi Rames
                        if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames') {
                            foreach ($nasiRamesDasar->reseps as $resepDasar) {
                                $totalPemakaianDasar = $resepDasar->jumlah_dibutuhkan * $jumlahPorsi;
                                $pemakaianAgregat[$resepDasar->bahan_baku_id] = ($pemakaianAgregat[$resepDasar->bahan_baku_id] ?? 0) + $totalPemakaianDasar;
                            }
                        }
                    }
                } // Akhir loop penjualan

                // 4. Loop melalui data agregat untuk mengurangi stok & mencatat histori
                foreach ($pemakaianAgregat as $bahanId => $totalTerpakai) {
                    if ($totalTerpakai > 0) {
                        // TUGAS 1: Mengurangi Saldo
                        BahanBaku::find($bahanId)->decrement('stok_terkini', $totalTerpakai);

                        // TUGAS 2: Mencatat Riwayat Pengeluaran (untuk SMA)
                        PemakaianHarian::create([
                            'bahan_baku_id' => $bahanId,
                            'jumlah_terpakai' => $totalTerpakai,
                            'tanggal' => $request->tanggal_penjualan
                        ]);
                    }
                }
            }); // Akhir transaction
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }

        return back()->with('success', 'Data penjualan berhasil disimpan dan stok telah diperbarui.');
    }

    public function showTodaySales()
    {

        $today = Carbon::today()->toDateString();

        // Ambil data penjualan hari ini, beserta nama menu
        $penjualanHariIni = TransaksiPenjualan::with('menu')
            ->whereDate('tanggal_penjualan', $today)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('staf.penjualan.today', compact('penjualanHariIni', 'today'));
    }
}
