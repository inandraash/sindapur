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

        $search = trim((string) $request->input('search', ''));
        $allowedSort = ['menu_name', 'total_porsi', 'last_recorded'];
        $sort_by = in_array($request->input('sort_by'), $allowedSort, true) ? $request->input('sort_by') : 'last_recorded';
        $sort_dir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $sort_by === 'menu_name' ? 'menus.nama_menu' : $sort_by;

        $penjualanHarian = TransaksiPenjualan::selectRaw(
                'transaksi_penjualans.menu_id, '
                . 'SUM(transaksi_penjualans.jumlah_porsi) as total_porsi, '
                . 'MAX(transaksi_penjualans.created_at) as last_recorded, '
                . 'menus.nama_menu as menu_name'
            )
            ->join('menus', 'transaksi_penjualans.menu_id', '=', 'menus.id')
            ->whereDate('tanggal_penjualan', $selectedDate)
            ->when($search !== '', function ($q) use ($search) {
                $q->where('menus.nama_menu', 'like', "%{$search}%");
            })
            ->groupBy('transaksi_penjualans.menu_id', 'menus.nama_menu')
            ->orderBy($orderColumn, $sort_dir)
            ->get();
        
        return view('staf.penjualan.index', compact('menus', 'selectedDate', 'penjualanHarian', 'search', 'sort_by', 'sort_dir'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan' => ['required', 'array'],
            'penjualan.*' => ['nullable', 'numeric', 'min:1'],
            'tanggal_penjualan' => ['required', 'date'],
        ], [
            'penjualan.*.min' => 'Jumlah porsi harus minimal 1. Jika tidak terjual, kosongkan saja.',
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
