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
        $menus = Menu::where('harga', '>', 0)->with('reseps.bahanBaku')->orderBy('nama_menu')->get();
        $selectedDate = $request->input('tanggal', Carbon::today()->toDateString());
        
        $penjualanHarian = TransaksiPenjualan::selectRaw('menu_id, SUM(jumlah_porsi) as total_porsi, MAX(created_at) as last_recorded')
            ->whereDate('tanggal_penjualan', $selectedDate)
            ->groupBy('menu_id')
            ->with('menu')
            ->orderBy('last_recorded', 'desc')
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

        $pemakaianAgregat = [];
        $nasiRamesDasar = Menu::where('nama_menu', 'Nasi Rames')->first();

        if (!$nasiRamesDasar) {
            return back()->with('error', 'Kesalahan Kritis: Resep dasar "Nasi Rames" tidak ditemukan. Hubungi Admin.');
        }

        try {
            DB::transaction(function () use ($request, &$pemakaianAgregat, $nasiRamesDasar) {
                
                foreach ($request->penjualan as $menuId => $jumlahPorsi) {
                    if ($jumlahPorsi > 0) {
                        $menu = Menu::with('reseps.bahanBaku')->find($menuId);
                        if (!$menu) continue; 

                        TransaksiPenjualan::create([
                            'menu_id' => $menuId,
                            'jumlah_porsi' => $jumlahPorsi,
                            'tanggal_penjualan' => $request->tanggal_penjualan,
                            'user_id' => Auth::id(),
                        ]);

                        foreach ($menu->reseps as $resep) {
                            $totalPemakaian = $resep->jumlah_dibutuhkan * $jumlahPorsi;
                            $pemakaianAgregat[$resep->bahan_baku_id] = ($pemakaianAgregat[$resep->bahan_baku_id] ?? 0) + $totalPemakaian;
                        }

                        if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames') {
                            foreach ($nasiRamesDasar->reseps as $resepDasar) {
                                $totalPemakaianDasar = $resepDasar->jumlah_dibutuhkan * $jumlahPorsi;
                                $pemakaianAgregat[$resepDasar->bahan_baku_id] = ($pemakaianAgregat[$resepDasar->bahan_baku_id] ?? 0) + $totalPemakaianDasar;
                            }
                        }
                    }
                }

                $bahanKurang = [];
                $bahanHabis = [];
                foreach ($pemakaianAgregat as $bahanId => $totalTerpakai) {
                    $bahan = BahanBaku::find($bahanId);
                    if (!$bahan) continue;
                    
                    if ($bahan->stok_terkini <= 0) {
                        $bahanHabis[] = $bahan->nama_bahan;
                    }
                    elseif ($bahan->stok_terkini < $totalTerpakai) {
                        $bahanKurang[] = [
                            'nama' => $bahan->nama_bahan,
                            'stok_tersedia' => $bahan->stok_terkini,
                            'stok_dibutuhkan' => $totalTerpakai,
                            'satuan' => $bahan->satuan
                        ];
                    }
                }

                if (!empty($bahanHabis)) {
                    $pesan = 'Bahan baku berikut sudah habis (stok = 0) dan tidak dapat digunakan:\n';
                    $pesan .= '- ' . implode("\n- ", $bahanHabis);
                    throw new \Exception($pesan);
                }

                if (!empty($bahanKurang)) {
                    $pesan = 'Stok bahan baku tidak cukup:\n';
                    foreach ($bahanKurang as $bahan) {
                        $pesan .= "- {$bahan['nama']}: Tersedia {$bahan['stok_tersedia']} {$bahan['satuan']}, dibutuhkan {$bahan['stok_dibutuhkan']} {$bahan['satuan']}\n";
                    }
                    throw new \Exception(trim($pesan));
                }

                foreach ($pemakaianAgregat as $bahanId => $totalTerpakai) {
                    if ($totalTerpakai > 0) {
                        BahanBaku::find($bahanId)->decrement('stok_terkini', $totalTerpakai);

                        PemakaianHarian::create([
                            'bahan_baku_id' => $bahanId,
                            'jumlah_terpakai' => $totalTerpakai,
                            'tanggal' => $request->tanggal_penjualan
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }

        return back()->with('success', 'Data penjualan berhasil disimpan dan stok telah diperbarui.');
    }

    public function showTodaySales()
    {

        $today = Carbon::today()->toDateString();

        $penjualanHariIni = TransaksiPenjualan::with('menu')
            ->whereDate('tanggal_penjualan', $today)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('staf.penjualan.today', compact('penjualanHariIni', 'today'));
    }
}
