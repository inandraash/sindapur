<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\BahanBaku;
use App\Models\TransaksiPenjualan;
use App\Models\PemakaianHarian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::whereHas('role', function($q) {
            $q->where('nama_role', 'Staf Dapur');
        })->first() ?? User::first();

        $nasiRamesDasar = Menu::where('nama_menu', 'Nasi Rames')->first();

        $endDate = Carbon::yesterday();
        $startDate = $endDate->copy()->subDays(6);

        $menus = Menu::where('harga', '>', 0)->get();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            
            $this->command->info("Memproses penjualan tanggal: " . $date->format('Y-m-d'));
            
            $pemakaianAgregat = [];

            foreach ($menus as $menu) {
                $requestedPorsi = rand(5, 25);

                $maxPossible = PHP_INT_MAX;

                foreach ($menu->reseps as $resep) {
                    $bahan = BahanBaku::find($resep->bahan_baku_id);
                    if (!$bahan) continue;

                    $need = floatval($resep->jumlah_dibutuhkan ?: 0);
                    if ($need <= 0) continue;

                    $possible = floor(floatval($bahan->stok_terkini) / $need);
                    $maxPossible = min($maxPossible, max(0, $possible));
                }

                if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames' && $nasiRamesDasar) {
                    foreach ($nasiRamesDasar->reseps as $resepDasar) {
                        $bahan = BahanBaku::find($resepDasar->bahan_baku_id);
                        if (!$bahan) continue;

                        $need = floatval($resepDasar->jumlah_dibutuhkan ?: 0);
                        if ($need <= 0) continue;

                        $possible = floor(floatval($bahan->stok_terkini) / $need);
                        $maxPossible = min($maxPossible, max(0, $possible));
                    }
                }

                if ($maxPossible === PHP_INT_MAX) {
                    continue;
                }

                if ($maxPossible <= 0) {
                    continue;
                }

                $jumlahPorsi = min($requestedPorsi, (int)$maxPossible);

                if ($jumlahPorsi <= 0) {
                    continue;
                }

                TransaksiPenjualan::create([
                    'menu_id' => $menu->id,
                    'jumlah_porsi' => $jumlahPorsi,
                    'tanggal_penjualan' => $date->format('Y-m-d'),
                    'user_id' => $user->id,
                    'created_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                    'updated_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                ]);

                foreach ($menu->reseps as $resep) {
                    $total = $resep->jumlah_dibutuhkan * $jumlahPorsi;
                    $pemakaianAgregat[$resep->bahan_baku_id] = ($pemakaianAgregat[$resep->bahan_baku_id] ?? 0) + $total;

                    $b = BahanBaku::find($resep->bahan_baku_id);
                    if ($b) {
                        $b->decrement('stok_terkini', $total);
                    }
                }

                if (str_contains($menu->nama_menu, 'Nasi Rames') && $menu->nama_menu != 'Nasi Rames') {
                    if ($nasiRamesDasar) {
                        foreach ($nasiRamesDasar->reseps as $resepDasar) {
                            $totalDasar = $resepDasar->jumlah_dibutuhkan * $jumlahPorsi;
                            $pemakaianAgregat[$resepDasar->bahan_baku_id] = ($pemakaianAgregat[$resepDasar->bahan_baku_id] ?? 0) + $totalDasar;

                            $b2 = BahanBaku::find($resepDasar->bahan_baku_id);
                            if ($b2) {
                                $b2->decrement('stok_terkini', $totalDasar);
                            }
                        }
                    }
                }
            }

            foreach ($pemakaianAgregat as $bahanId => $totalTerpakai) {
                if ($totalTerpakai <= 0) continue;

                PemakaianHarian::create([
                    'bahan_baku_id' => $bahanId,
                    'jumlah_terpakai' => $totalTerpakai,
                    'tanggal' => $date->format('Y-m-d'),
                    'created_at' => $date->copy()->setTime(21, 0), 
                    'updated_at' => $date->copy()->setTime(21, 0),
                ]);
            }
        }
    }
}