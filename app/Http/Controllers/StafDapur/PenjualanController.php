<?php

namespace App\Http\Controllers\StafDapur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\TransaksiPenjualan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    public function index()
    {
        $menus = Menu::where('harga', '>', 0)->orderBy('nama_menu')->get();
        return view('staf.penjualan.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penjualan' => ['required', 'array'],
            'penjualan.*' => ['nullable', 'numeric', 'min:0'],
            'tanggal_penjualan' => ['required', 'date'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $nasiRamesPolos = Menu::where('nama_menu', 'Nasi Rames Polos')->first();

                foreach ($request->penjualan as $menuId => $jumlahPorsi) {
                    if ($jumlahPorsi > 0) {
                        $menu = Menu::find($menuId);

                        TransaksiPenjualan::create([
                            'menu_id' => $menuId,
                            'jumlah_porsi' => $jumlahPorsi,
                            'tanggal_penjualan' => $request->tanggal_penjualan,
                            'user_id' => Auth::id(),
                        ]);

                        foreach ($menu->reseps as $resep) {
                            $totalPemakaian = $resep->jumlah_dibutuhkan * $jumlahPorsi;
                            $resep->bahanBaku()->decrement('stok_terkini', $totalPemakaian);
                        }

                        if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames Polos') {
                            foreach ($nasiRamesPolos->reseps as $resepDasar) {
                                $totalPemakaianDasar = $resepDasar->jumlah_dibutuhkan * $jumlahPorsi;
                                $resepDasar->bahanBaku()->decrement('stok_terkini', $totalPemakaianDasar);
                            }
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }

        return back()->with('success', 'Data penjualan berhasil disimpan dan stok telah diperbarui.');
    }
}
